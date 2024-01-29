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

namespace Xezilaires\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
final class Options
{
    public function __construct(public int $start = 0, public int $end = \PHP_INT_MAX, public int $header = 0, public bool $reverse = false)
    {
    }
}
