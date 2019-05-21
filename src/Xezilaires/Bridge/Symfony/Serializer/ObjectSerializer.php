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

use Xezilaires\Denormalizer;
use Xezilaires\Serializer;

final class ObjectSerializer extends \Symfony\Component\Serializer\Serializer implements Denormalizer, Serializer
{
}
