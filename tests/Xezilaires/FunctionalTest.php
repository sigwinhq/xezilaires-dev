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
use Xezilaires\Metadata\ArrayReference;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Mapping;
use Xezilaires\PhpSpreadsheetIterator;
use Xezilaires\Test\Model\Product;

/**
 * Class FunctionalTest.
 */
class FunctionalTest extends TestCase
{
    /**
     * @coversNothing
     */
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

        $this->assertIteratorMatches([
            ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
        ], $iterator);
    }

    /**
     * @coversNothing
     */
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

        $this->assertIteratorMatches([
            ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
        ], $iterator);
    }

    /**
     * @coversNothing
     */
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

        $this->assertIteratorMatches([
            ['all' => ['The Very Hungry Caterpillar', '6.59']],
            ['all' => ['Brown Bear, Brown Bear, What Do You See?', '6.51']],
        ], $iterator);
    }

    /**
     * @coversNothing
     */
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

        $this->assertIteratorMatches([
            ['name' => 'The Very Hungry Caterpillar', 'price' => '6.59'],
            ['name' => 'Brown Bear, Brown Bear, What Do You See?', 'price' => '6.51'],
        ], $iterator);
    }

    /**
     * @coversNothing
     */
    public function testCannotLoadFixtureWithDuplicateHeaderReference(): void
    {
        $this->expectException(\Xezilaires\Exception\HeaderException::class);
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

    /**
     * @coversNothing
     */
    public function testCannotLoadFixtureWithInvalidHeaderReference(): void
    {
        $this->expectException(\Xezilaires\Exception\HeaderException::class);
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

    /**
     * @coversNothing
     */
    public function testCannotLoadFlatFixtureWithNestedArrayReference(): void
    {
        $this->expectException(\Xezilaires\Exception\ReferenceException::class);
        $this->expectExceptionMessage('Invalid reference');

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
     * @param array<int, array<string, string|array<string>>> $expected
     * @param \Iterator                                       $iterator
     */
    private function assertIteratorMatches(array $expected, \Iterator $iterator): void
    {
        $keys = array_keys(current($expected));
        $idxValidator = 0;

        /**
         * @var int    $idx
         * @var object $item
         */
        foreach ($iterator as $idx => $item) {
            static::assertSame($idxValidator, $idx);
            foreach ($keys as $key) {
                static::assertSame($expected[$idx][$key], $item->{$key});
            }

            ++$idxValidator;
        }
    }
}
