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

final readonly class SpreadsheetIteratorFactory implements IteratorFactory
{
    /**
     * @param array<class-string<Spreadsheet>> $spreadsheetClasses
     */
    public function __construct(private Denormalizer $denormalizer, private array $spreadsheetClasses)
    {
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
