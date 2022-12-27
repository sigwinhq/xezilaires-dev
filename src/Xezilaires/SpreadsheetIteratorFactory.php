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
 *                      
 * @implements IteratorFactory<T>
 */
final class SpreadsheetIteratorFactory implements IteratorFactory
{
    private Denormalizer $denormalizer;

    /**
     * @var array<class-string<Spreadsheet<T>>>
     */
    private array $spreadsheetClasses;

    /**
     * @param array<class-string<Spreadsheet<T>>> $spreadsheetClasses
     */
    public function __construct(Denormalizer $denormalizer, array $spreadsheetClasses)
    {
        $this->denormalizer = $denormalizer;
        $this->spreadsheetClasses = $spreadsheetClasses;
    }

    /**
     * @throws \RuntimeException
     */
    public function fromFile(\SplFileObject $file, Mapping $mapping): Iterator
    {
        foreach ($this->spreadsheetClasses as $spreadsheetClass) {
            return $this->fromSpreadsheet($spreadsheetClass::fromFile($file), $mapping);
        }

        throw new \RuntimeException('Install either sigwin/xezilaires-phpspreadsheet or sigwin/xezilaires-spout to read Excel files');
    }

    public function fromSpreadsheet(Spreadsheet $spreadsheet, Mapping $mapping): Iterator
    {
        return new SpreadsheetIterator($spreadsheet, $mapping, $this->denormalizer);
    }
}
