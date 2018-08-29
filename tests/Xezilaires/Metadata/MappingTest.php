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

namespace Xezilaires\Test\Metadata;

use PHPUnit\Framework\TestCase;
use Xezilaires\Exception\MappingException;
use Xezilaires\Metadata\ArrayReference;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Mapping;
use Xezilaires\Test\Model\Product;

/**
 * Class PhpSpreadsheetIteratorTest.
 *
 * @covers \Xezilaires\Metadata\Mapping
 */
class MappingTest extends TestCase
{
    /**
     * @uses \Xezilaires\Metadata\ColumnReference
     */
    public function testMappingHasDefaultOptions(): void
    {
        $mapping = new Mapping(Product::class, ['name' => new ColumnReference('A')]);

        static::assertEquals(1, $mapping->getOption('start'));
        static::assertNull($mapping->getOption('end'));
        static::assertNull($mapping->getOption('header'));
        static::assertFalse($mapping->getOption('reverse'));
    }

    /**
     * @dataProvider getValidMappings
     *
     * @param string                                        $className
     * @param array<string, \Xezilaires\Metadata\Reference> $columns
     * @param null|array<string, null|string|bool>          $options
     */
    public function testCanCreateValidMapping(string $className, array $columns, ?array $options = null): void
    {
        $mapping = new Mapping($className, $columns, $options);

        static::assertEquals($className, $mapping->getClassName());
        static::assertEquals($columns, $mapping->getReferences());

        if (null !== $options) {
            foreach ($options as $option => $value) {
                static::assertEquals($value, $mapping->getOption($option));
            }
        }
    }

    /**
     * @dataProvider getInvalidMappings
     *
     * @param string                                        $exceptionMessage
     * @param string                                        $className
     * @param array<string, \Xezilaires\Metadata\Reference> $columns
     * @param null|array<string, null|string|bool>          $options
     */
    public function testCannotCreateInvalidMapping(string $exceptionMessage, string $className, array $columns, ?array $options = null): void
    {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage($exceptionMessage);

        new Mapping($className, $columns, $options);
    }

    /**
     * @return array
     */
    public function getValidMappings(): array
    {
        return [
            [Product::class, ['name' => new ArrayReference([new ColumnReference('A'), new HeaderReference('Price')])]],
            [Product::class, ['name' => new ColumnReference('A')]],
            [Product::class, ['name' => new HeaderReference('Name')], ['header' => 1]],
            [Product::class, ['name' => new HeaderReference('Name')], ['start' => 2, 'header' => 4, 'reverse' => true]],
        ];
    }

    /**
     * @return array
     */
    public function getInvalidMappings(): array
    {
        return [
            ['Invalid class "foo"', 'foo', ['name' => new ColumnReference('A')]],

            ['Invalid mapping, no references specified', Product::class, []],

            ['Invalid reference "aaa"', Product::class, ['aaa' => 123]],

            ['When using HeaderReference, "header" option is required', Product::class, ['name' => new HeaderReference('Name')]],

            ['The option "start" with value "yes" is expected to be of type "int"', Product::class, ['name' => new ColumnReference('A')], ['start' => 'yes']],
            ['The option "end" with value "yes" is expected to be of type "int"', Product::class, ['name' => new ColumnReference('A')], ['end' => 'yes']],
            ['The option "header" with value "yes" is expected to be of type "int"', Product::class, ['name' => new ColumnReference('A')], ['header' => 'yes']],
            ['The option "reverse" with value "yes" is expected to be of type "bool"', Product::class, ['name' => new ColumnReference('A')], ['reverse' => 'yes']],
        ];
    }
}
