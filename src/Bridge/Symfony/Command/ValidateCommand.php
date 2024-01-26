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
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Xezilaires\IteratorFactory;
use Xezilaires\Metadata\Annotation\AnnotationDriver;
use Xezilaires\Validator;

#[AsCommand(
    name: 'xezilaires:validate',
    description: 'Validate the input file',
)]
final class ValidateCommand extends Command
{
    private IteratorFactory $iteratorFactory;

    private Validator $validator;

    public function __construct(IteratorFactory $iteratorFactory, Validator $validator)
    {
        parent::__construct();

        $this->iteratorFactory = $iteratorFactory;
        $this->validator = $validator;
    }

    protected function configure(): void
    {
        $this
            ->setName('xezilaires:serialize')
            ->addArgument('class', InputArgument::REQUIRED, 'Process the rows as class')
            ->addArgument('paths', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Path(s) to file(s) to process')
            ->addOption('bundle', 'B', InputOption::VALUE_REQUIRED, 'Custom project-specific bundle to load')
            ->addOption('stop-on-violation', 'S', InputOption::VALUE_NONE, 'Stop validation on first violation found')
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
         * @phpstan-var bool $stopOnViolation
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $stopOnViolation = $input->getOption('stop-on-violation');

        /**
         * @var class-string $class
         */
        $class = $input->getArgument('class');

        /** @var array<string> $paths */
        $paths = $input->getArgument('paths');

        $style = new SymfonyStyle($input, $output);
        $style->title('Xezilaires validate');

        $driver = new AnnotationDriver();

        $totalCount = 0;
        $totalInvalid = 0;
        $totalPaths = 0;
        foreach ($paths as $path) {
            ++$totalPaths;
            if ($style->isVerbose()) {
                $style->section(sprintf('Processing: %1$s', $path));
            }

            $iterator = $this->iteratorFactory->fromFile(
                new \SplFileObject($path),
                $driver->getMetadataMapping($class)
            );

            $count = 0;
            $invalid = 0;
            $progress = $style->createProgressBar();
            foreach ($iterator as $item) {
                ++$count;
                ++$totalCount;
                $violations = $this->validator->validate($item);
                if ($violations->count() > 0) {
                    ++$invalid;
                    ++$totalInvalid;
                    $progress->clear();

                    $style->section(sprintf('Row %1$d', $iterator->key()));

                    /**
                     * @psalm-suppress UnnecessaryVarAnnotation it's required for older Symfony instances
                     *
                     * @var ConstraintViolationInterface $violation
                     */
                    foreach ($violations as $violation) {
                        /** @var string $message */
                        $message = $violation->getMessage();

                        $style->error(sprintf('%1$s: %2$s', $violation->getPropertyPath(), $message));
                    }

                    if ($stopOnViolation) {
                        break;
                    }
                }

                $progress->advance();
            }
            $progress->clear();

            if ($invalid > 0 || $style->isVerbose()) {
                $style->section(sprintf('Summary:    %1$s', $path));
            }
            if ($invalid > 0) {
                $style->error(sprintf('Processed %1$d rows, %2$d had errors (%3$.02f%%)', $count, $invalid, $invalid / $count * 100));

                if ($stopOnViolation) {
                    break;
                }
            } elseif (\count($paths) < 2 || $style->isVerbose()) {
                $style->success(sprintf('Processed %1$d rows', $count));
            }
        }

        if ($totalPaths > 1) {
            if ($totalInvalid > 0 || $style->isVerbose()) {
                $style->title('Summary');
            }
            if ($totalInvalid > 0) {
                $style->error(sprintf('Processed %1$d files with %2$d rows, %3$d rows had errors (%4$.02f%%)', $totalPaths, $totalCount, $totalInvalid, $totalInvalid / $totalCount * 100));

                return 1;
            }

            $style->success(sprintf('Processed %1$d files with %2$d rows', $totalPaths, $totalCount));
        }

        return $totalInvalid;
    }
}
