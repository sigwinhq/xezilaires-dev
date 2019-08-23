# Xezilaires

Xezilaires is a PHP 7 library which helps reading structured Excel files
into PHP objects.

[![Latest Stable Version](https://poser.pugx.org/dkarlovi/xezilaires/v/stable.png)](https://packagist.org/packages/dkarlovi/xezilaires)
[![Travis CI Build Status](https://travis-ci.com/dkarlovi/xezilaires.svg?branch=master)](https://travis-ci.com/dkarlovi/xezilaires)
[![Infection Mutation testing badge](https://badge.stryker-mutator.io/github.com/dkarlovi/xezilaires/master)](https://stryker-mutator.io/)
[![PHPStan enabled](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

## What it does

1. we create a PHP class which will hold our Excel row data
2. we next create spreadsheet iterator instance
    1. passing the path to the Excel file we wish to read
    2. passing the configuration mapping the Excel columns into PHP properties
3. as we're iterating, we are getting an value object (instance of the defined class)
   for each row

Think of it as an "ORM" *(Object Relation Manager)* for an Excel file.
An OEM *(Object Excel Manager)*, if you will.

## Example usage

### Without annotations

```php
class Product
{
    private $name;
}

$symfonySerializer = new \Symfony\Component\Serializer\Serializer([
    new \Symfony\Component\Serializer\Normalizer\PropertyNormalizer(),
]);
$normalizer = new \Xezilaires\Bridge\Symfony\Serializer\ObjectSerializer($symfonySerializer);
$iteratorFactory = new \Xezilaires\SpreadsheetIteratorFactory($normalizer);

$iterator = $iteratorFactory->fromFile(
    // https://github.com/dkarlovi/xezilaires/raw/master/resources/fixtures/products.xlsx
    new \SplFileObject(__DIR__.'/../../resources/fixtures/products.xlsx'),
    new \Xezilaires\Metadata\Mapping(
        Model\Product::class,
        [
            'name' => new \Xezilaires\Metadata\ColumnReference('A'),
        ],
        [
            // options
            'start' => 2,
        ]
    )
);
```

### With annotations

```php
use Xezilaires\Annotation as XLS;

/**
 * @XLS\Options(header=1, start=2)
 */
class Product
{
    /**
     * @XLS\HeaderReference(header="Name")
     */
    private $name;
}

$symfonySerializer = new \Symfony\Component\Serializer\Serializer([
    new \Symfony\Component\Serializer\Normalizer\PropertyNormalizer(),
]);
$normalizer = new \Xezilaires\Bridge\Symfony\Serializer\ObjectSerializer($symfonySerializer);
$iteratorFactory = new \Xezilaires\SpreadsheetIteratorFactory($normalizer);
$annotationDriver = new \Xezilaires\Metadata\Annotation\AnnotationDriver();

$iterator = $iteratorFactory->fromFile(
    // https://github.com/dkarlovi/xezilaires/raw/master/resources/fixtures/products.xlsx
    new \SplFileObject(__DIR__.'/../../resources/fixtures/products.xlsx'),
    $annotationDriver->getMetadataMapping(Product::class, ['reverse' => true])
);
```

See more examples in the [`docs/examples/`](./docs/examples/) folder.

## Options

- `start`, which row do we start on  
  *(integer, optional, default: `1`)*
- `header`, which row contains the header labels  
  *(integer, optional if not using `HeaderReference`, default: `null`)*
- `reverse`, do we iterate the rows in reverse, from end to start  
  *(boolean, optional, default: `false`)*

## Features

Features included:

- **Reading Excel files**  
*(using either `phpoffice/PhpSpreadsheet` or `box/spout`)*
- **Denormalization / normalization** support  
*(using `symfony/serializer`, from / to all supported formats)*
- **Annotations** support  
*(using `doctrine/annotations`)*
- mapping via **column names** or **header labels**  
*(saying "Map header label `PrdctEN` to property `product`")*
- **A Symfony bundle**
*(for easy integration into existing apps)*
- CLI *(command-line interface)* tool

## What's with the name

"xezilaires" is "serializex" backwards.

We added the X so the name so we can shorten it as [XLS](https://fileinfo.com/extension/xls).
As a side-effect, we [made reading Excel files with this library cool](https://tvtropes.org/pmwiki/pmwiki.php/Main/XMakesAnythingCool).
