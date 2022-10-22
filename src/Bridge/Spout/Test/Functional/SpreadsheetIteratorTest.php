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
use Xezilaires\Spreadsheet as SpreadsheetInterface;
use Xezilaires\Test\Functional\FunctionalTestCase;

/**
 * @covers \Xezilaires\Bridge\Spout\RowIterator
 * @covers \Xezilaires\Bridge\Spout\Spreadsheet
 * @covers \Xezilaires\SpreadsheetIterator
 *
 * @uses \Xezilaires\Metadata\ArrayReference
 * @uses \Xezilaires\Metadata\ColumnReference
 * @uses \Xezilaires\Metadata\HeaderReference
 * @uses \Xezilaires\Metadata\Mapping
 * @uses \Xezilaires\Serializer\ObjectSerializer
 *
 * @group functional
 * @group spout
 *
 * @internal
 *
 * @small
 */
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
