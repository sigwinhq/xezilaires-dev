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
         *
         * @psalm-suppress MixedArrayAccess
         */
        if (isset($data['all'][1])) {
            /**
             * @phpstan-ignore-next-line
             *
             * @psalm-suppress MixedArgument
             * @psalm-suppress MixedArrayAccess
             * @psalm-suppress MixedArrayAssignment
             */
            $data['all'][1] = $this->cast($data['all'][1]);
        }

        /**
         * @phpstan-ignore-next-line
         *
         * @psalm-suppress MixedArgument
         * @psalm-suppress MixedArrayAccess
         * @psalm-suppress MixedArrayAssignment
         */
        $data['price'] = $this->cast($data['price']);

        return (object) $data;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return $type === Product::class;
    }

    private function cast(string $price): float
    {
        return (float) filter_var($price, \FILTER_SANITIZE_NUMBER_FLOAT, \FILTER_FLAG_ALLOW_FRACTION);
    }
}
