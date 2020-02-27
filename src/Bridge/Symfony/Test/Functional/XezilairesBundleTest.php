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

namespace Xezilaires\Bridge\Symfony\Test\Functional;

use Nyholm\BundleTest\BaseBundleTestCase;
use Xezilaires\Bridge\Symfony\XezilairesBundle;
use Xezilaires\Denormalizer;
use Xezilaires\IteratorFactory;
use Xezilaires\Serializer;
use Xezilaires\Serializer\ObjectSerializer;
use Xezilaires\SpreadsheetIteratorFactory;

/**
 * @covers \Xezilaires\Bridge\Symfony\XezilairesBundle
 *
 * @uses \Xezilaires\Bridge\Symfony\DependencyInjection\XezilairesExtension
 * @uses \Xezilaires\Serializer\ObjectSerializer
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
     * {@inheritdoc}
     */
    protected function getBundleClass(): string
    {
        return XezilairesBundle::class;
    }
}
