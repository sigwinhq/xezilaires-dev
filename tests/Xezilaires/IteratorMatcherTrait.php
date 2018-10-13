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

/**
 * Class IteratorMatcherTrait.
 */
trait IteratorMatcherTrait
{
    /**
     * @param array<int, array<string, string|array<string>>> $expected
     * @param \Iterator                                       $iterator
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

        static::assertEquals($expected, $actual);
    }
}
