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

namespace Xezilaires\Bridge\Symfony\Test\Functional;

use Nyholm\BundleTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Xezilaires\Bridge\Symfony\XezilairesBundle;
use Xezilaires\Denormalizer;
use Xezilaires\IteratorFactory;
use Xezilaires\Metadata\ColumnReference;
use Xezilaires\Metadata\Mapping;
use Xezilaires\Serializer;
use Xezilaires\Serializer\ObjectSerializer;
use Xezilaires\SpreadsheetIterator;
use Xezilaires\SpreadsheetIteratorFactory;
use Xezilaires\Test\FixtureTrait;
use Xezilaires\Test\Model\Product;
use Xezilaires\Validator;

/**
 * @internal
 *
 * @coversNothing
 *
 * @small
 */
#[\PHPUnit\Framework\Attributes\Medium]
#[\PHPUnit\Framework\Attributes\CoversClass(XezilairesBundle::class)]
#[\PHPUnit\Framework\Attributes\Group('functional')]
#[\PHPUnit\Framework\Attributes\Group('symfony')]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Bridge\Spout\Spreadsheet::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Bridge\Symfony\DependencyInjection\XezilairesExtension::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(\Xezilaires\Bridge\Symfony\Validator::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(ColumnReference::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(Mapping::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(ObjectSerializer::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(SpreadsheetIterator::class)]
#[\PHPUnit\Framework\Attributes\UsesClass(SpreadsheetIteratorFactory::class)]
final class XezilairesBundleTest extends KernelTestCase
{
    use FixtureTrait;

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testBundleHasValidator(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        self::assertTrue($container->has(Validator::class));
        self::assertInstanceOf(\Xezilaires\Bridge\Symfony\Validator::class, $container->get(Validator::class));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testBundleHasSerializer(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        self::assertTrue($container->has(Serializer::class));
        self::assertInstanceOf(ObjectSerializer::class, $container->get(Serializer::class));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testBundleHasDenormalizer(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        self::assertTrue($container->has(Denormalizer::class));
        self::assertInstanceOf(ObjectSerializer::class, $container->get(Denormalizer::class));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testBundleHasIteratorFactory(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        self::assertTrue($container->has(IteratorFactory::class));
        self::assertInstanceOf(SpreadsheetIteratorFactory::class, $container->get(IteratorFactory::class));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testIteratorFactoryWorks(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        /**
         * @phpstan-var IteratorFactory $iteratorFactory
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $iteratorFactory = $container->get(IteratorFactory::class);

        $mapping = new Mapping(Product::class, ['name' => new ColumnReference('A')]);
        $iterator = $iteratorFactory->fromFile($this->fixture('products.xlsx'), $mapping);

        self::assertInstanceOf(SpreadsheetIterator::class, $iterator);
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @phpstan-var TestKernel $kernel
         *
         * @psalm-suppress UnnecessaryVarAnnotation
         */
        $kernel = parent::createKernel($options);
        $kernel->addTestBundle(XezilairesBundle::class);
        $kernel->addTestConfig(__DIR__.'/config.yaml');
        $kernel->handleOptions($options);

        return $kernel;
    }
}
