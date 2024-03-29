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

namespace Xezilaires\Bridge\Symfony\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Xezilaires\IteratorFactory;
use Xezilaires\Metadata\Attribute\AttributeDriver;
use Xezilaires\Serializer;

#[AsCommand(
    name: 'xezilaires:serialize',
    description: 'Serialize Excel files into JSON, XML, CSV',
)]
final class SerializeCommand extends Command
{
    public function __construct(private readonly IteratorFactory $iteratorFactory, private readonly Serializer $serializer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('class', InputArgument::REQUIRED, 'Process the rows as class')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to file to process')
            ->addOption('bundle', 'B', InputOption::VALUE_REQUIRED, 'Custom project-specific bundle to load')
            ->addOption('format', 'F', InputOption::VALUE_OPTIONAL, 'Format to export to', 'json')
            ->addOption('reverse', 'R', InputOption::VALUE_NONE, 'Iterate in reverse')
            ->addOption('group', 'G', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Group(s) to serialize')
            ->addOption('xml-root', null, InputOption::VALUE_OPTIONAL, 'Name of root node in XML format', 'root')
        ;
    }

    /**
     * @throws \ReflectionException
     * @throws \RuntimeException
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var class-string $class
         */
        $class = $input->getArgument('class');
        /**
         * @phpstan-var string $path
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $path = $input->getArgument('path');
        /**
         * @phpstan-var null|string $format
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $format = $input->getOption('format');
        /**
         * @phpstan-var bool $reverse
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $reverse = $input->getOption('reverse');
        /**
         * @phpstan-var array<string> $groups
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $groups = $input->getOption('group');
        /**
         * @phpstan-var null|string $xmlRoot
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $xmlRoot = $input->getOption('xml-root');

        if ($format === null) {
            throw new \RuntimeException('Format is required');
        }

        $context = array_filter([
            'groups' => $groups,
        ]);
        switch ($format) {
            case 'csv':
                if (false === class_exists(CsvEncoder::class)) {
                    throw new \RuntimeException('CSV format is only available with Symfony 4.0+');
                }
                break;
            case 'json':
                $context['json_encode_options'] = \JSON_PRETTY_PRINT;
                break;
            case 'xml':
                if ($xmlRoot === null) {
                    throw new \RuntimeException('XML root node name cannot be empty if XML format requested');
                }
                $context['xml_root_node_name'] = $xmlRoot;
                break;
        }

        $driver = new AttributeDriver();
        $iterator = $this->iteratorFactory->fromFile(
            new \SplFileObject($path),
            $driver->getMetadataMapping($class, ['reverse' => $reverse, 'sequential' => true])
        );
        $output->write($this->serializer->serialize($iterator, $format, $context));

        return 0;
    }
}
