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

namespace Xezilaires\Infrastructure\Symfony\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Xezilaires\Infrastructure\Symfony\Serializer\ObjectNormalizer;
use Xezilaires\Metadata\Annotation\AnnotationDriver;
use Xezilaires\PhpSpreadsheetIterator;

/**
 * Class SerializeCommand.
 */
class SerializeCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('xezilaires:serialize')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to file to process')
            ->addArgument('class', InputArgument::REQUIRED, 'Process the rows as class')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Format to export to', 'json');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        /** @var string $path */
        $path = $input->getArgument('path');
        /** @var string $class */
        $class = $input->getArgument('class');
        /** @var string $format */
        $format = $input->getOption('format');

        $driver = new AnnotationDriver();
        $mapping = $driver->getMetadataMapping($class);

        $iterator = new PhpSpreadsheetIterator(new \SplFileObject($path), $mapping);
        $serializer = new Serializer(
            [new ObjectNormalizer()],
            [new JsonEncode(JSON_PRETTY_PRINT), new XmlEncoder('xezilaires')]
        );
        $output->write($serializer->serialize($iterator, $format));

        return 0;
    }
}
