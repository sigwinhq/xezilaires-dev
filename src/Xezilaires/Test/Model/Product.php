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

use Xezilaires\Annotation as XLS;

/**
 * @XLS\Options(header=1, start=2)
 */
final class Product
{
    /**
     * @var array<float|string>
     *
     * @XLS\ArrayReference({
     *     @XLS\ColumnReference(column="A"),
     *     @XLS\HeaderReference(header="Price USD")
     * })
     */
    public $all;

    /**
     * @var string
     *
     * @XLS\ColumnReference(column="A")
     */
    public $name;

    /**
     * @var float|string
     *
     * @XLS\HeaderReference(header="Price USD")
     */
    public $price;
}
