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
use Symfony\Component\Serializer\SerializerInterface;
use Xezilaires\Denormalizer;
use Xezilaires\Serializer;

final class ObjectSerializer implements Denormalizer, Serializer
{
    /**
     * @var SerializerInterface&DenormalizerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface&DenormalizerInterface $serializer
     */
    public function __construct($serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = []): object
    {
        return $this->serializer->denormalize($data, $class, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, $format, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }
}
