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

use Symfony\Component\Console\Application as BaseApplication;
use Xezilaires\Bridge\Symfony\Command\SerializeCommand;

/**
 * Class Application.
 */
class Application extends BaseApplication
{
    private const APP_NAME = '
 __   __        _ _       _
 \ \ / /       (_) |     (_)              
  \ V / ___ _____| | __ _ _ _ __ ___  ___ 
   > < / _ \_  / | |/ _` | | \'__/ _ \/ __|
  / . \  __// /| | | (_| | | | |  __/\__ \
 /_/ \_\___/___|_|_|\__,_|_|_|  \___||___/ ';

    private const APP_VERSION = '0.1.0';

    public function __construct()
    {
        parent::__construct(self::APP_NAME, self::APP_VERSION);

        $this->addCommands([
            new SerializeCommand(),
        ]);
        $this->setDefaultCommand('xezilaires:serialize');
    }
}
