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
 * Class HeaderReference.
 */
class HeaderReference implements Reference
{
    /**
     * @var string
     */
    private $header;

    /**
     * @param string $header
     */
    public function __construct(string $header)
    {
        $this->header = $header;
    }

    /**
     * @return string
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * {@inheritdoc}
     */
    public function getReference(): string
    {
        return $this->getHeader();
    }
}
