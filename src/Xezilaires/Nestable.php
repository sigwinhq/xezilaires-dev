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

namespace Xezilaires;

/**
 * Interface Nestable.
 */
interface Nestable
{
    /**
     * @return string|int|float
     */
    public function getIdentifier();

    /**
     * @return bool
     */
    public function hasParent(): bool;

    /**
     * @return null|string|int|float
     */
    public function getParentIdentifier();
}
