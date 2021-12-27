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

use Box\Spout\Common\Entity\Row;
use Box\Spout\Reader\IteratorInterface;
use Xezilaires\Iterator;

/**
 * @internal
 */
final class RowIterator implements Iterator
{
    private IteratorInterface $iterator;

    private int $firstRow;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ?int $highestRow;

    public function __construct(IteratorInterface $iterator, int $firstRow)
    {
        $iterator->rewind();

        $this->iterator = $iterator;
        $this->firstRow = $firstRow;
    }

    /**
     * {@inheritdoc}
     */
    public function current(): object
    {
        /** @var Row $row */
        $row = $this->iterator->current();

        /** @var array<int, null|float|int|string> $current */
        $current = $row->toArray();

        return new \ArrayObject($current);
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key(): int
    {
        /** @var int $key */
        $key = $this->iterator->key();

        return $key;
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->seek($this->firstRow);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function prev(): void
    {
        $this->seek($this->key() - 1);
    }

    public function getHighestRow(): int
    {
        if (null === $this->highestRow) {
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
