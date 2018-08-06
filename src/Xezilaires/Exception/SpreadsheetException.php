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

namespace Xezilaires\Exception;

use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use PhpOffice\PhpSpreadsheet\Reader\Exception as PhpSpreadsheetReaderException;
use Xezilaires\Exception;

/**
 * Class SpreadsheetException.
 */
class SpreadsheetException extends \InvalidArgumentException implements Exception
{
    /**
     * @return self
     */
    public static function noSpreadsheetFound(): self
    {
        return new self('No spreadsheet path given');
    }

    /**
     * @param PhpSpreadsheetReaderException $exception
     *
     * @return self
     */
    public static function invalidSpreadsheet(PhpSpreadsheetReaderException $exception): self
    {
        return new self('Invalid spreadsheet', 0, $exception);
    }

    /**
     * @param PhpSpreadsheetException $exception
     *
     * @return self
     */
    public static function invalidCell(PhpSpreadsheetException $exception): self
    {
        return new self('Invalid cell: '.$exception->getMessage(), 0, $exception);
    }

    /**
     * @param PhpSpreadsheetException $exception
     *
     * @return self
     */
    public static function failedToFetchActiveWorksheet(PhpSpreadsheetException $exception): self
    {
        return new self('Failed to fetch active worksheet: '.$exception->getMessage(), 0, $exception);
    }
}
