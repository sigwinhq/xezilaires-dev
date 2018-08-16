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

namespace Xezilaires\Metadata\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Xezilaires\Annotation;
use Xezilaires\Exception\AnnotationException;
use Xezilaires\Metadata\ArrayReference;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Mapping;

/**
 * Class AnnotationDriver.
 *
 * @internal
 */
class AnnotationDriver
{
    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * @param null|AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader = null)
    {
        try {
            $this->reader = $reader ?? new AnnotationReader();
        } catch (\Doctrine\Common\Annotations\AnnotationException $exception) {
            throw AnnotationException::failedCreatingAnnotationReader($exception);
        }
    }

    /**
     * @param string $className
     *
     * @return Mapping
     */
    public function getMetadataMapping(string $className): Mapping
    {
        $reflectionClass = new \ReflectionClass($className);

        return new Mapping($className, $this->getColumns($reflectionClass), $this->getOptions($reflectionClass));
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return array<string, \Xezilaires\Metadata\Reference>
     */
    private function getColumns(\ReflectionClass $reflectionClass): array
    {
        $columns = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            /** @var null|Annotation\ArrayReference $arrayAnnotation */
            $arrayAnnotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                Annotation\ArrayReference::class
            );
            /** @var null|Annotation\ColumnReference $columnAnnotation */
            $columnAnnotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                Annotation\ColumnReference::class
            );
            /** @var null|Annotation\HeaderReference $headerAnnotation */
            $headerAnnotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                Annotation\HeaderReference::class
            );

            if (null === $arrayAnnotation && null === $columnAnnotation && null === $headerAnnotation) {
                // property not managed, skip
                continue;
            }

            if (false === ($arrayAnnotation xor $columnAnnotation xor $headerAnnotation)) {
                // if any is set, only one is allowed
                throw AnnotationException::tooManyReferencesDefined(
                    $reflectionProperty,
                    [$arrayAnnotation, $columnAnnotation, $headerAnnotation]
                );
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

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return array
     */
    private function getOptions(\ReflectionClass $reflectionClass): array
    {
        $annotation = (array) $this->reader->getClassAnnotation($reflectionClass, Annotation\Options::class);

        return array_filter($annotation);
    }

    /**
     * @param Annotation\Reference $annotation
     *
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
}
