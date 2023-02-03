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

use Xezilaires\Metadata\Mapping;

/**
 * @template T of object
 */
interface IteratorFactory
{
    /**
     * @param Mapping<T> $mapping
     *
     * @return Iterator<T>
     */
    public function fromFile(\SplFileObject $file, Mapping $mapping): Iterator;

    /**
     * @param Spreadsheet<T> $spreadsheet
     * @param Mapping<T>     $mapping
     *
     * @return Iterator<T>
     */
    public function fromSpreadsheet(Spreadsheet $spreadsheet, Mapping $mapping): Iterator;
}
