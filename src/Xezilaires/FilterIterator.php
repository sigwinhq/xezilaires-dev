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
 * Class FilterIterator.
 */
class FilterIterator extends \FilterIterator
{
    /**
     * @var callable
     */
    private $filter;

    /**
     * @var int
     */
    private $key = -1;

    /**
     * @param Iterator               $iterator
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
        /** @var object $object */
        $object = $this->getInnerIterator()->current();

        /** @var bool $accepted */
        $accepted = \call_user_func($this->filter, $object);

        if (true === $accepted) {
            ++$this->key;
        }

        return $accepted;
    }
}
