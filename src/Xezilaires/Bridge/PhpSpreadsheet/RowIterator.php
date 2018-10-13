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

namespace Xezilaires\Bridge\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator as PhpSpreadsheetRowIterator;
use Xezilaires\Exception\SpreadsheetException;
use Xezilaires\Iterator;

/**
 * Class RowIterator.
 */
class RowIterator implements Iterator
{
    /**
     * @var PhpSpreadsheetRowIterator
     */
    private $iterator;

    /**
     * @param PhpSpreadsheetRowIterator $iterator
     */
    public function __construct(PhpSpreadsheetRowIterator $iterator)
    {
        $this->iterator = $iterator;
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
    public function next(): void
    {
        $this->iterator->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key(): int
    {
        return $this->iterator->key();
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
        $this->iterator->rewind();
    }

    /**
     * @param int $index
     */
    public function seek(int $index): void
    {
        try {
            $this->iterator->seek($index);
        } catch (PhpSpreadsheetException $exception) {
            throw SpreadsheetException::invalidSeek($exception);
        }
    }

    public function prev(): void
    {
        $this->iterator->prev();
    }
}
