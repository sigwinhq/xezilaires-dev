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

use Xezilaires\Bridge\Symfony\Serializer\Exception;

interface Denormalizer
{
    /**
     * @param array  $data
     * @param string $class
     * @param string $format
     *
     * @throws Exception
     */
    public function denormalize($data, $class, $format = null, array $context = []): object;
}
