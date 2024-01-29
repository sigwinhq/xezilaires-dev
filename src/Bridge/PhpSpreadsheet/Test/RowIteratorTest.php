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

namespace Xezilaires\Bridge\PhpSpreadsheet\Test;

use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator as PhpspreadsheetRowIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;
use Xezilaires\Bridge\PhpSpreadsheet\RowIterator;

/**
 * @covers \Xezilaires\Bridge\PhpSpreadsheet\RowIterator
 *
 * @internal
 *
 * @small
 */
#[\PHPUnit\Framework\Attributes\Small]
#[\PHPUnit\Framework\Attributes\CoversClass(RowIterator::class)]
final class RowIteratorTest extends TestCase
{
    public function testCanPerformValidCorrectly(): void
    {
        $iterator = new RowIterator($this->mockIterator([
            'valid' => ['count' => 1, 'params' => null, 'return' => true],
            'current' => ['count' => 0, 'params' => null],
            'prev' => ['count' => 0, 'params' => null],
            'next' => ['count' => 0, 'params' => null],
        ]));
        $iterator->valid();
    }

    public function testCanPerformCurrentCorrectly(): void
    {
        $iterator = new RowIterator($this->mockIterator([
            'valid' => ['count' => 0, 'params' => null, 'return' => false],
            'current' => ['count' => 1, 'params' => null, 'return' => new Row(new Worksheet())],
            'prev' => ['count' => 0, 'params' => null],
            'next' => ['count' => 0, 'params' => null],
        ]));
        $iterator->current();
    }

    public function testCanPerformPreviousCorrectly(): void
    {
        $iterator = new RowIterator($this->mockIterator([
            'valid' => ['count' => 0, 'params' => null, 'return' => false],
            'current' => ['count' => 0, 'params' => null],
            'prev' => ['count' => 1, 'params' => null],
            'next' => ['count' => 0, 'params' => null],
        ]));
        $iterator->prev();
    }

    public function testCanPerformNextCorrectly(): void
    {
        $iterator = new RowIterator($this->mockIterator([
            'valid' => ['count' => 0, 'params' => null, 'return' => false],
            'current' => ['count' => 0, 'params' => null],
            'prev' => ['count' => 0, 'params' => null],
            'next' => ['count' => 1, 'params' => null],
        ]));
        $iterator->next();
    }

    /**
     * @param null|array<string, array<string, null|bool|int|object>> $counts
     */
    private function mockIterator(?array $counts = null): PhpspreadsheetRowIterator
    {
        $iterator = $this
            ->getMockBuilder(PhpspreadsheetRowIterator::class)
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
                    $mocker->willReturn($spec['return']);
                }
            }
        }

        return $iterator;
    }
}
