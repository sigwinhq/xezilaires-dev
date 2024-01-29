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

namespace Xezilaires\Bridge\PhpSpreadsheet\Test\Functional;

use Xezilaires\Bridge\PhpSpreadsheet\Spreadsheet;
use Xezilaires\Metadata\Attribute\AttributeDriver;
use Xezilaires\ReverseIterator;
use Xezilaires\Spreadsheet as SpreadsheetInterface;
use Xezilaires\Test\Functional\FunctionalTestCase;

/**
 * @covers \Xezilaires\Bridge\PhpSpreadsheet\RowIterator
 * @covers \Xezilaires\Bridge\PhpSpreadsheet\Spreadsheet
 * @covers \Xezilaires\SpreadsheetIterator
 *
 * @uses \Xezilaires\Metadata\ArrayReference
 * @uses \Xezilaires\Metadata\ColumnReference
 * @uses \Xezilaires\Metadata\HeaderReference
 * @uses \Xezilaires\Metadata\Mapping
 * @uses \Xezilaires\Serializer\ObjectSerializer
 *
 * @internal
 *
 * @small
 */
#[\PHPUnit\Framework\Attributes\Small]
#[\PHPUnit\Framework\Attributes\CoversClass(\Xezilaires\Bridge\PhpSpreadsheet\RowIterator::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(Spreadsheet::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\Xezilaires\SpreadsheetIterator::class)]
#[\PHPUnit\Framework\Attributes\Group('functional')]
#[\PHPUnit\Framework\Attributes\Group('phpspreadsheet')]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Metadata\ArrayReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Metadata\ColumnReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Metadata\HeaderReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Metadata\Mapping::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Serializer\ObjectSerializer::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Attribute\ArrayReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Attribute\ColumnReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Attribute\HeaderReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(ReverseIterator::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(AttributeDriver::class)]
final class SpreadsheetIteratorTest extends FunctionalTestCase
{
    /**
     * @return Spreadsheet
     */
    protected function getSpreadsheet(\SplFileObject $file): SpreadsheetInterface
    {
        return new Spreadsheet($file);
    }
}
