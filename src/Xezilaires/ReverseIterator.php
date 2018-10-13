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
 * Class ReverseIterator.
 */
class ReverseIterator implements Iterator
{
    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * @var int
     */
    private $startIndex;

    /**
     * @var int
     */
    private $endIndex;

    /**
     * @var int
     */
    private $index;

    /**
     * @param Iterator $iterator
     * @param int      $startIndex
     * @param int      $endIndex
     */
    public function __construct(Iterator $iterator, int $startIndex, int $endIndex)
    {
        $this->iterator = $iterator;
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;

        $this->rewind();
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MissingReturnType Cannot type-hint object here because of 7.1 compat
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function seek(int $index = 1): void
    {
        $this->index = ($this->endIndex + 1) - $index;

        $this->iterator->seek($this->index);
    }

    /**
     * {@inheritdoc}
     */
    public function prev(): void
    {
        --$this->index;

        $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        ++$this->index;

        $this->iterator->prev();
    }

    /**
     * {@inheritdoc}
     */
    public function key(): int
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return $this->endIndex - $this->startIndex >= $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->index = 0;

        $this->iterator->seek($this->endIndex);
    }
}
