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
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Xezilaires\IteratorFactory;
use Xezilaires\Metadata\Annotation\AnnotationDriver;
use Xezilaires\Validator;

final class ValidateCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'xezilaires:validate';

    /**
     * @var IteratorFactory
     */
    private $iteratorFactory;

    /**
     * @var Validator
     */
    private $validator;

    public function __construct(IteratorFactory $iteratorFactory, Validator $validator)
    {
        parent::__construct(self::$defaultName);

        $this->setDescription('Validate the input file');

        $this->iteratorFactory = $iteratorFactory;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('xezilaires:serialize')
            ->addArgument('class', InputArgument::REQUIRED, 'Process the rows as class')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to file to process')
            ->addOption('bundle', 'b', InputOption::VALUE_REQUIRED, 'Custom project-specific bundle to load');
    }

    /**
     * @throws \ReflectionException
     * @throws \RuntimeException
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var string $class
         * @psalm-var class-string $class
         */
        $class = $input->getArgument('class');
        /** @var string $path */
        $path = $input->getArgument('path');

        $driver = new AnnotationDriver();
        $iterator = $this->iteratorFactory->fromFile(
            new \SplFileObject($path),
            $driver->getMetadataMapping($class)
        );

        $style = new SymfonyStyle($input, $output);
        $style->title('Xezilaires validate');

        $total = 0;
        $invalid = 0;
        $progress = $style->createProgressBar();
        foreach ($iterator as $item) {
            ++$total;
            $violations = $this->validator->validate($item);
            if ($violations->count() > 0) {
                ++$invalid;
                $progress->clear();

                $style->section(sprintf('Row %1$d', $iterator->key()));

                /** @var ConstraintViolationInterface $violation */
                foreach ($violations as $violation) {
                    /** @var string $message */
                    $message = $violation->getMessage();

                    $style->error(sprintf('%1$s: %2$s', $violation->getPropertyPath(), $message));
                }
            }

            $progress->advance();
        }
        $progress->clear();

        if ($invalid > 0) {
            $style->error(sprintf('Processed %1$d rows, %2$d had errors', $total, $invalid));

            return 1;
        }

        $style->success(sprintf('Processed %1$d rows', $total));

        return 0;
    }
}
