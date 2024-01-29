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

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/src',
    ]);
    $rectorConfig->skip([
        __DIR__.'/src/Xezilaires/var',
        __DIR__.'/src/Xezilaires/vendor',
        __DIR__.'/src/Bridge/PhpSpreadsheet/var',
        __DIR__.'/src/Bridge/PhpSpreadsheet/vendor',
        __DIR__.'/src/Bridge/Spout/var',
        __DIR__.'/src/Bridge/Spout/vendor',
        __DIR__.'/src/Bridge/Symfony/var',
        __DIR__.'/src/Bridge/Symfony/vendor',
    ]);
    $rectorConfig->sets([
        DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,
        LevelSetList::UP_TO_PHP_82,
        PHPUnitSetList::PHPUNIT_90,
        PHPUnitSetList::PHPUNIT_91,
        PHPUnitSetList::PHPUNIT_100,
        SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,
        SymfonySetList::SYMFONY_63,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ]);
};
