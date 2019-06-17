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

interface Serializer
{
    /**
     * @param object $data
     * @param string $format
     */
    public function serialize($data, $format, array $context = []): string;
}
