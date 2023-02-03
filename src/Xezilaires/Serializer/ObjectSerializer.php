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

namespace Xezilaires\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Xezilaires\Denormalizer;
use Xezilaires\Serializer;

/**
 * @template T of object
 *
 * @implements Denormalizer<T>
 */
final class ObjectSerializer implements Denormalizer, Serializer
{
    /**
     * @var DenormalizerInterface&SerializerInterface
     */
    private $serializer;

    /**
     * @param DenormalizerInterface&SerializerInterface $serializer
     */
    public function __construct($serializer)
    {
        $this->serializer = $serializer;
    }

    public function denormalize(array $data, string $class, ?string $format = null, array $context = []): object
    {
        /**
         * @phpstan-var T $object
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $object = $this->serializer->denormalize($data, $class, $format, $context);

        return $object;
    }

    public function serialize(object $data, string $format, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }
}
