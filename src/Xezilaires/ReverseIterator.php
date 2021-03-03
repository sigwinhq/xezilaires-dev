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

final class ReverseIterator implements Iterator
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

    public function __construct(Iterator $iterator, int $startIndex, int $endIndex)
    {
        $this->iterator = $iterator;
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;

        $this->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function current(): object
    {
        return $this->iterator->current();
    }

    /**
     * {@inheritdoc}
     */
    public function seek(int $rowIndex = 1): void
    {
        $this->index = ($this->endIndex + 1) - $rowIndex;

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
