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
use Xezilaires\Metadata\ColumnReference;
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
    public function testCanLoadFlatFixture(): void
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
     * @param string $name
     *
     * @return \SplFileObject
     */
    private function fixture(string $name): \SplFileObject
    {
        return new \SplFileObject(__DIR__.'/../../resources/fixtures/'.$name);
    }

    /**
     * @param array<int, array<string, string>> $expected
     * @param \Iterator                         $iterator
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
            self::assertSame($idxValidator, $idx);
            foreach ($keys as $key) {
                self::assertSame($expected[$idx][$key], $item->{$key});
            }

            ++$idxValidator;
        }
    }
}
