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

/**
 * @template T of object
 */
interface Denormalizer
{
    /**
     * @param array<string, mixed> $data
     * @param class-string<T>      $class
     * @param array<string, mixed> $context
     *
     * @return T
     *
     * @throws Exception
     */
    public function denormalize(array $data, string $class, ?string $format = null, array $context = []): object;
}
