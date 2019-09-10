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

namespace Xezilaires\Test\Bridge\Spout;

use Box\Spout\Reader\IteratorInterface;
use PHPUnit\Framework\TestCase;
use Xezilaires\Bridge\Spout\RowIterator;

/**
 * @covers \Xezilaires\Bridge\Spout\RowIterator
 *
 * @internal
 *
 * @small
 */
final class RowIteratorTest extends TestCase
{
    public function seekProvider(): array
    {
        return [
            [2, 2, 2, ['rewind' => 1, 'next' => 0, 'valid' => 0]],
            [2, 2, 4, ['rewind' => 1, 'next' => 2, 'valid' => 0]],
            [2, 5, 3, ['rewind' => 2, 'next' => 2, 'valid' => 0]],
        ];
    }

    public function getHighestRowProvider(): array
    {
        return [
            [1, 1, 2, ['rewind' => 2, 'next' => 2], ['valid' => [true, true, false]]],
        ];
    }

    /**
     * @dataProvider seekProvider
     *
     * @param array<string, int> $counts
     */
    public function testCanSeekProperly(int $startRow, int $currentRow, int $seekToRow, array $counts): void
    {
        $iterator = new RowIterator($this->mockIterator($currentRow, $counts), $startRow);
        $iterator->seek($seekToRow);
    }

    /**
     * @dataProvider getHighestRowProvider
     *
     * @param array<string, int>   $counts
     * @param array<string, array> $calls
     */
    public function testCanDetermineHighestRowProperly(int $startRow, int $currentRow, int $highestRow, array $counts, array $calls): void
    {
        // Spout bug workaround: we're calling prev() to realign the counter
        ++$counts['rewind'];

        $iterator = new RowIterator($this->mockIterator($currentRow, $counts, $calls), $startRow);
        static::assertSame($highestRow, $iterator->getHighestRow());
    }

    /**
     * @param null|array<string, int>   $counts
     * @param null|array<string, array> $calls
     */
    private function mockIterator(int $currentRow, ?array $counts = null, ?array $calls = null): IteratorInterface
    {
        $iterator = $this
            ->getMockBuilder(IteratorInterface::class)
            ->getMock();

        $iterator
            ->method('key')
            ->willReturn($currentRow);

        if (null !== $counts) {
            foreach ($counts as $method => $count) {
                $iterator
                    ->expects(static::exactly($count))
                    ->method($method);
            }
        }

        if (null !== $calls) {
            foreach ($calls as $method => $return) {
                $iterator
                    ->expects(static::exactly(\count($return)))
                    ->method($method)
                    ->willReturnOnConsecutiveCalls(...$return);
            }
        }

        return $iterator;
    }
}
