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

namespace Xezilaires\Test\Metadata;

use PHPUnit\Framework\TestCase;
use Xezilaires\Exception\MappingException;
use Xezilaires\Metadata\ArrayReference;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Mapping;
use Xezilaires\Metadata\Reference;
use Xezilaires\Test\Model\Product;

/**
 * @covers \Xezilaires\Metadata\Mapping
 *
 * @internal
 *
 * @small
 */
final class MappingTest extends TestCase
{
    /**
     * @uses \Xezilaires\Metadata\ColumnReference
     */
    public function testMappingHasDefaultOptions(): void
    {
        $mapping = new Mapping(Product::class, ['name' => new ColumnReference('A')]);

        self::assertSame(1, $mapping->getOption('start'));
        self::assertNull($mapping->getOption('end'));
        self::assertNull($mapping->getOption('header'));
        self::assertFalse($mapping->getOption('reverse'));
    }

    /**
     * @dataProvider provideCanCreateValidMappingCases
     *
     * @param class-string                                  $className
     * @param array<string, \Xezilaires\Metadata\Reference> $columns
     * @param null|array<string, null|bool|int|string>      $options
     */
    public function testCanCreateValidMapping(string $className, array $columns, ?array $options = null): void
    {
        $mapping = new Mapping($className, $columns, $options);

        self::assertSame($className, $mapping->getClassName());
        self::assertSame($columns, $mapping->getReferences());

        if ($options !== null) {
            foreach ($options as $option => $value) {
                self::assertSame($value, $mapping->getOption($option));
            }
        }
    }

    /**
     * @dataProvider   provideCannotCreateInvalidMappingCases
     *
     * @psalm-suppress MixedArgumentTypeCoercion intentionally testing invalid mappings
     *
     * @param array<array-key, mixed>                  $columns
     * @param null|array<string, null|bool|int|string> $options
     */
    public function testCannotCreateInvalidMapping(
        string $exceptionMessage,
        string $className,
        array $columns,
        ?array $options = null
    ): void {
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage($exceptionMessage);

        /** @phpstan-ignore-next-line */
        new Mapping($className, $columns, $options);
    }

    /**
     * @return iterable<int, array{0: class-string, 1: array{name: Reference}, 2?: array{header: int, reverse?: bool, start?: int}}>
     */
    public function provideCanCreateValidMappingCases(): iterable
    {
        yield [
            Product::class,
            ['name' => new ArrayReference([new ColumnReference('A'), new HeaderReference('Price')])],
        ];
        yield [Product::class, ['name' => new ColumnReference('A')]];
        yield [Product::class, ['name' => new HeaderReference('Name')], ['header' => 1]];
        yield [
            Product::class,
            ['name' => new HeaderReference('Name')],
            ['start' => 2, 'header' => 4, 'reverse' => true],
        ];
    }

    /**
     * @return iterable<int, array{0: string, 1: string, 2: array<array-key, mixed>, 3?: array<string, null|bool|int|string>}>
     */
    public function provideCannotCreateInvalidMappingCases(): iterable
    {
        yield ['Invalid class "foo"', 'foo', ['name' => new ColumnReference('A')]];
        yield ['Invalid mapping, no references specified', Product::class, []];
        yield ['Invalid property name "0"', Product::class, [new ColumnReference('A')]];
        yield ['Invalid reference "aaa"', Product::class, ['aaa' => 123]];
        yield [
            'When using HeaderReference, "header" option is required',
            Product::class,
            ['name' => new HeaderReference('Name')],
        ];
        yield [
            'The option "start" with value "yes" is expected to be of type "int"',
            Product::class,
            ['name' => new ColumnReference('A')],
            ['start' => 'yes'],
        ];
        yield [
            'The option "end" with value "yes" is expected to be of type "int"',
            Product::class,
            ['name' => new ColumnReference('A')],
            ['end' => 'yes'],
        ];
        yield [
            'The option "header" with value "yes" is expected to be of type "int"',
            Product::class,
            ['name' => new ColumnReference('A')],
            ['header' => 'yes'],
        ];
        yield [
            'The option "reverse" with value "yes" is expected to be of type "bool"',
            Product::class,
            ['name' => new ColumnReference('A')],
            ['reverse' => 'yes'],
        ];
    }
}
