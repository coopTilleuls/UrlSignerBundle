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

namespace CoopTilleuls\UrlSignerBundle\DependencyInjection;

use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class CoopTilleulsUrlSignerExtension extends Extension
{
    /** @param mixed[] $configs */
    #[\Override]
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        /** @var array{signer: string, signature_key: string, default_expiration: int, expires_parameter: string, signature_parameter: string} */
        $config = $this->processConfiguration($configuration, $configs);

        $container->registerForAutoconfiguration(UrlSignerInterface::class)->addTag('url_signer.signer');

        $container->setParameter('url_signer.signer', $config['signer']);
        $container->setParameter('url_signer.signature_key', $config['signature_key']);
        $container->setParameter('url_signer.default_expiration', $config['default_expiration']);
        $container->setParameter('url_signer.expires_parameter', $config['expires_parameter']);
        $container->setParameter('url_signer.signature_parameter', $config['signature_parameter']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
