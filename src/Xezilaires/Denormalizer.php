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

interface Denormalizer
{
    /**
     * @param array<string, mixed> $data
     * @param class-string         $class
     * @param array<string, mixed> $context
     *
     * @throws Exception
     */
    public function denormalize(array $data, string $class, ?string $format = null, array $context = []): object;
}
