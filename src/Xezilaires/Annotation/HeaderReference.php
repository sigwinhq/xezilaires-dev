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
 * @Target({"ANNOTATION", "PROPERTY"})
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class HeaderReference implements Reference
{
    public string $header;

    public function __construct(string $header)
    {
        $this->header = $header;
    }
}
