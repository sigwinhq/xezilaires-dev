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

use Xezilaires\Bridge\Symfony\Serializer\Denormalizer;
use Xezilaires\Bridge\Symfony\Serializer\Exception as SerializerException;
use Xezilaires\Bridge\Symfony\Serializer\ObjectNormalizer;
use Xezilaires\Exception\DenormalizerException;
use Xezilaires\Exception\MappingException;
use Xezilaires\Metadata\ArrayReference;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Mapping;
use Xezilaires\Metadata\Reference;

/**
 * Class SpreadsheetIterator.
 */
class SpreadsheetIterator implements Iterator
{
    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    /**
     * @var Mapping
     */
    private $mapping;

    /**
     * @var null|Iterator
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
     * @param Spreadsheet $spreadsheet
     * @param Mapping     $mapping
     */
    public function __construct(Spreadsheet $spreadsheet, Mapping $mapping)
    {
        $this->spreadsheet = $spreadsheet;
        $this->mapping = $mapping;
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress MissingReturnType Cannot type-hint object here because of 7.1 compat
     */
    public function current()
    {
        $row = $this->spreadsheet->getCurrentRow();

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
    public function seek(int $rowIndex): void
    {
        /** @var int $start */
        $start = $this->mapping->getOption('start');
        $this->index = $rowIndex;

        $this->getIterator()->seek($start + $rowIndex);
    }

    /**
     * {@inheritdoc}
     */
    private function getIterator(): Iterator
    {
        if (null === $this->iterator) {
            /** @var int $start */
            $start = $this->mapping->getOption('start');
            $this->spreadsheet->createIterator($start);

            $iterator = $this->spreadsheet->getIterator();

            $reverse = $this->mapping->getOption('reverse');
            if (true === $reverse) {
                $iterator = new ReverseIterator($iterator, $start, $this->spreadsheet->getHighestRow());
            }
            $this->iterator = $iterator;
        }

        return $this->iterator;
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
            $headerRow = $this->spreadsheet->getRow($headerRowIndex);

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
