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

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    #[\Override]
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('coop_tilleuls_url_signer');
        $rootNode = $treeBuilder->getRootNode();

        $childNode = $rootNode->children();

        /** @var NodeBuilder $childNode */
        $childNode = $childNode
                ->scalarNode('signer')
                    ->defaultValue('sha256')
                    ->cannotBeEmpty()
                    ->info('signer to use to create the signature')
                ->end()
        ;
        /** @var NodeBuilder $childNode */
        $childNode = $childNode
            ->scalarNode('signature_key')
                ->cannotBeEmpty()
                ->isRequired()
                ->info('key used to sign the URL')
            ->end()
        ;
        /** @var NodeBuilder $childNode */
        $childNode = $childNode
            ->scalarNode('default_expiration')
                ->defaultValue(86400)
                ->cannotBeEmpty()
                ->info('default expiration time in seconds')
            ->end()
        ;
        /** @var NodeBuilder $childNode */
        $childNode = $childNode
            ->scalarNode('expires_parameter')
                ->defaultValue('expires')
                ->cannotBeEmpty()
                ->info('name of the expires parameter in the URL')
            ->end()
        ;
        /** @var NodeBuilder $childNode */
        $childNode = $childNode
            ->scalarNode('signature_parameter')
                ->defaultValue('signature')
                ->cannotBeEmpty()
                ->info('name of the signature parameter in the URL')
            ->end()
        ;

        $childNode->end();

        return $treeBuilder;
    }
}
