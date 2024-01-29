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

use Symfony\Component\Serializer\Attribute as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Xezilaires\Attribute as XLS;

#[XLS\Options(start: 2, header: 1)]
final class Product
{
    #[Serializer\Groups('array')]
    #[XLS\ArrayReference([
        new XLS\ColumnReference(column: 'A'),
        new XLS\HeaderReference(header: 'Price USD'),
    ])]
    public array $all;

    #[Assert\NotBlank]
    #[Serializer\Groups(['column', 'product'])]
    #[XLS\ColumnReference(column: 'A')]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(0)]
    #[Serializer\Groups(['header', 'product'])]
    #[XLS\HeaderReference(header: 'Price USD')]
    public float $price;
}
