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

namespace Xezilaires;

use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\HeaderReference;
use Xezilaires\Metadata\Reference;
use Xezilaires\Metadata\ReferenceResolver;

/**
 * Class PhpSpreadsheetHeaderReferenceResolver.
 */
class PhpSpreadsheetHeaderReferenceResolver implements ReferenceResolver
{
    /**
     * @var PhpSpreadsheetIterator
     */
    private $iterator;

    /**
     * @param PhpSpreadsheetIterator $iterator
     */
    public function __construct(PhpSpreadsheetIterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * @param Reference $reference
     *
     * @return string
     */
    public function resolve(Reference $reference): string
    {
        if ($reference instanceof ColumnReference) {
            return $reference->getColumn();
        }

        if ($reference instanceof HeaderReference) {
            return $this->iterator->getColumnByHeader($reference->getReference());
        }

        throw new \InvalidArgumentException('Invalid reference given');
    }
}
