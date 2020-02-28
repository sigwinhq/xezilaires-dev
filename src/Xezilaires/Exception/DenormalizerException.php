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

namespace Xezilaires\Exception;

use Xezilaires\Exception;

final class DenormalizerException extends \InvalidArgumentException implements Exception
{
    public static function denormalizationFailed(Exception $exception): self
    {
        return new self('Denormalization failed', 0, $exception);
    }
}
