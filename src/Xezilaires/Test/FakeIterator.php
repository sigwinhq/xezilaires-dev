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

namespace Xezilaires\Test;

use Xezilaires\Iterator;

/**
 * @internal
 */
final class FakeIterator implements Iterator
{
    private readonly \ArrayIterator $iterator;

    /**
     * @param array<int, int|object|string> $items
     */
    public function __construct(array $items)
    {
        $objects = [];
        foreach ($items as $idx => $item) {
            $objects[$idx] = (object) $item;
        }

        $this->iterator = new \ArrayIterator($objects);
    }

    /**
     * @return array<int, int|object|string>
     */
    public static function toArray(\Iterator $iterator): array
    {
        $objects = iterator_to_array($iterator);

        $items = [];

        /**
         * @var int    $idx
         * @var object $object
         */
        foreach ($objects as $idx => $object) {
            if ($object instanceof \stdClass) {
                /** @var int|string $scalar */
                $scalar = $object->scalar;

                $items[$idx] = $scalar;
            } else {
                $items[$idx] = $object;
            }
        }

        return $items;
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function current(): object
    {
        /** @var object $current */
        $current = $this->iterator->current();

        return $current;
    }

    public function key(): int
    {
        /** @var int|string $key */
        $key = $this->iterator->key();

        if (false === \is_int($key)) {
            throw new \LogicException(sprintf('Key must be of type int, %1$s given', \gettype($key)));
        }

        return $key;
    }

    public function seek(int $rowIndex): void
    {
        $this->iterator->seek($rowIndex);
    }

    public function prev(): void
    {
    }
}
