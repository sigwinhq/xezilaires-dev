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
 * Interface Loader.
 */
interface Spreadsheet
{
    /**
     * @param int $startRowIndex row index where this iterator starts, one-based
     */
    public function createIterator(int $startRowIndex): void;

    /**
     * @return Iterator
     */
    public function getIterator(): Iterator;

    /**
     * @param int $rowIndex row index to fetch, one-based
     *
     * @return array<string, null|string|int|float>
     */
    public function getRow(int $rowIndex): array;

    /**
     * @return array<string, null|string|int|float>
     */
    public function getCurrentRow(): array;

    /**
     * @return int row index of the last row, one-based
     */
    public function getHighestRow(): int;
}
