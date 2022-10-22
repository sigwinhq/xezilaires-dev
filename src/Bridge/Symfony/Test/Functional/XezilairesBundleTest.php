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
 * @covers \Xezilaires\Bridge\Symfony\XezilairesBundle
 *
 * @uses \Xezilaires\Bridge\Spout\Spreadsheet
 * @uses \Xezilaires\Bridge\Symfony\DependencyInjection\XezilairesExtension
 * @uses \Xezilaires\Bridge\Symfony\Validator
 * @uses \Xezilaires\Metadata\ColumnReference
 * @uses \Xezilaires\Metadata\Mapping
 * @uses \Xezilaires\Serializer\ObjectSerializer
 * @uses \Xezilaires\SpreadsheetIterator
 * @uses \Xezilaires\SpreadsheetIteratorFactory
 *
 * @group functional
 * @group symfony
 *
 * @internal
 *
 * @medium
 */
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

        static::assertTrue($container->has(Validator::class));
        static::assertInstanceOf(\Xezilaires\Bridge\Symfony\Validator::class, $container->get(Validator::class));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testBundleHasSerializer(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        static::assertTrue($container->has(Serializer::class));
        static::assertInstanceOf(ObjectSerializer::class, $container->get(Serializer::class));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testBundleHasDenormalizer(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        static::assertTrue($container->has(Denormalizer::class));
        static::assertInstanceOf(ObjectSerializer::class, $container->get(Denormalizer::class));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testBundleHasIteratorFactory(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();

        static::assertTrue($container->has(IteratorFactory::class));
        static::assertInstanceOf(SpreadsheetIteratorFactory::class, $container->get(IteratorFactory::class));
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

        static::assertInstanceOf(SpreadsheetIterator::class, $iterator);
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
