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

namespace Xezilaires;

/**
 * Class DeduplicationIterator.
 */
class DeduplicationIterator extends \FilterIterator
{
    /**
     * @var string[]
     */
    private $fields;

    /**
     * @var string[]
     */
    private $hashes = [];

    /**
     * @var int
     */
    private $key = -1;

    /**
     * @param Iterator $iterator
     * @param string[] $fields
     */
    public function __construct(Iterator $iterator, array $fields)
    {
        parent::__construct($iterator);

        $this->fields = $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(): bool
    {
        /** @var object $object */
        $object = $this->getInnerIterator()->current();

        $hash = md5(implode('_', \array_map(function (string $field) use ($object): string {
            return (string) $object->{$field};
        }, $this->fields)));

        if (true === \in_array($hash, $this->hashes, true)) {
            return false;
        }

        $this->hashes[] = $hash;

        ++$this->key;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function key(): int
    {
        return $this->key;
    }
}
