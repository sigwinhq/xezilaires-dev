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
     * @param \Throwable $exception
     *
     * @return self
     */
    public static function invalidSpreadsheet(\Throwable $exception): self
    {
        return new self('Invalid spreadsheet: '.$exception->getMessage(), 0, $exception);
    }

    /**
     * @param \Throwable $exception
     *
     * @return self
     */
    public static function invalidCell(\Throwable $exception): self
    {
        return new self('Invalid cell: '.$exception->getMessage(), 0, $exception);
    }

    /**
     * @param \Throwable $exception
     *
     * @return SpreadsheetException
     */
    public static function invalidSeek(\Throwable $exception): self
    {
        return new self('Invalid seek: '.$exception->getMessage(), 0, $exception);
    }

    /**
     * @param null|\Throwable $exception
     *
     * @return self
     */
    public static function failedFetchingActiveWorksheet(?\Throwable $exception = null): self
    {
        $message = 'Failed to fetch active worksheet';
        if (null === $exception) {
            return new self($message);
        }

        return new self($message.': '.$exception->getMessage(), 0, $exception);
    }

    /**
     * @return SpreadsheetException
     */
    public static function noIterator(): self
    {
        return new self('No iterator was created');
    }

    /**
     * @return SpreadsheetException
     */
    public static function iteratorAlreadyCreated(): self
    {
        return new self('Iterator already created');
    }
}
