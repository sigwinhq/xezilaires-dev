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

namespace Xezilaires\Bridge\Symfony\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Xezilaires\Bridge\Symfony\Command\SerializeCommand;

/**
 * @internal
 */
final class RegisterCommandsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        /** @var array<string, array<string, string>> $commands */
        $commands = $container->findTaggedServiceIds('console.command');
        foreach ($commands as $id => $arguments) {
            $definition = $container->getDefinition($id);

            $className = $definition->getClass();
            if (null === $className) {
                continue;
            }

            if (0 !== mb_strpos($className, 'Xezilaires')) {
                $container->removeDefinition($id);
            }

            if ($className === SerializeCommand::class) {
                $arguments = $definition->getArguments();

                $definition = new Definition($className);
                $definition->setArguments($arguments);
                $definition->addTag('console.command', [
                    'command' => 'serialize',
                ]);
                $container->addDefinitions([
                    $id => $definition,
                ]);
            }
        }
    }
}
