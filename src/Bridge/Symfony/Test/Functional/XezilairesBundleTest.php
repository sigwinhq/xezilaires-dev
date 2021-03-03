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

use Nyholm\BundleTest\BaseBundleTestCase;
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
 * @uses \Xezilaires\Serializer\ObjectSerializer
 * @uses \Xezilaires\Metadata\Mapping
 * @uses \Xezilaires\Metadata\ColumnReference
 * @uses \Xezilaires\SpreadsheetIterator
 * @uses \Xezilaires\SpreadsheetIteratorFactory
 *
 * @group functional
 * @group symfony
 *
 * @internal
 *
 * @small
 */
final class XezilairesBundleTest extends BaseBundleTestCase
{
    use FixtureTrait;

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testBundleHasValidator(): void
    {
        $this->bootKernel();
        $container = $this->getContainer();

        static::assertTrue($container->has(Validator::class));
        static::assertInstanceOf(\Xezilaires\Bridge\Symfony\Validator::class, $container->get(Validator::class));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testBundleHasSerializer(): void
    {
        $this->bootKernel();
        $container = $this->getContainer();

        static::assertTrue($container->has(Serializer::class));
        static::assertInstanceOf(ObjectSerializer::class, $container->get(Serializer::class));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testBundleHasDenormalizer(): void
    {
        $this->bootKernel();
        $container = $this->getContainer();

        static::assertTrue($container->has(Denormalizer::class));
        static::assertInstanceOf(ObjectSerializer::class, $container->get(Denormalizer::class));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testBundleHasIteratorFactory(): void
    {
        $this->bootKernel();
        $container = $this->getContainer();

        static::assertTrue($container->has(IteratorFactory::class));
        static::assertInstanceOf(SpreadsheetIteratorFactory::class, $container->get(IteratorFactory::class));
    }

    /**
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function testIteratorFactoryWorks(): void
    {
        $this->bootKernel();
        $container = $this->getContainer();

        /** @var IteratorFactory $iteratorFactory */
        $iteratorFactory = $container->get(IteratorFactory::class);

        $mapping = new Mapping(Product::class, ['name' => new ColumnReference('A')]);
        $iterator = $iteratorFactory->fromFile($this->fixture('products.xlsx'), $mapping);

        static::assertInstanceOf(SpreadsheetIterator::class, $iterator);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBundleClass(): string
    {
        return XezilairesBundle::class;
    }

    protected function bootKernel(): void
    {
        $kernel = $this->createKernel();
        $kernel->addConfigFile(__DIR__.'/config.yaml');

        parent::bootKernel();
    }
}
