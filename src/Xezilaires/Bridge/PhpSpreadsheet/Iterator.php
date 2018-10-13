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

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as PhpSpreadsheetReaderException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Xezilaires\Bridge\Symfony\Serializer\Denormalizer;
use Xezilaires\Bridge\Symfony\Serializer\Exception as SerializerException;
use Xezilaires\Bridge\Symfony\Serializer\ObjectNormalizer;
use Xezilaires\Exception\DenormalizerException;
use Xezilaires\Exception\MappingException;
use Xezilaires\Exception\SpreadsheetException;
use Xezilaires\Iterator as IteratorInterface;
use Xezilaires\Metadata\ArrayReference;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Mapping;
use Xezilaires\Metadata\Reference;
use Xezilaires\ReverseIterator;

/**
 * Class CategoryProvider.
 */
class Iterator implements IteratorInterface
{
    private const CELL_NO_AUTO_CREATE = false;

    /**
     * @var \SplFileObject
     */
    private $file;

    /**
     * @var Mapping
     */
    private $mapping;

    /**
     * @var null|Spreadsheet
     */
    private $spreadsheet;

    /**
     * @var null|IteratorInterface
     */
    private $iterator;

    /**
     * @var null|Denormalizer
     */
    private $denormalizer;

    /**
     * @var null|array<string, string>
     */
    private $headers;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @param \SplFileObject $file
     * @param Mapping        $mapping
     */
    public function __construct(\SplFileObject $file, Mapping $mapping)
    {
        $this->file = $file;
        $this->mapping = $mapping;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MissingReturnType Cannot type-hint object here because of 7.1 compat
     */
    public function current()
    {
        /** @var Row $row */
        $row = $this->getIterator()->current();
        $row = $this->fetchRow($row->getRowIndex());

        /** @var array<string, null|string|int|float|array<null|string|int|float>> $data */
        $data = [];
        foreach ($this->mapping->getReferences() as $name => $reference) {
            if ($reference instanceof ArrayReference) {
                $data[$name] = $this->readArrayReference($row, $reference);
            } else {
                $data[$name] = $this->readReference($row, $reference);
            }
        }

        try {
            return $this->getDenormalizer()->denormalize($data, $this->mapping->getClassName());
        } catch (SerializerException $exception) {
            throw DenormalizerException::denormalizationFailed($exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prev(): void
    {
        --$this->index;

        $this->getIterator()->prev();
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        ++$this->index;

        $this->getIterator()->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key(): int
    {
        return $this->index;
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return $this->getIterator()->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->index = 0;

        $this->getIterator()->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function seek(int $index): void
    {
        /** @var int $start */
        $start = $this->mapping->getOption('start');
        $this->index = $index;

        $this->getIterator()->seek($start + $index);
    }

    /**
     * @return Spreadsheet
     */
    private function getSpreadsheet(): Spreadsheet
    {
        if (null === $this->spreadsheet) {
            $path = $this->file->getRealPath();
            if (false === $path) {
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

    /**
     * @return Worksheet
     */
    private function getActiveWorksheet(): Worksheet
    {
        try {
            return $this->getSpreadsheet()->getActiveSheet();
        } catch (PhpSpreadsheetException $exception) {
            throw SpreadsheetException::failedFetchingActiveWorksheet($exception);
        }
    }

    /**
     * @return IteratorInterface
     */
    private function getIterator(): IteratorInterface
    {
        if (null === $this->iterator) {
            $sheet = $this->getActiveWorksheet();

            /** @var int $start */
            $start = $this->mapping->getOption('start');
            $iterator = new RowIterator($sheet->getRowIterator($start));

            $reverse = $this->mapping->getOption('reverse');
            if (true === $reverse) {
                $iterator = new ReverseIterator($iterator, $start, $sheet->getHighestRow());
            }
            $this->iterator = $iterator;
        }

        return $this->iterator;
    }

    /**
     * @param int $rowIndex
     *
     * @return array<string, null|string|int|float>
     */
    private function fetchRow(int $rowIndex): array
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

    /**
     * @param string $columnName
     * @param int    $rowIndex
     *
     * @return null|string|int|float
     */
    private function fetchCell(string $columnName, int $rowIndex)
    {
        $worksheet = $this->getActiveWorksheet();
        $columnIndex = Coordinate::columnIndexFromString($columnName);

        /** @var null|Cell $cell */
        $cell = $worksheet->getCellByColumnAndRow($columnIndex, $rowIndex, self::CELL_NO_AUTO_CREATE);
        if (null === $cell) {
            return null;
        }

        /** @var null|string|int|float $value */
        $value = $cell->getValue();

        return $value;
    }

    /**
     * @return Denormalizer
     */
    private function getDenormalizer(): Denormalizer
    {
        if (null === $this->denormalizer) {
            $this->denormalizer = new ObjectNormalizer();
        }

        return $this->denormalizer;
    }

    /**
     * @return array<string, string>
     */
    private function getHeaderColumnReferences(): array
    {
        if (null === $this->headers) {
            /** @var null|int $headerRowIndex */
            $headerRowIndex = $this->mapping->getOption('header');
            if (null === $headerRowIndex) {
                throw MappingException::missingHeaderOption();
            }
            /** @var array<string, null|string> $headerRow */
            $headerRow = $this->fetchRow($headerRowIndex);

            $headers = [];
            foreach ($headerRow as $column => $header) {
                if (null === $header) {
                    continue;
                }

                if (isset($headers[$header])) {
                    throw MappingException::duplicateHeader($header, $column, $headers[$header]);
                }

                $headers[$header] = $column;
            }
            $this->headers = $headers;
        }

        return $this->headers;
    }

    /**
     * @param string $header
     *
     * @return string
     */
    private function getColumnByHeader(string $header): string
    {
        $headerColumnReferences = $this->getHeaderColumnReferences();
        if (false === \array_key_exists($header, $headerColumnReferences)) {
            throw MappingException::headerNotFound($header);
        }

        return $headerColumnReferences[$header];
    }

    /**
     * @param array<string, null|string|int|float> $row
     * @param ArrayReference                       $reference
     *
     * @return array<null|string|int|float>
     */
    private function readArrayReference(array $row, ArrayReference $reference): array
    {
        $data = [];
        foreach ($reference->getReference() as $arrayReference) {
            $data[] = $this->readReference($row, $arrayReference);
        }

        return $data;
    }

    /**
     * @param array<string, null|string|int|float> $row
     * @param Reference                            $reference
     *
     * @return null|string|int|float
     */
    private function readReference(array $row, Reference $reference)
    {
        switch (true) {
            case $reference instanceof ColumnReference:
                $column = $reference->getReference();
                $data = $row[$column];
                break;
            case $reference instanceof HeaderReference:
                $column = $this->getColumnByHeader($reference->getReference());
                $data = $row[$column];
                break;
            default:
                throw MappingException::unexpectedReference();
        }

        return $data;
    }
}
