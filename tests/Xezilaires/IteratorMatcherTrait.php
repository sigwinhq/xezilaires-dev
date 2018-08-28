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

        if (0 === $idxValidator && \count($expected) > 0) {
            static::fail('Iterator does not iterate');
        }
    }
}
