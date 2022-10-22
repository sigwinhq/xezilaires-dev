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

namespace Xezilaires\Bridge\Symfony\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

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
        foreach (array_keys($commands) as $id) {
            $definition = $container->getDefinition($id);

            $className = $definition->getClass();
            if ($className === null) {
                continue;
            }

            if (0 !== mb_strpos($className, 'Xezilaires')) {
                $container->removeDefinition($id);

                continue;
            }

            $arguments = $definition->getArguments();

            /** @var array{"console.command": array} $all */
            $all = $definition->getTags();

            /** @var array{command: string} $tags */
            $tags = current($all['console.command']);

            // TODO: error handling
            $command = explode(':', $tags['command'])[1];

            $definition = new Definition($className);
            $definition->setArguments($arguments);
            $definition->addTag('console.command', ['command' => $command]);
            $container->setDefinition($id, $definition);
        }
    }
}
