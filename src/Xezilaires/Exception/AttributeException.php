<?php

declare(strict_types=1);

/*
 * This file is part of the xezilaires project.
 *
 * (c) sigwin.hr
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Xezilaires\Exception;

use Xezilaires\Exception;

final class AttributeException extends \InvalidArgumentException implements Exception
{
    public static function unsupportedAttribute(): self
    {
        return new self('Unsupported attribute');
    }

    /**
     * @param array<int, null|\Xezilaires\Attribute\ArrayReference|\Xezilaires\Attribute\Reference> $references
     */
    public static function tooManyReferencesDefined(\ReflectionProperty $property, array $references): self
    {
        return new self('Too many references defined for '.$property->getName());
    }
}
