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
 * Class NestableIteratorException.
 */
class NestableIteratorException extends \InvalidArgumentException implements Exception
{
    /**
     * @return self
     */
    public static function iteratorMustBeNestable(): self
    {
        return new self('Iterator must be nestable');
    }

    /**
     * @param string|int|float $node
     *
     * @return self
     */
    public static function noSuchNode($node): self
    {
        return new self(sprintf('No such node: "%1$s"', $node));
    }
}
