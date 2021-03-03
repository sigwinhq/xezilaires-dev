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

namespace Xezilaires\Metadata;

final class ArrayReference implements Reference
{
    /**
     * @var Reference[]
     */
    private $references;

    /**
     * @param Reference[] $references
     */
    public function __construct(array $references)
    {
        $this->references = $references;
    }

    /**
     * @return Reference[]
     */
    public function getReference(): array
    {
        return $this->references;
    }
}
