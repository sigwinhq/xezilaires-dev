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

namespace Xezilaires\Test\Functional\Symfony\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Xezilaires\Bridge\Symfony\Command\SerializeCommand;
use Xezilaires\Bridge\Symfony\DependencyInjection\XezilairesExtension;
use Xezilaires\Bridge\Symfony\Serializer\ObjectSerializer;
use Xezilaires\Denormalizer;
use Xezilaires\IteratorFactory;
use Xezilaires\Serializer;

/**
 * @covers \Xezilaires\Bridge\Symfony\DependencyInjection\XezilairesExtension
 *
 * @group functional
 * @group symfony
 *
 * @internal
 *
 * @small
 */
final class XezilairesExtensionTest extends AbstractExtensionTestCase
{
    public function testContainerHasSerializer(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('xezilaires.serializer', ObjectSerializer::class);
        $this->assertContainerBuilderHasAlias(Denormalizer::class, 'xezilaires.serializer');
        $this->assertContainerBuilderHasAlias(Serializer::class, 'xezilaires.serializer');
    }

    public function testContainerHasIteratorFactory(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('xezilaires.spreadsheet_iterator_factory');
        $this->assertContainerBuilderHasAlias(IteratorFactory::class, 'xezilaires.spreadsheet_iterator_factory');
    }

    public function testContainerHasSerializeCommand(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService(SerializeCommand::class);
        $this->assertContainerBuilderHasServiceDefinitionWithTag(SerializeCommand::class, 'console.command');
    }

    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new XezilairesExtension()];
    }
}
