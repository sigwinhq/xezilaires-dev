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
 * Trait FixtureTrait.
 */
trait FixtureTrait
{
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
