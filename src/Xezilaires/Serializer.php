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

namespace Xezilaires;

interface Serializer
{
    /**
     * @param array<string, mixed> $context
     */
    public function serialize(object $data, string $format, array $context = []): string;
}
