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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use CoopTilleuls\UrlSignerBundle\EventListener\ValidateSignedRouteListener;
use CoopTilleuls\UrlSignerBundle\UrlSigner\Md5UrlSigner;
use CoopTilleuls\UrlSignerBundle\UrlSigner\Sha256UrlSigner;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('url_signer.signer.md5', Md5UrlSigner::class)
        ->public()
        ->tag('url_signer.signer')
    ;

    $services->alias(Md5UrlSigner::class, 'url_signer.signer.md5');

    $services->set('url_signer.signer.sha256', Sha256UrlSigner::class)
        ->public()
        ->tag('url_signer.signer')
    ;

    $services->alias(Sha256UrlSigner::class, 'url_signer.signer.sha256');

    $services->set('url_signer.listener.validate_signed_route', ValidateSignedRouteListener::class)
        ->private()
        ->args([service('url_signer.signer')])
        ->tag('kernel.event_subscriber')
    ;
};
