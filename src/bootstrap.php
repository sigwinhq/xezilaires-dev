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

use Doctrine\Common\Annotations\AnnotationRegistry;

$autoLoaders = [
    // own auto-loader
    __DIR__.'/../vendor/autoload.php',

    // project auto-loader
    __DIR__.'/../../../autoload.php',
];

$found = false;
foreach ($autoLoaders as $autoLoader) {
    if (true === file_exists($autoLoader)) {
        /* @noinspection PhpIncludeInspection */
        include $autoLoader;

        $found = true;
        break;
    }
}

if (false === $found) {
    fwrite(
        STDERR,
        'You must set up the project dependencies using `composer install`'.PHP_EOL
    );
    exit(1);
}

if (false === class_exists(AnnotationRegistry::class)) {
    fwrite(
        STDERR,
        'Xezilaires annotations support requires Doctrine Annotations component. Install "doctrine/annotations" to use it.'.PHP_EOL
    );
    exit(1);
}

AnnotationRegistry::registerLoader('class_exists');
