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

namespace Xezilaires\Bridge\Spout;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\RowIteratorInterface;
use Xezilaires\Iterator;

/**
 * @internal
 */
final class RowIterator implements Iterator
{
    private readonly RowIteratorInterface $iterator;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private int $highestRow;

    public function __construct(RowIteratorInterface $iterator, private readonly int $firstRow)
    {
        $iterator->rewind();

        $this->iterator = $iterator;
    }

    public function current(): object
    {
        /**
         * @var Row $row
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $row = $this->iterator->current();

        /** @var array<int, null|float|int|string> $current */
        $current = $row->toArray();

        return new \ArrayObject($current);
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function key(): int
    {
        /** @var int $key */
        $key = $this->iterator->key();

        return $key;
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->seek($this->firstRow);
    }

    public function seek(int $rowIndex): void
    {
        $currentIndex = $this->key();

        if ($currentIndex > $rowIndex) {
            $this->iterator->rewind();
            --$rowIndex;
        } else {
            $rowIndex -= $currentIndex;
        }

        for ($x = 1; $x <= $rowIndex; ++$x) {
            $this->next();
        }
    }

    public function prev(): void
    {
        $this->seek($this->key() - 1);
    }

    public function getHighestRow(): int
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->highestRow) === false) {
            $highestRow = 0;

            $this->iterator->rewind();
            while ($this->iterator->valid()) {
                ++$highestRow;
                $this->iterator->next();
            }

            // NOTE: Spout goes out of bounds, but the index is not incremented
            // bug workaround
            $this->prev();

            $this->highestRow = $highestRow;
        }

        return $this->highestRow;
    }
}
