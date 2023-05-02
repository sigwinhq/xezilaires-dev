# Xezilaires

Xezilaires is a PHP library which helps to iterate structured Excel spreadsheets,
normalize rows into value objects, validate, serialize into CSV, JSON, XML.

[![Latest Stable Version](https://poser.pugx.org/sigwin/xezilaires/v/stable.png)](https://github.com/sigwinhq/xezilaires-dev)
[![Actions Status](https://github.com/sigwinhq/xezilaires-dev/workflows/Build/badge.svg)](https://github.com/sigwinhq/xezilaires-dev/actions)
[![PHPStan enabled](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)
[![Psalm enabled](https://img.shields.io/badge/Psalm-enabled-brightgreen.svg?style=flat)](https://github.com/vimeo/psalm)

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
$iteratorFactory = new \Xezilaires\SpreadsheetIteratorFactory($normalizer, [
    \Xezilaires\Bridge\PhpSpreadsheet\Spreadsheet::class,
]);

$iterator = $iteratorFactory->fromFile(
    // https://github.com/sigwinhq/xezilaires-dev/raw/master/src/Xezilaires/Test/resources/fixtures/products.xlsx
    new \SplFileObject(__DIR__.'/../../src/Xezilaires/Test/resources/fixtures/products.xlsx'),
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
$iteratorFactory = new \Xezilaires\SpreadsheetIteratorFactory($normalizer, [
    \Xezilaires\Bridge\PhpSpreadsheet\Spreadsheet::class,
]);
$annotationDriver = new \Xezilaires\Metadata\Annotation\AnnotationDriver();

$iterator = $iteratorFactory->fromFile(
    // https://github.com/sigwinhq/xezilaires-dev/raw/master/src/Xezilaires/Test/resources/fixtures/products.xlsx
    new \SplFileObject(__DIR__.'/../../src/Xezilaires/Test/resources/fixtures/products.xlsx'),
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
- `sequential`, is the key sequential (0, 1, 2) or represents current row?  
  *(boolean, optional, default: `false`)*

## Features

Features included:

- **Reading Excel files**  
*(using either `phpoffice/PhpSpreadsheet` or `openspout/openspout`)*
- **Denormalization / normalization** support  
*(using `symfony/serializer`, from / to all supported formats)*
- **Annotations** support  
*(using `doctrine/annotations`)*
- mapping via **column names** or **header labels**  
*(saying "Map header label `PrdctEN` to property `product`")*
- **A Symfony bundle**  
*(for easy integration into existing apps)*
- CLI *(command-line interface)* tool

## Custom normalizers / validators

You can use your own normalizers / validators by passing your own Symfony bundle
which registers them to the Xezilaires commands via `--bundle`, like so: 

```
vendor/bin/xezilaires validate --bundle Xezilaires\\Test\\ExampleBundle\\XezilairesExampleBundle Xezilaires\\Test\\Model\\Product src/Xezilaires/Test/resources/fixtures/products.xlsx 
```

See example bundle in [`src/Xezilaires/Test/ExampleBundle/`](./src/Xezilaires/Test/ExampleBundle/).

## What's with the name

`xezilaires` is `serializex` backwards.

We added the X so the name so we can shorten it as [XLS](https://fileinfo.com/extension/xls).
As a side-effect, we [made reading Excel files with this library cool](https://tvtropes.org/pmwiki/pmwiki.php/Main/XMakesAnythingCool).
