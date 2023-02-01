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

namespace Xezilaires\Bridge\PhpSpreadsheet;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as PhpSpreadsheetReaderException;
use PhpOffice\PhpSpreadsheet\Spreadsheet as PhpSpreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Xezilaires\Exception\SpreadsheetException;
use Xezilaires\Iterator;
use Xezilaires\Spreadsheet as SpreadsheetInterface;

/**
 * @implements SpreadsheetInterface<object>
 */
final class Spreadsheet implements SpreadsheetInterface
{
    private \SplFileObject $file;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private PhpSpreadsheet $spreadsheet;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private Iterator $iterator;

    public function __construct(\SplFileObject $file)
    {
        $this->file = $file;
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

        $this->iterator = new RowIterator($sheet->getRowIterator($startRowIndex));
    }

    public function getIterator(): Iterator
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->iterator) === false) {
            throw SpreadsheetException::noIterator();
        }

        return $this->iterator;
    }

    public function getCurrentRow(): array
    {
        /** @var Row $row */
        $row = $this->getIterator()->current();

        return $this->getRow($row->getRowIndex());
    }

    public function getRow(int $rowIndex): array
    {
        $data = [];
        $worksheet = $this->getActiveWorksheet();
        $columnIterator = $worksheet->getColumnIterator();

        foreach ($columnIterator as $column) {
            $columnName = $column->getColumnIndex();
            $data[$columnName] = $this->fetchCell($columnName, $rowIndex);
        }

        return $data;
    }

    public function getHighestRow(): int
    {
        return $this->getActiveWorksheet()->getHighestRow();
    }

    private function getSpreadsheet(): PhpSpreadsheet
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        if (isset($this->spreadsheet) === false) {
            $path = $this->file->getRealPath();
            if ($path === false) {
                throw SpreadsheetException::noSpreadsheetFound();
            }

            try {
                $reader = IOFactory::createReaderForFile($path);
                $this->spreadsheet = $reader->load($path);
            } catch (PhpSpreadsheetReaderException $exception) {
                throw SpreadsheetException::invalidSpreadsheet($exception);
            }
        }

        return $this->spreadsheet;
    }

    private function getActiveWorksheet(): Worksheet
    {
        try {
            return $this->getSpreadsheet()->getActiveSheet();
        } catch (PhpSpreadsheetException $exception) {
            throw SpreadsheetException::failedFetchingActiveWorksheet($exception);
        }
    }

    /**
     * @return null|float|int|string
     */
    private function fetchCell(string $columnName, int $rowIndex)
    {
        $worksheet = $this->getActiveWorksheet();
        $columnIndex = Coordinate::columnIndexFromString($columnName);

        $cell = $worksheet->getCell([$columnIndex, $rowIndex]);

        /** @var null|float|int|string $value */
        $value = $cell->getValue();

        return $value;
    }
}
