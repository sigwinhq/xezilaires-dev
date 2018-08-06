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
     * @return self
     */
    public static function duplicateHeader(): self
    {
        return new self('Header already used');
    }
}
