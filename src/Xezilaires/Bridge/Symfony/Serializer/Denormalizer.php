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

namespace Xezilaires\Bridge\Symfony\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Interface Denormalizer.
 */
interface Denormalizer extends DenormalizerInterface
{
    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed  $data    data to restore
     * @param string $class   the expected class to instantiate
     * @param string $format  format the given data was extracted from
     * @param array  $context options available to the denormalizer
     *
     * @throws Exception
     *
     * @return object
     */
    public function denormalize($data, $class, $format = null, array $context = []);
}
