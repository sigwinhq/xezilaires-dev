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

namespace Xezilaires\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class Options
{
    public int $start;

    public int $end;

    public int $header;

    public bool $reverse;

    public function __construct(int $start = 0, int $end = \PHP_INT_MAX, int $header = 0, bool $reverse = false)
    {
        $this->start = $start;
        $this->end = $end;
        $this->header = $header;
        $this->reverse = $reverse;
    }
}
