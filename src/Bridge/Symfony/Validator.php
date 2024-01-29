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

namespace Xezilaires\Bridge\Symfony;

use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class Validator implements \Xezilaires\Validator
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function validate(object $object): ConstraintViolationListInterface
    {
        return $this->validator->validate($object);
    }
}
