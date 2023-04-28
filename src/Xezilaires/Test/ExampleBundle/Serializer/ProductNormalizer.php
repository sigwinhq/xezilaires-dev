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

namespace Xezilaires\Test\ExampleBundle\Serializer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Xezilaires\Test\Model\Product;

final class ProductNormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): object
    {
        /**
         * @phpstan-ignore-next-line
         * @psalm-suppress MixedArrayAssignment
         */
        $data['price'] = (float) $data;

        return (object) $data;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return $type === Product::class;
    }
}
