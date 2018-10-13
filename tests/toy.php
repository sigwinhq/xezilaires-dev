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

use Tree\Node\NodeInterface;
use Xezilaires\Bridge\PhpSpreadsheet\Iterator;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\Mapping;
use Xezilaires\Test\Model\Category;

require_once __DIR__.'/../vendor/autoload.php';

$reader = new Iterator(
    new SplFileObject(__DIR__.'/../resources/fixtures/categories.xls'),
    new Mapping(
        Category::class,
        [
            'id' => new ColumnReference('A'),
            'parent' => new ColumnReference('B'),
            'name' => new ColumnReference('C'),
        ],
        [
            'start' => 2,
        ]
    )
);

$tree = new Xezilaires\TreeBuilder($reader);
viz($tree->getRoot());

/**
 * @param NodeInterface $node
 * @param int           $depth
 */
function viz(NodeInterface $node, int $depth = 0): void
{
    echo str_repeat(' ', $depth * 4).(string) $node->getValue()."\n";

    /**
     * @var NodeInterface $child
     */
    foreach ($node->getChildren() as $child) {
        viz($child, $depth + 1);
    }
}
