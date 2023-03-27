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

use Symfony\Component\Serializer\Annotation\Groups;
use Xezilaires\Annotation as XLS;

/**
 * @XLS\Options(header=1, start=2)
 */
final class Product
{
    /**
     * @Groups({"array"})
     *
     * @XLS\ArrayReference({
     *
     *     @XLS\ColumnReference(column="A"),
     *
     *     @XLS\HeaderReference(header="Price USD")
     * })
     */
    public array $all;

    /**
     * @Groups({"column", "product"})
     *
     * @XLS\ColumnReference(column="A")
     */
    public string $name;

    /**
     * @Groups({"header", "product"})
     *
     * @XLS\HeaderReference(header="Price USD")
     */
    public float $price;
}
