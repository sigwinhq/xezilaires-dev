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
trait FixtureTrait
{
    private function fixture(string $name): \SplFileObject
    {
        return new \SplFileObject(__DIR__.'/resources/fixtures/'.$name);
    }

    private function invalidFixture(string $name): \SplFileObject
    {
        return new class(__DIR__.'/resources/fixtures/'.$name) extends \SplFileObject {
            /**
             * @psalm-suppress ImplementedReturnTypeMismatch Invalid by design
             *
             * @phpstan-ignore-next-line
             */
            #[\ReturnTypeWillChange]
            public function getRealPath(): bool
            {
                return false;
            }
        };
    }
}
