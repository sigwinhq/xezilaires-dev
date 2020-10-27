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

namespace Example;

use Xezilaires\Test\Model\Product;

require_once __DIR__.'/../../vendor/autoload.php';

// needed only for the demo
// <demo>
$symfonySerializer = new \Symfony\Component\Serializer\Serializer([
    new \Symfony\Component\Serializer\Normalizer\PropertyNormalizer(),
]);
$normalizer = new \Xezilaires\Serializer\ObjectSerializer($symfonySerializer);
$iteratorFactory = new \Xezilaires\SpreadsheetIteratorFactory($normalizer, [
    \Xezilaires\Bridge\PhpSpreadsheet\Spreadsheet::class,
]);
$annotationDriver = new \Xezilaires\Metadata\Annotation\AnnotationDriver();
// </demo>

$iterator = $iteratorFactory->fromFile(
    // https://github.com/dkarlovi/xezilaires/raw/master/resources/fixtures/products.xlsx
    new \SplFileObject(__DIR__.'/../../src/Xezilaires/Test/resources/fixtures/products.xlsx'),
    $annotationDriver->getMetadataMapping(Product::class)
);

$out = iterator_to_array($iterator);
echo \json_encode($out, \JSON_PRETTY_PRINT);
