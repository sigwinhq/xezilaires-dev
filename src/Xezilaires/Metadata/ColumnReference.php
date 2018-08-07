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

namespace Xezilaires\Metadata;

/**
 * Class ColumnMapping.
 */
class ColumnReference implements Reference
{
    /**
     * @var string
     */
    private $column;

    /**
     * @param string $column
     */
    public function __construct(string $column)
    {
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->column;
    }
}
