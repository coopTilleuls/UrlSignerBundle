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

namespace Tests\DependencyInjection;

use CoopTilleuls\UrlSignerBundle\DependencyInjection\CoopTilleulsUrlSignerExtension;
use CoopTilleuls\UrlSignerBundle\UrlSigner\Md5UrlSigner;
use CoopTilleuls\UrlSignerBundle\UrlSigner\Sha256UrlSigner;
use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @internal
 * @covers \CoopTilleuls\UrlSignerBundle\DependencyInjection\CoopTilleulsUrlSignerExtension
 */
final class CoopTilleulsUrlSignerExtensionTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<ContainerBuilder> */
    private ObjectProphecy $containerBuilderProphecy;
    private CoopTilleulsUrlSignerExtension $extension;

    protected function setUp(): void
    {
        $this->containerBuilderProphecy = $this->prophesize(ContainerBuilder::class);
        $this->extension = new CoopTilleulsUrlSignerExtension();

        $this->containerBuilderProphecy->hasExtension('http://symfony.com/schema/dic/services')->willReturn(false);
        $this->containerBuilderProphecy->removeBindings(Argument::type('string'))->will(function () {});
    }

    public function testLoadDefaultConfiguration(): void
    {
        $signatureKey = 'secret';
        $configs = [
            'coop_tilleuls_url_signer' => ['signature_key' => $signatureKey],
        ];
        $childDefinitionProphecy = $this->prophesize(ChildDefinition::class);
        $childDefinitionProphecy->addTag('url_signer.signer')->willReturn($childDefinitionProphecy);
        $this->containerBuilderProphecy->registerForAutoconfiguration(UrlSignerInterface::class)->willReturn($childDefinitionProphecy->reveal());
        $this->containerBuilderProphecy->fileExists(Argument::containingString('/../Resources/config'))->willReturn(true);
        $this->containerBuilderProphecy->setDefinition(Argument::cetera())->willReturn(new Definition());
        $this->containerBuilderProphecy->setAlias(Argument::cetera())->willReturn(new Alias('alias'));

        $this->extension->load($configs, $this->containerBuilderProphecy->reveal());

        $this->containerBuilderProphecy->registerForAutoconfiguration(UrlSignerInterface::class)->shouldHaveBeenCalledOnce();
        $childDefinitionProphecy->addTag('url_signer.signer')->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setParameter('url_signer.signer', Sha256UrlSigner::getName())->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setParameter('url_signer.signature_key', $signatureKey)->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setParameter('url_signer.default_expiration', 1)->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setParameter('url_signer.expires_parameter', 'expires')->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setParameter('url_signer.signature_parameter', 'signature')->shouldHaveBeenCalledOnce();
        $definitions = [
            'url_signer.signer.md5',
            'url_signer.signer.sha256',
            'url_signer.listener.validate_signed_route',
        ];
        foreach ($definitions as $definition) {
            $this->containerBuilderProphecy->setDefinition($definition, Argument::type(Definition::class))->shouldHaveBeenCalledOnce();
        }
        $aliases = [
            Md5UrlSigner::class => 'url_signer.signer.md5',
            Sha256UrlSigner::class => 'url_signer.signer.sha256',
        ];
        foreach ($aliases as $alias => $id) {
            $this->containerBuilderProphecy->setAlias($alias, $id)->shouldHaveBeenCalledOnce();
        }
    }

    public function testLoadFullConfiguration(): void
    {
        $signatureKey = 'secret';
        $signer = 'md5';
        $defaultExpiration = 4;
        $expiresParameter = 'exp';
        $signatureParameter = 'sign';
        $configs = [
            'coop_tilleuls_url_signer' => [
                'signature_key' => $signatureKey,
                'signer' => $signer,
                'default_expiration' => $defaultExpiration,
                'expires_parameter' => $expiresParameter,
                'signature_parameter' => $signatureParameter,
            ],
        ];
        $childDefinitionProphecy = $this->prophesize(ChildDefinition::class);
        $childDefinitionProphecy->addTag('url_signer.signer')->willReturn($childDefinitionProphecy);
        $this->containerBuilderProphecy->registerForAutoconfiguration(UrlSignerInterface::class)->willReturn($childDefinitionProphecy->reveal());
        $this->containerBuilderProphecy->fileExists(Argument::containingString('/../Resources/config'))->willReturn(true);
        $this->containerBuilderProphecy->setDefinition(Argument::cetera())->willReturn(new Definition());
        $this->containerBuilderProphecy->setAlias(Argument::cetera())->willReturn(new Alias('alias'));

        $this->extension->load($configs, $this->containerBuilderProphecy->reveal());

        $this->containerBuilderProphecy->registerForAutoconfiguration(UrlSignerInterface::class)->shouldHaveBeenCalledOnce();
        $childDefinitionProphecy->addTag('url_signer.signer')->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setParameter('url_signer.signer', Md5UrlSigner::getName())->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setParameter('url_signer.signature_key', $signatureKey)->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setParameter('url_signer.default_expiration', $defaultExpiration)->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setParameter('url_signer.expires_parameter', $expiresParameter)->shouldHaveBeenCalledOnce();
        $this->containerBuilderProphecy->setParameter('url_signer.signature_parameter', $signatureParameter)->shouldHaveBeenCalledOnce();
    }
}
