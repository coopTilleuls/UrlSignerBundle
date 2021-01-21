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

namespace CoopTilleuls\UrlSignerBundle\EventListener;

use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Route;

final class ValidateSignedRouteListener implements EventSubscriberInterface
{
    private UrlSignerInterface $urlSigner;

    public function __construct(UrlSignerInterface $urlSigner)
    {
        $this->urlSigner = $urlSigner;
    }

    public function validateSignedRoute(RequestEvent $event): void
    {
        $request = $event->getRequest();
        /** @var array $routeParams */
        $routeParams = $request->attributes->get('_route_params');

        if (!$routeParams || !($routeParams['_signed'] ?? false)) {
            return;
        }

        if (!$this->urlSigner->validate($request->getRequestUri())) {
            throw new AccessDeniedHttpException('URL is either missing a valid signature or have a bad signature');
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'validateSignedRoute',
        ];
    }
}
