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

namespace Xezilaires\Test;

use Nyholm\NSA;
use PHPUnit\Framework\TestCase;
use Xezilaires\Denormalizer;
use Xezilaires\Exception\MappingException;
use Xezilaires\Iterator;
use Xezilaires\Metadata\ArrayReference;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Mapping;
use Xezilaires\ReverseIterator;
use Xezilaires\Spreadsheet;
use Xezilaires\SpreadsheetIterator;

/**
 * @covers \Xezilaires\Metadata\Mapping
 * @covers \Xezilaires\SpreadsheetIterator
 *
 * @uses \Xezilaires\Metadata\ArrayReference
 * @uses \Xezilaires\Metadata\ColumnReference
 * @uses \Xezilaires\Metadata\HeaderReference
 *
 * @internal
 *
 * @small
 */
#[\PHPUnit\Framework\Attributes\Small]
#[\PHPUnit\Framework\Attributes\CoversClass(Mapping::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(SpreadsheetIterator::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(ArrayReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(ColumnReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(HeaderReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(ReverseIterator::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Attribute\ArrayReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Attribute\ColumnReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Attribute\HeaderReference::class)]
final class SpreadsheetIteratorTest extends TestCase
{
    public function testCanPerformValidCorrectly(): void
    {
        $mapping = new Mapping(\stdClass::class, [
            'name' => new HeaderReference('Name'),
        ], ['header' => 1]);

        $iterator = new SpreadsheetIterator(
            $this->getMockBuilder(Spreadsheet::class)->getMock(),
            $mapping,
            $this->getMockBuilder(Denormalizer::class)->getMock()
        );
        NSA::setProperty($iterator, 'iterator', $this->mockIterator([
            'valid' => ['count' => 1, 'params' => null, 'return' => true],
            'current' => ['count' => 0, 'params' => null],
            'prev' => ['count' => 0, 'params' => null],
            'next' => ['count' => 0, 'params' => null],
        ]));
        $iterator->valid();
    }

    public function testCanPerformNextCorrectly(): void
    {
        $mapping = new Mapping(\stdClass::class, [
            'name' => new HeaderReference('Name'),
        ], ['header' => 1]);

        $iterator = new SpreadsheetIterator(
            $this->getMockBuilder(Spreadsheet::class)->getMock(),
            $mapping,
            $this->getMockBuilder(Denormalizer::class)->getMock()
        );
        NSA::setProperty($iterator, 'iterator', $this->mockIterator([
            'valid' => ['count' => 0, 'params' => null, 'return' => true],
            'current' => ['count' => 0, 'params' => null],
            'key' => ['count' => 2, 'params' => null, 'return' => [1, 2]],
            'prev' => ['count' => 0, 'params' => null],
            'next' => ['count' => 1, 'params' => null],
        ]));
        $key = $iterator->key();
        $iterator->next();

        self::assertGreaterThan($key, $iterator->key());
    }

    public function testCanPerformNextSequentially(): void
    {
        $mapping = new Mapping(\stdClass::class, [
            'name' => new HeaderReference('Name'),
        ], ['header' => 1, 'sequential' => true]);

        $iterator = new SpreadsheetIterator(
            $this->getMockBuilder(Spreadsheet::class)->getMock(),
            $mapping,
            $this->getMockBuilder(Denormalizer::class)->getMock()
        );
        NSA::setProperty($iterator, 'iterator', $this->mockIterator([
            'valid' => ['count' => 0, 'params' => null, 'return' => true],
            'current' => ['count' => 0, 'params' => null],
            'key' => ['count' => 0, 'params' => null],
            'prev' => ['count' => 0, 'params' => null],
            'next' => ['count' => 1, 'params' => null],
        ]));
        $key = $iterator->key();
        $iterator->next();

        self::assertGreaterThan($key, $iterator->key());
    }

    public function testCanPerformCurrentCorrectly(): void
    {
        $spreadsheet = $this
            ->getMockBuilder(Spreadsheet::class)
            ->getMock()
        ;
        $spreadsheet
            ->expects(self::once())
            ->method('getRow')
            ->willReturn(
                ['A' => 'One', 'B' => 'Two', 'C' => 'Three', 'D' => 'Four'],
            )
        ;
        $spreadsheet
            ->expects(self::once())
            ->method('getCurrentRow')
            ->willReturn(
                ['A' => 'Yes', 'B' => 'Nope', 'C' => 'Yeah', 'D' => 'Right'],
            )
        ;

        $denormalizer = $this
            ->getMockBuilder(Denormalizer::class)
            ->getMock()
        ;
        $denormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with(
                ['one' => 'Yes', 'two' => 'Nope', 'three' => ['Yeah', 'Right']],
            )
        ;

        $mapping = new Mapping(\stdClass::class, [
            'one' => new HeaderReference('One'),
            'two' => new ColumnReference('B'),
            'three' => new ArrayReference([new ColumnReference('C'), new HeaderReference('Four')]),
        ], ['header' => 1]);

        $iterator = new SpreadsheetIterator($spreadsheet, $mapping, $denormalizer);
        $iterator->current();
    }

    public function testWillOfferAnDidYouMeanForInvalidHeader(): void
    {
        $spreadsheet = $this
            ->getMockBuilder(Spreadsheet::class)
            ->getMock()
        ;
        $spreadsheet
            ->expects(self::once())
            ->method('getRow')
            ->with(1)
            ->willReturn(['Amen', 'Nope', 'Name'])
        ;

        $mapping = new Mapping(\stdClass::class, [
            'name' => new HeaderReference('Naem'),
        ], ['header' => 1]);

        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('Invalid header "Naem", did you mean "Name"?');

        $iterator = new SpreadsheetIterator($spreadsheet, $mapping, $this->getMockBuilder(Denormalizer::class)->getMock());
        $iterator->current();
    }

    public function testCannotLoadWithNestedArrayReference(): void
    {
        $mapping = new Mapping(\stdClass::class, [
            'name' => new ArrayReference([new ArrayReference([new ColumnReference('A')])]),
        ], ['header' => 1]);

        $this->expectException(MappingException::class);
        $this->expectExceptionMessage('Unexpected reference type');

        $iterator = new SpreadsheetIterator(
            $this->getMockBuilder(Spreadsheet::class)->getMock(),
            $mapping,
            $this->getMockBuilder(Denormalizer::class)->getMock()
        );
        $iterator->current();
    }

    /**
     * @param null|array<string, array<string, null|array<int>|bool|int>> $counts
     */
    private function mockIterator(?array $counts = null): Iterator
    {
        $iterator = $this
            ->getMockBuilder(Iterator::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if ($counts !== null) {
            foreach ($counts as $method => $spec) {
                /** @var int $count */
                $count = $spec['count'];

                $mocker = $iterator
                    ->expects(self::exactly($count))
                    ->method($method)
                    ->with(...(array) $spec['params'])
                ;

                if (isset($spec['return'])) {
                    if (\is_array($spec['return'])) {
                        $mocker->willReturnOnConsecutiveCalls(...$spec['return']);
                    } else {
                        $mocker->willReturn($spec['return']);
                    }
                }
            }
        }

        return $iterator;
    }
}
