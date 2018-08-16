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

use Doctrine\Common\Annotations\AnnotationException as DoctrineAnnotationException;
use Xezilaires\Exception;

/**
 * Class AnnotationException.
 */
class AnnotationException extends \InvalidArgumentException implements Exception
{
    /**
     * @return AnnotationException
     */
    public static function unsupportedAnnotation(): self
    {
        return new self('Unsupported annotation');
    }

    /**
     * @param \ReflectionProperty                                                                     $property
     * @param array<int, null|\Xezilaires\Annotation\Reference|\Xezilaires\Annotation\ArrayReference> $references
     *
     * @return AnnotationException
     */
    public static function tooManyReferencesDefined(\ReflectionProperty $property, array $references): self
    {
        return new self('Too many references defined for '.$property->getName());
    }

    /**
     * @param DoctrineAnnotationException $exception
     *
     * @return AnnotationException
     */
    public static function failedCreatingAnnotationReader(DoctrineAnnotationException $exception): self
    {
        return new self('Failed creating annoration reader', 0, $exception);
    }
}
