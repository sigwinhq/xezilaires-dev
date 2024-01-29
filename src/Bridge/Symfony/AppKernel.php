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

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Xezilaires\Bridge\Symfony\DependencyInjection\CompilerPass\RegisterCommandsCompilerPass;

/**
 * @internal
 */
final class AppKernel extends Kernel
{
    /**
     * @param BundleInterface[] $customBundles
     */
    public function __construct(private readonly array $customBundles)
    {
        parent::__construct('prod', false);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return array_merge([
            new FrameworkBundle(),
            new XezilairesBundle(),
        ], $this->customBundles);
    }

    /**
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/Resources/bin-config/services.xml');
    }

    public function getCacheDir(): string
    {
        return '/dev/shm/'.sha1(__DIR__.time()).'/logs';
    }

    public function getLogDir(): string
    {
        return '/dev/shm/'.sha1(__DIR__.time()).'/logs';
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RegisterCommandsCompilerPass());
    }
}
