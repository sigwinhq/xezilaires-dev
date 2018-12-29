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

namespace Xezilaires\Test\Bridge\Spout;

use Nyholm\NSA;
use PHPUnit\Framework\TestCase;
use Xezilaires\Bridge\Spout\Spreadsheet;
use Xezilaires\Test\FakeIterator;
use Xezilaires\Test\FixtureTrait;

/**
 * @covers \Xezilaires\Bridge\Spout\Spreadsheet
 *
 * @internal
 */
final class SpreadsheetTest extends TestCase
{
    use FixtureTrait;

    public function testCanCorrectlyCalculateColumns(): void
    {
        $row = [
            0 => 'A',
            25 => 'Z',
            26 => 'AA',
        ];
        /** @var string[] $values */
        $values = array_values($row);

        $object = new Spreadsheet($this->invalidFixture('products.xlsx'));
        NSA::setProperty($object, 'iterator', new FakeIterator([(object) $row]));

        static::assertEquals(array_combine($values, $values), $object->getCurrentRow());
    }
}
