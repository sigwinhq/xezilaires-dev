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

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class ArrayReference
{
    /**
     * @param Reference[] $references
     */
    public function __construct(public array $references)
    {
    }
}
