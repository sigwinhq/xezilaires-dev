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
    private Iterator $iterator;

    private int $startIndex;

    private int $endIndex;

    private int $index;

    public function __construct(Iterator $iterator, int $startIndex, int $endIndex)
    {
        $this->iterator = $iterator;
        $this->startIndex = $startIndex;
        $this->endIndex = $endIndex;

        $this->rewind();
    }

    public function current(): object
    {
        return $this->iterator->current();
    }

    public function seek(int $rowIndex = 1): void
    {
        $this->index = ($this->endIndex + 1) - $rowIndex;

        $this->iterator->seek($this->index);
    }

    public function prev(): void
    {
        --$this->index;

        $this->iterator->next();
    }

    public function next(): void
    {
        ++$this->index;

        $this->iterator->prev();
    }

    public function key(): int
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return $this->index <= $this->endIndex - $this->startIndex;
    }

    public function rewind(): void
    {
        $this->index = 0;

        $this->iterator->seek($this->endIndex);
    }
}
