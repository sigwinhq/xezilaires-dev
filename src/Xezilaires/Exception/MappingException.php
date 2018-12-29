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

final class MappingException extends \InvalidArgumentException implements Exception
{
    public static function missingHeaderOption(): self
    {
        return new self('When using HeaderReference, "header" option is required');
    }

    public static function duplicateHeader(string $header, string $foundColumn, string $firstColumn): self
    {
        return new self(sprintf('Duplicate header "%1$s" found in "%2$s", first used in "%3$s"', $header, $foundColumn, $firstColumn));
    }

    public static function headerNotFound(string $header): self
    {
        return new self(sprintf('Invalid header "%1$s", not found', $header));
    }

    public static function classNotFound(string $className): self
    {
        return new self(sprintf('Invalid class "%1$s", not found', $className));
    }

    public static function noReferencesSpecified(): self
    {
        return new self('Invalid mapping, no references specified');
    }

    public static function invalidReference(string $name): self
    {
        return new self(sprintf('Invalid reference "%1$s"', $name));
    }

    public static function unexpectedReference(): self
    {
        return new self('Unexpected reference type');
    }

    /**
     * @param \Throwable&ExceptionInterface $exception
     */
    public static function invalidOption(ExceptionInterface $exception): self
    {
        return new self($exception->getMessage(), 0, $exception);
    }
}
