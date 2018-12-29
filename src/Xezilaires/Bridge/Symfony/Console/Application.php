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

namespace Xezilaires\Bridge\Symfony\Console;

use Symfony\Bundle\FrameworkBundle\Console\Application as BaseApplication;
use Symfony\Component\HttpKernel\KernelInterface;

final class Application extends BaseApplication
{
    private const APP_NAME = '
 __   __        _ _       _
 \ \ / /       (_) |     (_)              
  \ V / ___ _____| | __ _ _ _ __ ___  ___ 
   > < / _ \_  / | |/ _` | | \'__/ _ \/ __|
  / . \  __// /| | | (_| | | | |  __/\__ \
 /_/ \_\___/___|_|_|\__,_|_|_|  \___||___/ ';

    private const APP_VERSION = '0.1.0';

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->setName(self::APP_NAME);
        $this->setVersion(self::APP_VERSION);
    }
}
