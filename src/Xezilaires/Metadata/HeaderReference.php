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

final class HeaderReference implements Reference
{
    /**
     * @var string
     */
    private $header;

    public function __construct(string $header)
    {
        $this->header = $header;
    }

    public function getReference(): string
    {
        return $this->header;
    }
}
