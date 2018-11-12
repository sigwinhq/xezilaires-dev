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
use Xezilaires\FilterIterator;

/**
 * @covers \Xezilaires\FilterIterator
 */
class FilterIteratorTest extends TestCase
{
    public function testCanFilterOutIteratorItems(): void
    {
        $iterator = new FakeIterator([1, 2, 3, 4, 5]);
        $filter = new FilterIterator($iterator, /** @psalm-param object $item */ function ($item): bool {
            /** @var int $scalar */
            $scalar = $item->scalar;

            return $scalar % 2 === 0;
        });

        static::assertEquals([2, 4], FakeIterator::toArray($filter));
    }

    public function testCallbackReturnValueMustBeBool(): void
    {
        $iterator = new FakeIterator(['ba', 'abba', 'boo', 'bae', 'nba', 'ab', 'ban']);
        $filter = new FilterIterator($iterator, /** @psalm-param object $item */ function ($item): bool {
            /** @var string $scalar */
            $scalar = $item->scalar;

            return false !== mb_strpos($scalar, 'ba');
        });

        static::assertEquals(['ba', 'abba', 'bae', 'nba', 'ban'], FakeIterator::toArray($filter));
    }
}
