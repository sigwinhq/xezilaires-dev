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

use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Xezilaires\Exception;

/**
 * Class OptionsException.
 */
class OptionsException extends \InvalidArgumentException implements Exception
{
    /**
     * @param ExceptionInterface&\Throwable $exception
     *
     * @return self
     */
    public static function invalidOption(ExceptionInterface $exception): self
    {
        return new self('Invalid option', 0, $exception);
    }
}
