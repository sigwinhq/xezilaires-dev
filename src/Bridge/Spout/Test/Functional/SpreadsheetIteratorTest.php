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

namespace Xezilaires\Bridge\Spout\Test\Functional;

use Xezilaires\Bridge\Spout\Spreadsheet;
use Xezilaires\Metadata\Attribute\AttributeDriver;
use Xezilaires\ReverseIterator;
use Xezilaires\Spreadsheet as SpreadsheetInterface;
use Xezilaires\Test\Functional\FunctionalTestCase;

/**
 * @internal
 *
 * @coversNothing
 *
 * @small
 */
#[\PHPUnit\Framework\Attributes\Small]
#[\PHPUnit\Framework\Attributes\CoversClass(\Xezilaires\Bridge\Spout\RowIterator::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(Spreadsheet::class)]
#[\PHPUnit\Framework\Attributes\CoversClass(\Xezilaires\SpreadsheetIterator::class)]
#[\PHPUnit\Framework\Attributes\Group('functional')]
#[\PHPUnit\Framework\Attributes\Group('spout')]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Metadata\ArrayReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Metadata\ColumnReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Metadata\HeaderReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Metadata\Mapping::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Serializer\ObjectSerializer::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Attribute\ArrayReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Attribute\ColumnReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Attribute\HeaderReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(AttributeDriver::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(ReverseIterator::class)]
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
