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

namespace Xezilaires;

interface Nestable
{
    /**
     * @return float|int|string
     */
    public function getIdentifier();

    public function hasParent(): bool;

    /**
     * @return null|float|int|string
     */
    public function getParentIdentifier();
}
