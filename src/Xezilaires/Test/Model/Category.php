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

namespace Xezilaires\Test\Model;

final class Category
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var null|int
     */
    public $parent;

    /**
     * @var string
     */
    public $name;

    public function __toString(): string
    {
        return $this->name;
    }

    public function getIdentifier(): int
    {
        return $this->id;
    }

    public function hasParent(): bool
    {
        return null !== $this->parent;
    }

    public function getParentIdentifier(): ?int
    {
        return $this->parent;
    }
}
