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

namespace Xezilaires\Bridge\Symfony\Console;

use Composer\InstalledVersions;
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

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->setName(self::APP_NAME);
        if (class_exists(InstalledVersions::class)) {
            $version = InstalledVersions::getPrettyVersion('sigwin/xezilaires-symfony');
            $this->setVersion($version ?? 'N/A');
        } else {
            $this->setVersion('N/A');
        }
    }

    public function getLongVersion(): string
    {
        return sprintf('%s <info>%s</info>', $this->getName(), $this->getVersion());
    }
}
