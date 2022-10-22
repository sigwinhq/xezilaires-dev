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

final class FilterIterator extends \FilterIterator
{
    /**
     * @var callable
     */
    private $filter;

    private int $key = -1;

    /**
     * @param callable(object): bool $filter
     */
    public function __construct(Iterator $iterator, callable $filter)
    {
        parent::__construct($iterator);

        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function key(): int
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(): bool
    {
        /** @var Iterator $iterator */
        $iterator = $this->getInnerIterator();
        $object = $iterator->current();

        /** @var bool $accepted */
        $accepted = \call_user_func($this->filter, $object);

        if ($accepted === true) {
            ++$this->key;
        }

        return $accepted;
    }
}
