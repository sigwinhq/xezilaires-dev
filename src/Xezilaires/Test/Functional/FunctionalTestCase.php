<?php

declare(strict_types=1);

/*
 * This file is part of the xezilaires project.
 *
 * (c) sigwin.hr
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xezilaires\Test\Functional;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Xezilaires\Exception\MappingException;
use Xezilaires\Exception\SpreadsheetException;
use Xezilaires\Metadata\Annotation\AnnotationDriver;
use Xezilaires\Metadata\ArrayReference;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Mapping;
use Xezilaires\Serializer\ObjectSerializer;
use Xezilaires\Spreadsheet;
use Xezilaires\SpreadsheetIterator;
use Xezilaires\Test\FixtureTrait;
use Xezilaires\Test\IteratorMatcherTrait;
use Xezilaires\Test\Model\Product;

/**
 * @internal
 */
abstract class FunctionalTestCase extends TestCase
{
    use FixtureTrait;
    use IteratorMatcherTrait;

    public function testCanLoadFlatFixtureWithColumnReference(): void
    {
        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products.xlsx')),
            new Mapping(
                Product::class,
                [
                    'name' => new ColumnReference('A'),
                    'price' => new ColumnReference('B'),
                ],
                [
                    'start' => 2,
                ]
            )
        );

        self::assertIteratorMatches([
            2 => ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            3 => ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
            4 => ['name' => 'Stillhouse Lake', 'price' => '1.99'],
        ], $iterator);
    }

    public function testCanLoadFlatFixtureWithHeaderReference(): void
    {
        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products.xlsx')),
            new Mapping(
                Product::class,
                [
                    'price' => new HeaderReference('Price USD'),
                    'name' => new HeaderReference('Name'),
                ],
                [
                    'header' => 1,
                    'start' => 2,
                ]
            )
        );

        self::assertIteratorMatches([
            2 => ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            3 => ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
            4 => ['name' => 'Stillhouse Lake', 'price' => '1.99'],
        ], $iterator);
    }

    public function testCanLoadFlatFixtureWithArrayReference(): void
    {
        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products.xlsx')),
            new Mapping(
                Product::class,
                [
                    'all' => new ArrayReference([
                        new HeaderReference('Name'),
                        new HeaderReference('Price USD'),
                    ]),
                ],
                [
                    'header' => 1,
                    'start' => 2,
                ]
            )
        );

        self::assertIteratorMatches([
            2 => ['all' => ['The Very Hungry Caterpillar', '6.59']],
            3 => ['all' => ['Brown Bear, Brown Bear, What Do You See?', '6.51']],
            4 => ['all' => ['Stillhouse Lake', '1.99']],
        ], $iterator);
    }

    public function testCanLoadSparseFixtureWithHeaderReference(): void
    {
        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products-sparse.xlsx')),
            new Mapping(
                Product::class,
                [
                    'price' => new HeaderReference('Price USD'),
                    'name' => new HeaderReference('Name'),
                ],
                [
                    'header' => 1,
                    'start' => 2,
                ]
            )
        );

        self::assertIteratorMatches([
            2 => ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            3 => ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
            4 => ['name' => 'Stillhouse Lake', 'price' => '1.99'],
        ], $iterator);
    }

    /**
     * @uses \Xezilaires\Metadata\Annotation\AnnotationDriver
     *
     * @throws \ReflectionException
     * @throws \RuntimeException
     * @throws \ReflectionException
     */
    public function testCanLoadSparseFixtureWithAnnotations(): void
    {
        $driver = new AnnotationDriver();
        $mapping = $driver->getMetadataMapping(Product::class);

        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products-sparse.xlsx')),
            $mapping
        );

        self::assertIteratorMatches([
            2 => ['all' => ['The Very Hungry Caterpillar', '6.59'], 'name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            3 => ['all' => ['Brown Bear, Brown Bear, What Do You See?', '6.51'], 'name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
            4 => ['all' => ['Stillhouse Lake', '1.99'], 'name' => 'Stillhouse Lake', 'price' => '1.99'],
        ], $iterator);
    }

    public function testCannotLoadFixtureWithAmbiguousHeaderReference(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('Ambiguous header "Duplicate"');

        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products.xlsx')),
            new Mapping(
                Product::class,
                [
                    'price' => new HeaderReference('Duplicate'),
                ],
                [
                    'header' => 1,
                    'start' => 2,
                ]
            )
        );

        iterator_to_array($iterator);
    }

    public function testCannotLoadFixtureWithInvalidHeaderReference(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('Invalid header "No such name"');

        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products.xlsx')),
            new Mapping(
                Product::class,
                [
                    'price' => new HeaderReference('Price USD'),
                    'name' => new HeaderReference('No such name'),
                ],
                [
                    'header' => 1,
                    'start' => 2,
                ]
            )
        );

        iterator_to_array($iterator);
    }

    public function testCannotLoadFlatFixtureWithNestedArrayReference(): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('Unexpected reference type');

        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products.xlsx')),
            new Mapping(
                Product::class,
                [
                    'all' => new ArrayReference([
                        new ArrayReference([
                            new HeaderReference('Name'),
                            new HeaderReference('Price USD'),
                        ]),
                    ]),
                ],
                [
                    'header' => 1,
                    'start' => 2,
                ]
            )
        );

        iterator_to_array($iterator);
    }

    public function testCannotLoadUnreachableFile(): void
    {
        $this->expectException(SpreadsheetException::class);
        $this->expectExceptionMessage('No spreadsheet path given');

        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->invalidFixture('products.xlsx')),
            new Mapping(Product::class, ['name' => new ColumnReference('A')])
        );

        iterator_to_array($iterator);
    }

    public function testCanFetchCurrentIteratorItem(): void
    {
        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products.xlsx')),
            new Mapping(
                Product::class,
                [
                    'name' => new ColumnReference('A'),
                    'price' => new ColumnReference('B'),
                ],
                [
                    'start' => 2,
                ]
            )
        );

        $iterator->seek(1);
        static::assertSame(3, $iterator->key());

        $current = new Product();
        $current->name = 'Brown Bear, Brown Bear, What Do You See?';
        $current->price = '6.51';
        static::assertEquals($current, $iterator->current());
    }

    public function testCanRewindIterator(): void
    {
        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products.xlsx')),
            new Mapping(
                Product::class,
                [
                    'name' => new ColumnReference('A'),
                    'price' => new ColumnReference('B'),
                ],
                [
                    'start' => 2,
                ]
            )
        );

        $iterator->seek(1);
        static::assertSame(3, $iterator->key());

        $iterator->rewind();
        $current = new Product();
        $current->name = 'The Very Hungry Caterpillar';
        $current->price = '6.59';
        static::assertEquals($current, $iterator->current());
        static::assertEquals(2, $iterator->key());
    }

    /**
     * @uses \Xezilaires\ReverseIterator
     */
    public function testCanLoadFlatFixtureInReverse(): void
    {
        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products.xlsx')),
            new Mapping(
                Product::class,
                [
                    'name' => new ColumnReference('A'),
                    'price' => new ColumnReference('B'),
                ],
                [
                    'start' => 2,
                    'reverse' => true,
                ]
            )
        );

        self::assertIteratorMatches([
            ['name' => 'Stillhouse Lake', 'price' => '1.99'],
            ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
            ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
        ], $iterator);
    }

    public function testCanLoadIteratorSequentially(): void
    {
        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products.xlsx')),
            new Mapping(
                Product::class,
                [
                    'name' => new ColumnReference('A'),
                    'price' => new ColumnReference('B'),
                ],
                [
                    'start' => 2,
                    'sequential' => true,
                ]
            )
        );

        self::assertIteratorMatches([
            ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
            ['name' => 'Stillhouse Lake', 'price' => '1.99'],
        ], $iterator);
    }

    /**
     * @uses \Xezilaires\ReverseIterator
     */
    public function testCanLoadReverseIteratorSequentially(): void
    {
        $iterator = $this->createIterator(
            $this->getSpreadsheet($this->fixture('products.xlsx')),
            new Mapping(
                Product::class,
                [
                    'name' => new ColumnReference('A'),
                    'price' => new ColumnReference('B'),
                ],
                [
                    'start' => 2,
                    'reverse' => true,
                    'sequential' => true,
                ]
            )
        );

        self::assertIteratorMatches([
            ['name' => 'Stillhouse Lake', 'price' => '1.99'],
            ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
            ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
        ], $iterator);
    }

    abstract protected function getSpreadsheet(\SplFileObject $file): Spreadsheet;

    private function createIterator(Spreadsheet $spreadsheet, Mapping $mapping): SpreadsheetIterator
    {
        $serializer = new ObjectSerializer(new Serializer([new ObjectNormalizer()]));

        return new SpreadsheetIterator($spreadsheet, $mapping, $serializer);
    }
}
