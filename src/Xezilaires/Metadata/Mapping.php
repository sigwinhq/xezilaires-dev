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

namespace Xezilaires\Metadata;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Mapping.
 */
class Mapping
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var array<string, ColumnReference>
     */
    private $columns;

    /**
     * @var array<string, null|string|bool>
     */
    private $options;

    /**
     * @param string                         $className
     * @param array<string, ColumnReference> $columns
     * @param array                          $options
     */
    public function __construct(string $className, array $columns, array $options = null)
    {
        $this->className = $className;
        $this->columns = $columns;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        /** @var array<string, null|string|bool> $options */
        $options = $resolver->resolve($options ?? []);
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return array<string, string>
     */
    public function getColumnMapping(): array
    {
        $mapping = [];
        foreach ($this->columns as $name => $column) {
            $mapping[$name] = $column->getColumn();
        }

        return $mapping;
    }

    /**
     * @param string $string
     *
     * @return null|string|bool
     */
    public function getOption(string $string)
    {
        return $this->options[$string];
    }

    /**
     * @param OptionsResolver $resolver
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'start' => 1,
            'end' => null,
            'header' => null,
            'reverse' => false,
        ]);

        $resolver->setAllowedTypes('start', 'int');
        $resolver->setAllowedTypes('end', ['int', 'null']);
        $resolver->setAllowedTypes('header', ['int', 'null']);
        $resolver->setAllowedTypes('reverse', 'bool');
    }
}
