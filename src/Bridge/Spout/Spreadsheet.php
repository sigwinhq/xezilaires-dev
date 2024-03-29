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

use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use OpenSpout\Reader\ReaderInterface;
use OpenSpout\Reader\SheetInterface;
use OpenSpout\Reader\XLSX\Reader;
use Xezilaires\Exception\SpreadsheetException;
use Xezilaires\Iterator;
use Xezilaires\Spreadsheet as SpreadsheetInterface;

final class Spreadsheet implements SpreadsheetInterface
{
    /**
     * @var array<int, string>
     */
    private static array $indexCache = [];

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ReaderInterface $reader;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     *
     * @psalm-var Iterator&RowIterator
     */
    private Iterator $iterator;

    public function __construct(private readonly \SplFileObject $file)
    {
    }

    public static function fromFile(\SplFileObject $file): SpreadsheetInterface
    {
        return new self($file);
    }

    public function createIterator(int $startRowIndex): void
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->iterator)) {
            throw SpreadsheetException::iteratorAlreadyCreated();
        }

        $sheet = $this->getActiveWorksheet();
        $iterator = $sheet->getRowIterator();

        $this->iterator = new RowIterator($iterator, $startRowIndex);
    }

    public function getIterator(): Iterator
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->iterator) === false) {
            throw SpreadsheetException::noIterator();
        }

        return $this->iterator;
    }

    public function getRow(int $rowIndex): array
    {
        $iterator = $this->getIterator();
        $seekRow = $iterator->key();
        $iterator->seek($rowIndex);
        $row = $this->getCurrentRow();
        $iterator->seek($seekRow);

        return $row;
    }

    public function getCurrentRow(): array
    {
        /** @var \ArrayObject $rowArrayObject */
        $rowArrayObject = $this->getIterator()->current();

        $row = [];

        /**
         * @var int                   $columnIndex
         * @var null|float|int|string $columnValue
         */
        foreach ($rowArrayObject as $columnIndex => $columnValue) {
            $columnName = self::stringFromColumnIndex($columnIndex + 1);

            $row[$columnName] = $columnValue !== '' ? $columnValue : null;
        }

        return $row;
    }

    public function getHighestRow(): int
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->iterator) === false) {
            throw SpreadsheetException::noIterator();
        }

        return $this->iterator->getHighestRow();
    }

    /**
     * @author https://github.com/phpoffice/PhpSpreadsheet
     */
    private static function stringFromColumnIndex(int $columnIndex): string
    {
        if (isset(self::$indexCache[$columnIndex]) === false) {
            $indexValue = $columnIndex;
            $base26 = '';
            do {
                $normalizedIndexValue = $indexValue % 26;
                $characterValue = $normalizedIndexValue > 0 ? $normalizedIndexValue : 26;
                $indexValue = ($indexValue - $characterValue) / 26;
                $base26 = \chr($characterValue + 64).$base26;
            } while ($indexValue > 0);

            self::$indexCache[$columnIndex] = $base26;
        }

        return self::$indexCache[$columnIndex];
    }

    private function getReader(): ReaderInterface
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->reader) === false) {
            $path = $this->file->getRealPath();
            if ($path === false) {
                throw SpreadsheetException::noSpreadsheetFound();
            }

            try {
                $this->reader = new Reader();
                $this->reader->open($path);
            } catch (IOException|UnsupportedTypeException $exception) {
                throw SpreadsheetException::invalidSpreadsheet($exception);
            }
        }

        return $this->reader;
    }

    private function getActiveWorksheet(): SheetInterface
    {
        try {
            /** @var SheetInterface $sheet */
            foreach ($this->getReader()->getSheetIterator() as $sheet) {
                return $sheet;
            }
        } catch (ReaderNotOpenedException $exception) {
            throw SpreadsheetException::failedFetchingActiveWorksheet($exception);
        }

        throw SpreadsheetException::failedFetchingActiveWorksheet();
    }
}
