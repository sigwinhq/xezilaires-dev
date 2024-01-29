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

namespace Xezilaires\Test\Model;

use Xezilaires\Attribute as XLS;

#[XLS\Options(header: 1, start: 2)]
final class ProductWithAttributes
{
    #[XLS\ColumnReference(column: 'A')]
    public string $name;

    #[XLS\HeaderReference(header: 'Price USD')]
    public float $price;
}
