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

use Xezilaires\Metadata\Mapping;

final class SpreadsheetIteratorFactory
{
    /**
     * @var Denormalizer
     */
    private $denormalizer;

    public function __construct(Denormalizer $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    /**
     * @throws \RuntimeException
     */
    public function createFromPath(\SplFileObject $path, Mapping $mapping): SpreadsheetIterator
    {
        switch (true) {
            case true === interface_exists(\PhpOffice\PhpSpreadsheet\Reader\IReader::class):
                $spreadsheet = new Bridge\PhpSpreadsheet\Spreadsheet($path);
                break;
            case true === interface_exists(\Box\Spout\Reader\ReaderInterface::class):
                $spreadsheet = new Bridge\Spout\Spreadsheet($path);
                break;
            default:
                throw new \RuntimeException('Install either phpoffice/phpspreadsheet or box/spout to read Excel files');
        }

        return $this->createFromSpreadsheet($spreadsheet, $mapping);
    }

    public function createFromSpreadsheet(Spreadsheet $spreadsheet, Mapping $mapping): SpreadsheetIterator
    {
        return new SpreadsheetIterator($spreadsheet, $mapping, $this->denormalizer);
    }
}
