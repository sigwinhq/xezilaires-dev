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
 * Class HeaderException.
 */
class HeaderException extends \InvalidArgumentException implements Exception
{
    /**
     * @return self
     */
    public static function missingHeaderOption(): self
    {
        return new self('Header option is required');
    }

    /**
     * @param string $header
     * @param string $foundColumn
     * @param string $firstColumn
     *
     * @return self
     */
    public static function duplicateHeader(string $header, string $foundColumn, string $firstColumn): self
    {
        return new self(sprintf('Duplicate header "%1$s" found in "%2$s", first used in "%3$s"', $header, $foundColumn, $firstColumn));
    }

    /**
     * @param string $header
     *
     * @return HeaderException
     */
    public static function headerNotFound(string $header): self
    {
        return new self(sprintf('Invalid header "%1$s", not found', $header));
    }
}
