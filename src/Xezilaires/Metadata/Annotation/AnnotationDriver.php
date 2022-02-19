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

namespace Xezilaires\Metadata\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Xezilaires\Annotation;
use Xezilaires\Exception\AnnotationException;
use Xezilaires\Metadata\ArrayReference;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Mapping;

/**
 * @internal
 */
final class AnnotationDriver
{
    private AnnotationReader $reader;

    /**
     * @throws \RuntimeException if Doctrine's Annotations component is not available
     */
    public function __construct(AnnotationReader $reader = null)
    {
        if (false === class_exists(AnnotationReader::class)) {
            throw new \RuntimeException('Xezilaires annotations support requires Doctrine Annotations component. Install "doctrine/annotations" to use it.');
        }

        try {
            $this->reader = $reader ?? new AnnotationReader();
        } catch (\Doctrine\Common\Annotations\AnnotationException $exception) {
            throw AnnotationException::failedCreatingAnnotationReader($exception);
        }
    }

    /**
     * @throws \ReflectionException
     *
     * @psalm-param class-string $className
     */
    public function getMetadataMapping(string $className, ?array $options = null): Mapping
    {
        $reflectionClass = new \ReflectionClass($className);

        return new Mapping($className, $this->getColumns($reflectionClass), $this->getOptions($reflectionClass, $options));
    }

    /**
     * @return array<string, \Xezilaires\Metadata\Reference>
     */
    private function getColumns(\ReflectionClass $reflectionClass): array
    {
        $columns = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $arrayAnnotation = $this->getPropertyAnnotationOrAttribute(
                $reflectionProperty,
                Annotation\ArrayReference::class
            );
            $columnAnnotation = $this->getPropertyAnnotationOrAttribute(
                $reflectionProperty,
                Annotation\ColumnReference::class
            );
            $headerAnnotation = $this->getPropertyAnnotationOrAttribute(
                $reflectionProperty,
                Annotation\HeaderReference::class
            );

            if (null === $arrayAnnotation && null === $columnAnnotation && null === $headerAnnotation) {
                // property not managed, skip
                continue;
            }

            if (false === ($arrayAnnotation xor $columnAnnotation xor $headerAnnotation)) {
                // if any is set, only one is allowed
                throw AnnotationException::tooManyReferencesDefined($reflectionProperty, [$arrayAnnotation, $columnAnnotation, $headerAnnotation]);
            }

            switch (true) {
                case null !== $columnAnnotation:
                    $reference = $this->createReference($columnAnnotation);
                    break;
                case null !== $headerAnnotation:
                    $reference = $this->createReference($headerAnnotation);
                    break;
                case null !== $arrayAnnotation:
                    $references = [];
                    foreach ($arrayAnnotation->references as $annotation) {
                        $references[] = $this->createReference($annotation);
                    }
                    $reference = new ArrayReference($references);
                    break;
                default:
                    throw AnnotationException::unsupportedAnnotation();
            }

            $columns[$reflectionProperty->getName()] = $reference;
        }

        return $columns;
    }

    private function getOptions(\ReflectionClass $reflectionClass, ?array $additionalOptions = null): array
    {
        $options = $this->getClassAnnotationOrAttribute($reflectionClass, Annotation\Options::class);
        if (null !== $additionalOptions) {
            $options = array_replace($options, $additionalOptions);
        }

        return array_filter($options);
    }

    /**
     * @return ColumnReference|HeaderReference
     */
    private function createReference(Annotation\Reference $annotation)
    {
        switch (true) {
            case $annotation instanceof Annotation\ColumnReference:
                $reference = new ColumnReference($annotation->column);
                break;
            case $annotation instanceof Annotation\HeaderReference:
                $reference = new HeaderReference($annotation->header);
                break;
            default:
                throw AnnotationException::unsupportedAnnotation();
        }

        return $reference;
    }

    /**
     * @template T
     *
     * @param class-string<T> $name
     */
    private function getClassAnnotationOrAttribute(\ReflectionClass $reflection, string $name): array
    {
        if (\PHP_VERSION_ID >= 80000) {
            $attribute = current($reflection->getAttributes($name));
            if ($attribute !== false) {
                return $attribute->getArguments();
            }
        }

        return (array) $this->reader->getClassAnnotation($reflection, $name);
    }

    /**
     * @template T
     *
     * @param class-string<T> $name
     *
     * @phpstan-return T|null
     */
    private function getPropertyAnnotationOrAttribute(\ReflectionProperty $reflection, string $name)
    {
        if (\PHP_VERSION_ID >= 80000) {
            $attribute = current($reflection->getAttributes($name));
            if ($attribute !== false) {
                return $attribute->newInstance();
            }
        }

        return $this->reader->getPropertyAnnotation($reflection, $name);
    }
}
