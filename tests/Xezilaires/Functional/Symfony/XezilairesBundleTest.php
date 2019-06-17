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

namespace Xezilaires\Test\Functional\Symfony;

use Nyholm\BundleTest\BaseBundleTestCase;
use Xezilaires\Bridge\Symfony\Serializer\ObjectSerializer;
use Xezilaires\Bridge\Symfony\XezilairesBundle;

/**
 * @covers \Xezilaires\Bridge\Symfony\XezilairesBundle
 *
 * @uses \Xezilaires\Bridge\Symfony\DependencyInjection\XezilairesExtension
 * @uses \Xezilaires\Bridge\Symfony\Serializer\ObjectSerializer
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
    public function testCanInitBundle(): void
    {
        $this->bootKernel();

        $container = $this->getContainer();

        static::assertTrue($container->has('xezilaires.serializer'));

        $service = $container->get('xezilaires.serializer');
        static::assertInstanceOf(ObjectSerializer::class, $service);
    }

    /**
     * {@inheritdoc}
     */
    protected function getBundleClass(): string
    {
        return XezilairesBundle::class;
    }
}
