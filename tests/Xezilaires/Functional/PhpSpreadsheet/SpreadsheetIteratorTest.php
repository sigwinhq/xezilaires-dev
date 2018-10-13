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

namespace Xezilaires\Test\Functional\PhpSpreadsheet;

use Xezilaires\Bridge\PhpSpreadsheet\Spreadsheet;
use Xezilaires\Spreadsheet as SpreadsheetInterface;
use Xezilaires\Test\Functional\FunctionalTestCase;

/**
 * @covers \Xezilaires\SpreadsheetIterator
 * @covers \Xezilaires\Bridge\PhpSpreadsheet\Spreadsheet
 * @covers \Xezilaires\Bridge\PhpSpreadsheet\RowIterator
 *
 * @uses \Xezilaires\Metadata\ArrayReference
 * @uses \Xezilaires\Metadata\ColumnReference
 * @uses \Xezilaires\Metadata\HeaderReference
 * @uses \Xezilaires\Metadata\Mapping
 *
 * @group functional
 * @group phpspreadsheet
 */
class SpreadsheetIteratorTest extends FunctionalTestCase
{
    /**
     * @param \SplFileObject $file
     *
     * @return Spreadsheet
     */
    protected function getSpreadsheet(\SplFileObject $file): SpreadsheetInterface
    {
        return new Spreadsheet($file);
    }
}
