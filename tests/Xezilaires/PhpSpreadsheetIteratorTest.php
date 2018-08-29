<?php

declare(strict_types=1);

/*
 * This file is part of the xezilaires project.
 *
 * (c) Dalibor Karlović <dalibor@flexolabs.io>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xezilaires\Test;

use PHPUnit\Framework\TestCase;
use Xezilaires\Metadata\Annotation\AnnotationDriver;
use Xezilaires\Metadata\ArrayReference;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Mapping;
use Xezilaires\PhpSpreadsheetIterator;
use Xezilaires\Test\Model\Product;

/**
 * Class PhpSpreadsheetIteratorTest.
 *
 * @covers \Xezilaires\PhpSpreadsheetIterator
 *
 * @uses \Xezilaires\Infrastructure\PhpSpreadsheet\RowIterator
 * @uses \Xezilaires\Metadata\ArrayReference
 * @uses \Xezilaires\Metadata\ColumnReference
 * @uses \Xezilaires\Metadata\HeaderReference
 * @uses \Xezilaires\Metadata\Mapping
 */
class PhpSpreadsheetIteratorTest extends TestCase
{
    use IteratorMatcherTrait;

    public function testCanLoadFlatFixtureWithColumnReference(): void
    {
        $iterator = new PhpSpreadsheetIterator(
            $this->fixture('products.xls'),
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
            ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
        ], $iterator);
    }

    public function testCanLoadFlatFixtureWithHeaderReference(): void
    {
        $iterator = new PhpSpreadsheetIterator(
            $this->fixture('products.xls'),
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
            ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
        ], $iterator);
    }

    public function testCanLoadFlatFixtureWithArrayReference(): void
    {
        $iterator = new PhpSpreadsheetIterator(
            $this->fixture('products.xls'),
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
            ['all' => ['The Very Hungry Caterpillar', '6.59']],
            ['all' => ['Brown Bear, Brown Bear, What Do You See?', '6.51']],
        ], $iterator);
    }

    public function testCanLoadSparseFixtureWithHeaderReference(): void
    {
        $iterator = new PhpSpreadsheetIterator(
            $this->fixture('products-sparse.xls'),
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
            ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
        ], $iterator);
    }

    /**
     * @uses \Xezilaires\Metadata\Annotation\AnnotationDriver
     *
     * @throws \RuntimeException
     */
    public function testCanLoadSparseFixtureWithAnnotations(): void
    {
        $driver = new AnnotationDriver();
        $mapping = $driver->getMetadataMapping(Product::class);

        $iterator = new PhpSpreadsheetIterator(
            $this->fixture('products-sparse.xls'),
            $mapping
        );

        self::assertIteratorMatches([
            ['all' => ['The Very Hungry Caterpillar', '6.59'], 'name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            ['all' => ['Brown Bear, Brown Bear, What Do You See?', '6.51'], 'name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
        ], $iterator);
    }

    public function testCannotLoadFixtureWithDuplicateHeaderReference(): void
    {
        $this->expectException(\Xezilaires\Exception\MappingException::class);
        $this->expectExceptionMessage('Duplicate header "Name"');

        $iterator = new PhpSpreadsheetIterator(
            $this->fixture('products-duplicate-header.xls'),
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

        iterator_to_array($iterator);
    }

    public function testCannotLoadFixtureWithInvalidHeaderReference(): void
    {
        $this->expectException(\Xezilaires\Exception\MappingException::class);
        $this->expectExceptionMessage('Invalid header "Nameeee"');

        $iterator = new PhpSpreadsheetIterator(
            $this->fixture('products.xls'),
            new Mapping(
                Product::class,
                [
                    'price' => new HeaderReference('Price USD'),
                    'name' => new HeaderReference('Nameeee'),
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
        $this->expectException(\Xezilaires\Exception\MappingException::class);
        $this->expectExceptionMessage('Unexpected reference type');

        $iterator = new PhpSpreadsheetIterator(
            $this->fixture('products.xls'),
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
        $this->expectException(\Xezilaires\Exception\SpreadsheetException::class);
        $this->expectExceptionMessage('No spreadsheet path given');

        $iterator = new PhpSpreadsheetIterator(
            $this->invalidFixture('products.xls'),
            new Mapping(Product::class, ['name' => new ColumnReference('A')])
        );

        iterator_to_array($iterator);
    }

    public function testCanFetchCurrentIteratorItem(): void
    {
        $iterator = new PhpSpreadsheetIterator(
            $this->fixture('products.xls'),
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
        static::assertEquals(1, $iterator->key());

        $current = new Product();
        $current->name = 'Brown Bear, Brown Bear, What Do You See?';
        $current->price = '6.51';
        static::assertEquals($current, $iterator->current());
    }

    public function testCanRewindIterator(): void
    {
        $iterator = new PhpSpreadsheetIterator(
            $this->fixture('products.xls'),
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
        static::assertEquals(1, $iterator->key());

        $iterator->rewind();
        $current = new Product();
        $current->name = 'The Very Hungry Caterpillar';
        $current->price = '6.59';
        static::assertEquals($current, $iterator->current());
        static::assertEquals(0, $iterator->key());
    }

    /**
     * @param string $name
     *
     * @return \SplFileObject
     */
    private function fixture(string $name): \SplFileObject
    {
        return new \SplFileObject(__DIR__.'/../../resources/fixtures/'.$name);
    }

    /**
     * @param string $name
     *
     * @return \SplFileObject
     */
    private function invalidFixture(string $name): \SplFileObject
    {
        return new class(__DIR__.'/../../resources/fixtures/'.$name) extends \SplFileObject {
            /**
             * @return bool
             * @psalm-suppress ImplementedReturnTypeMismatch
             */
            public function getRealPath(): bool
            {
                return false;
            }
        };
    }
}
