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

namespace Xezilaires\Bridge\Symfony\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Xezilaires\Bridge\PhpSpreadsheet\Spreadsheet;
use Xezilaires\Bridge\Symfony\Serializer\ObjectNormalizer;
use Xezilaires\Metadata\Annotation\AnnotationDriver;
use Xezilaires\SpreadsheetIterator;

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
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Format to export to', 'json')
            ->addOption('reverse', 'r', InputOption::VALUE_NONE, 'Iterate in reverse')
            ->addOption('xml-root', null, InputOption::VALUE_OPTIONAL, 'Name of root node in XML format', 'root');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \RuntimeException
     *
     * @return null|int
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        /** @var string $path */
        $path = $input->getArgument('path');
        /** @var string $class */
        $class = $input->getArgument('class');
        /** @var null|string $format */
        $format = $input->getOption('format');
        /** @var bool $reverse */
        $reverse = $input->getOption('reverse');
        /** @var null|string $xmlRoot */
        $xmlRoot = $input->getOption('xml-root');

        if (null === $format) {
            throw new \RuntimeException('Format is required');
        }

        $normalizers = [new ObjectNormalizer()];
        $encoders = [new JsonEncode(JSON_PRETTY_PRINT)];

        if ('xml' === $format) {
            if (null === $xmlRoot) {
                throw new \RuntimeException('XML root node name cannot be empty if XML format requested');
            }

            $encoders[] = new XmlEncoder($xmlRoot);
        }

        if (true === class_exists(CsvEncoder::class)) {
            $encoders[] = new CsvEncoder();
        } elseif ('csv' === $format) {
            throw new \RuntimeException('CSV format is only available with Symfony 4.0+');
        }

        $driver = new AnnotationDriver();
        $iterator = new SpreadsheetIterator(
            new Spreadsheet(new \SplFileObject($path)),
            $driver->getMetadataMapping($class, ['reverse' => $reverse])
        );
        $serializer = new Serializer($normalizers, $encoders);
        $output->write($serializer->serialize($iterator, $format));

        return 0;
    }
}
