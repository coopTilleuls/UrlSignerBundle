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

namespace CoopTilleuls\UrlSignerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Route;

final class SignedRouteCollectionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $routes = $container->get('router')->getRouteCollection();

        $signedRoutePaths = [];
        /** @var Route $route */
        foreach ($routes as $route) {
            if ($route->getOption('signed')) {
                $signedRoutePaths[] = $route->getPath();
            }
        }

        $container->setParameter('url_signer.signed_route_paths', $signedRoutePaths);
    }
}
