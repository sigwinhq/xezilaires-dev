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

namespace Xezilaires\Bridge\Spout\Test;

use OpenSpout\Reader\RowIteratorInterface;
use PHPUnit\Framework\TestCase;
use Xezilaires\Bridge\Spout\RowIterator;

/**
 * @covers \Xezilaires\Bridge\Spout\RowIterator
 *
 * @internal
 *
 * @small
 */
#[\PHPUnit\Framework\Attributes\Small]
#[\PHPUnit\Framework\Attributes\CoversClass(RowIterator::class)]
final class RowIteratorTest extends TestCase
{
    /**
     * @return list<array{0: int, 1: int, 2: int, 3: array{rewind: int, next: int, valid: int}}>
     */
    public static function provideCanSeekProperlyCases(): iterable
    {
        return [
            [2, 2, 2, ['rewind' => 1, 'next' => 0, 'valid' => 0]],
            [2, 2, 4, ['rewind' => 1, 'next' => 2, 'valid' => 0]],
            [2, 5, 3, ['rewind' => 2, 'next' => 2, 'valid' => 0]],
        ];
    }

    /**
     * @return list<array{0: int, 1: int, 2: int, 3: array{rewind: int, next: int}, 4: array{valid: list<bool>}}>
     */
    public static function provideCanDetermineHighestRowProperlyCases(): iterable
    {
        return [
            [1, 1, 2, ['rewind' => 2, 'next' => 2], ['valid' => [true, true, false]]],
        ];
    }

    /**
     * @param array<string, int> $counts
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideCanSeekProperlyCases')]
    public function testCanSeekProperly(int $startRow, int $currentRow, int $seekToRow, array $counts): void
    {
        $iterator = new RowIterator($this->mockIterator($currentRow, $counts), $startRow);
        $iterator->seek($seekToRow);
    }

    /**
     * @param array<string, int>   $counts
     * @param array<string, array> $calls
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('provideCanDetermineHighestRowProperlyCases')]
    public function testCanDetermineHighestRowProperly(int $startRow, int $currentRow, int $highestRow, array $counts, array $calls): void
    {
        // Spout bug workaround: we're calling prev() to realign the counter
        ++$counts['rewind'];

        $iterator = new RowIterator($this->mockIterator($currentRow, $counts, $calls), $startRow);
        self::assertSame($highestRow, $iterator->getHighestRow());
    }

    /**
     * @param null|array<string, int>   $counts
     * @param null|array<string, array> $calls
     */
    private function mockIterator(int $currentRow, ?array $counts = null, ?array $calls = null): RowIteratorInterface
    {
        $iterator = $this
            ->getMockBuilder(RowIteratorInterface::class)
            ->getMock()
        ;

        $iterator
            ->method('key')
            ->willReturn($currentRow)
        ;

        if ($counts !== null) {
            foreach ($counts as $method => $count) {
                $iterator
                    ->expects(self::exactly($count))
                    ->method($method)
                ;
            }
        }

        if ($calls !== null) {
            foreach ($calls as $method => $return) {
                $iterator
                    ->expects(self::exactly(\count($return)))
                    ->method($method)
                    ->willReturnOnConsecutiveCalls(...$return)
                ;
            }
        }

        return $iterator;
    }
}
