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

namespace Xezilaires\Test;

/**
 * @internal
 */
trait IteratorMatcherTrait
{
    /**
     * @param array<int, array<string, array<string>|string>> $expected
     */
    private static function assertIteratorMatches(array $expected, \Iterator $iterator): void
    {
        $actual = [];

        /**
         * @var int    $idx
         * @var object $item
         */
        foreach ($iterator as $idx => $item) {
            $actual[$idx] = array_filter((array) $item);
        }

        static::assertSame($expected, $actual);
    }
}
