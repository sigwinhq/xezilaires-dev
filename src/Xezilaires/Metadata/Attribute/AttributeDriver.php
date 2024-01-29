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

namespace Xezilaires\Metadata\Attribute;

use Xezilaires\Attribute;
use Xezilaires\Exception\AttributeException;
use Xezilaires\Metadata\ArrayReference;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Mapping;

final class AttributeDriver
{
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
            $arrayAttribute = $this->getPropertyAttribute(
                $reflectionProperty,
                Attribute\ArrayReference::class
            );
            $columnAttribute = $this->getPropertyAttribute(
                $reflectionProperty,
                Attribute\ColumnReference::class
            );
            $headerAttribute = $this->getPropertyAttribute(
                $reflectionProperty,
                Attribute\HeaderReference::class
            );

            if ($arrayAttribute === null && $columnAttribute === null && $headerAttribute === null) {
                // property not managed, skip
                continue;
            }

            if (($arrayAttribute xor $columnAttribute xor $headerAttribute) === false) {
                // if any is set, only one is allowed
                throw AttributeException::tooManyReferencesDefined($reflectionProperty, [$arrayAttribute, $columnAttribute, $headerAttribute]);
            }

            switch (true) {
                case $columnAttribute !== null:
                    $reference = $this->createReference($columnAttribute);
                    break;
                case $headerAttribute !== null:
                    $reference = $this->createReference($headerAttribute);
                    break;
                case $arrayAttribute !== null:
                    $references = [];
                    foreach ($arrayAttribute->references as $attribute) {
                        $references[] = $this->createReference($attribute);
                    }
                    $reference = new ArrayReference($references);
                    break;
                default:
                    throw AttributeException::unsupportedAttribute();
            }

            $columns[$reflectionProperty->getName()] = $reference;
        }

        return $columns;
    }

    private function getOptions(\ReflectionClass $reflectionClass, ?array $additionalOptions = null): array
    {
        $options = $this->getClassAttribute($reflectionClass, Attribute\Options::class);
        if ($additionalOptions !== null) {
            $options = array_replace($options, $additionalOptions);
        }

        return array_filter($options);
    }

    private function createReference(Attribute\Reference $attribute): ColumnReference|HeaderReference
    {
        return match (true) {
            $attribute instanceof Attribute\ColumnReference => new ColumnReference($attribute->column),
            $attribute instanceof Attribute\HeaderReference => new HeaderReference($attribute->header),
            default => throw AttributeException::unsupportedAttribute(),
        };
    }

    /**
     * @template T
     *
     * @param class-string<T> $name
     */
    private function getClassAttribute(\ReflectionClass $reflection, string $name): array
    {
        $attribute = current($reflection->getAttributes($name));
        if ($attribute !== false) {
            return $attribute->getArguments();
        }

        return [];
    }

    /**
     * @template T
     *
     * @param class-string<T> $name
     *
     * @phpstan-return T|null
     */
    private function getPropertyAttribute(\ReflectionProperty $reflection, string $name)
    {
        $attribute = current($reflection->getAttributes($name));
        if ($attribute !== false) {
            return $attribute->newInstance();
        }

        return null;
    }
}
