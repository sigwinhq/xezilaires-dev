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

namespace Xezilaires\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class XezilairesExtension extends Extension
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('validator.xml');
        $loader->load('serializer.xml');
        $loader->load('iterator.xml');
        $loader->load('application.xml');

        /** @var array<class-string<\Xezilaires\Spreadsheet>> $spreadsheetClasses */
        $spreadsheetClasses = [];
        if (class_exists(\Xezilaires\Bridge\Spout\Spreadsheet::class)) {
            $spreadsheetClasses[] = \Xezilaires\Bridge\Spout\Spreadsheet::class;
        }
        if (class_exists(\Xezilaires\Bridge\PhpSpreadsheet\Spreadsheet::class)) {
            $spreadsheetClasses[] = \Xezilaires\Bridge\PhpSpreadsheet\Spreadsheet::class;
        }

        $container->setParameter('xezilaires.spreadsheet_classes', $spreadsheetClasses);
    }
}
