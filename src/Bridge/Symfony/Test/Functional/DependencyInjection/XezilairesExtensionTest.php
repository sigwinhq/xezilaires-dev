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

namespace Xezilaires\Bridge\Symfony\Test\Functional\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Xezilaires\Bridge\Symfony\Command\SerializeCommand;
use Xezilaires\Bridge\Symfony\Command\ValidateCommand;
use Xezilaires\Bridge\Symfony\DependencyInjection\XezilairesExtension;
use Xezilaires\Bridge\Symfony\Validator;
use Xezilaires\Denormalizer;
use Xezilaires\IteratorFactory;
use Xezilaires\Serializer;
use Xezilaires\Serializer\ObjectSerializer;

/**
 * @internal
 *
 * @coversNothing
 *
 * @small
 */
#[\PHPUnit\Framework\Attributes\Medium]
#[\PHPUnit\Framework\Attributes\CoversClass(XezilairesExtension::class)]
#[\PHPUnit\Framework\Attributes\Group('functional')]
#[\PHPUnit\Framework\Attributes\Group('symfony')]
final class XezilairesExtensionTest extends AbstractExtensionTestCase
{
    public function testContainerHasValidator(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService('xezilaires.validator', Validator::class);
        $this->assertContainerBuilderHasAlias(\Xezilaires\Validator::class, 'xezilaires.validator');
    }

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
        $this->assertContainerBuilderHasServiceDefinitionWithTag(SerializeCommand::class, 'console.command', ['command' => 'xezilaires:serialize']);
    }

    public function testContainerHasValidateCommand(): void
    {
        $this->load();

        $this->assertContainerBuilderHasService(ValidateCommand::class);
        $this->assertContainerBuilderHasServiceDefinitionWithTag(ValidateCommand::class, 'console.command', ['command' => 'xezilaires:validate']);
    }

    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new XezilairesExtension()];
    }
}
