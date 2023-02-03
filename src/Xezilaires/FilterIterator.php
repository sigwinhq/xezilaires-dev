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

/**
 * @template T of object
 *
 * @extends \FilterIterator<int, T, Iterator<T>>
 */
final class FilterIterator extends \FilterIterator
{
    /**
     * @var callable(T): bool
     */
    private $filter;

    private int $key = -1;

    /**
     * @param Iterator<T>       $iterator
     * @param callable(T): bool $filter
     */
    public function __construct(Iterator $iterator, callable $filter)
    {
        parent::__construct($iterator);

        $this->filter = $filter;
    }

    public function key(): int
    {
        return $this->key;
    }

    public function accept(): bool
    {
        /** @var Iterator<T> $iterator */
        $iterator = $this->getInnerIterator();
        $object = $iterator->current();
        /**
         * @var bool $accepted
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $accepted = \call_user_func($this->filter, $object);

        if ($accepted === true) {
            ++$this->key;
        }

        return $accepted;
    }
}
