<?php

/*
 * This file is part of CoopTilleulsUrlSignerBundle.
 *
 * (c) Les-Tilleuls.coop <contact@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\DependencyInjection\Compiler;

use CoopTilleuls\UrlSignerBundle\DependencyInjection\Compiler\SignerPass;
use CoopTilleuls\UrlSignerBundle\UrlSigner\Md5UrlSigner;
use CoopTilleuls\UrlSignerBundle\UrlSigner\Sha256UrlSigner;
use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

#[CoversClass(SignerPass::class)]
final class SignerPassTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<ContainerBuilder> */
    private ObjectProphecy $containerBuilderProphecy;
    private SignerPass $signerPass;

    #[\Override]
    protected function setUp(): void
    {
        $this->containerBuilderProphecy = $this->prophesize(ContainerBuilder::class);
        $this->signerPass = new SignerPass();
    }

    public function testProcess(): void
    {
        $this->containerBuilderProphecy->findTaggedServiceIds('url_signer.signer')->willReturn([
            'url_signer.signer.sha256' => [],
            'url_signer.signer.md5' => [],
        ]);
        $bindings = [
            'string $signatureKey' => '%url_signer.signature_key%',
            '$defaultExpiration' => '%url_signer.default_expiration%',
            'string $expiresParameter' => '%url_signer.expires_parameter%',
            'string $signatureParameter' => '%url_signer.signature_parameter%',
        ];
        $md5SignerServiceDefinitionProphecy = $this->prophesize(Definition::class);
        $md5SignerServiceDefinitionProphecy->setBindings($bindings)->willReturn($md5SignerServiceDefinitionProphecy);
        $sha256SignerServiceDefinitionProphecy = $this->prophesize(Definition::class);
        $sha256SignerServiceDefinitionProphecy->setBindings($bindings)->willReturn($sha256SignerServiceDefinitionProphecy);
        $this->containerBuilderProphecy->getDefinition('url_signer.signer.md5')->willReturn($md5SignerServiceDefinitionProphecy->reveal());
        $this->containerBuilderProphecy->getDefinition('url_signer.signer.sha256')->willReturn($sha256SignerServiceDefinitionProphecy->reveal());
        $md5SignerServiceDefinitionProphecy->getClass()->willReturn(Md5UrlSigner::class);
        $sha256SignerServiceDefinitionProphecy->getClass()->willReturn(Sha256UrlSigner::class);
        $this->containerBuilderProphecy->getParameter('url_signer.signer')->willReturn(Md5UrlSigner::getName());
        $this->containerBuilderProphecy->setAlias('url_signer.signer', 'url_signer.signer.md5')->willReturn(new Alias('url_signer.signer.md5'));
        $this->containerBuilderProphecy->setAlias(UrlSignerInterface::class, 'url_signer.signer.md5')->willReturn(new Alias('url_signer.signer.md5'));

        $this->signerPass->process($this->containerBuilderProphecy->reveal());

        $md5SignerServiceDefinitionProphecy->setBindings($bindings)->shouldHaveBeenCalledOnce();
        $sha256SignerServiceDefinitionProphecy->setBindings($bindings)->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setAlias('url_signer.signer', 'url_signer.signer.md5')->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setAlias(UrlSignerInterface::class, 'url_signer.signer.md5')->shouldHaveBeenCalledOnce();
    }

    public function testProcessNoUrlSigner(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("No URL signer with the name \"invalid\" found. Available names are:\n- \"sha256\"");

        $this->containerBuilderProphecy->findTaggedServiceIds('url_signer.signer')->willReturn([
            'url_signer.signer.sha256' => [],
        ]);
        $sha256SignerServiceDefinitionProphecy = $this->prophesize(Definition::class);
        $sha256SignerServiceDefinitionProphecy->setBindings(Argument::type('array'))->willReturn($sha256SignerServiceDefinitionProphecy);
        $this->containerBuilderProphecy->getDefinition('url_signer.signer.sha256')->willReturn($sha256SignerServiceDefinitionProphecy->reveal());
        $sha256SignerServiceDefinitionProphecy->getClass()->willReturn(Sha256UrlSigner::class);
        $this->containerBuilderProphecy->getParameter('url_signer.signer')->willReturn('invalid');

        $this->signerPass->process($this->containerBuilderProphecy->reveal());

        $this->containerBuilderProphecy->setAlias(Argument::cetera())->shouldNotHaveBeenCalled();
    }
}
