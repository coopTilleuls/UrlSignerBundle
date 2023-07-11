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

namespace Tests\EventListener;

use CoopTilleuls\UrlSignerBundle\EventListener\ValidateSignedRouteListener;
use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @internal
 *
 * @covers \CoopTilleuls\UrlSignerBundle\EventListener\ValidateSignedRouteListener
 */
final class ValidateSignedRouteListenerTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<UrlSignerInterface> */
    private ObjectProphecy $signerProphecy;
    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->signerProphecy = $this->prophesize(UrlSignerInterface::class);
        $subscriber = new ValidateSignedRouteListener($this->signerProphecy->reveal());
        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber($subscriber);
    }

    public function testSubscribedEvents(): void
    {
        self::assertArrayHasKey(RequestEvent::class, ValidateSignedRouteListener::getSubscribedEvents());
    }

    /** @dataProvider provideValidateSignedRouteCases */
    public function testValidateSignedRoute(string $validUrl): void
    {
        $request = Request::create('http://test.org/valid-signature');
        $request->attributes->set('_route_params', ['_signed' => true]);
        $event = new RequestEvent($this->prophesize(HttpKernelInterface::class)->reveal(), $request, null);
        $this->signerProphecy->validate(Argument::any())->willReturn(false);
        $this->signerProphecy->validate($validUrl)->willReturn(true);

        $this->dispatcher->dispatch($event);

        $isPath = str_starts_with($validUrl, '/');

        $this->signerProphecy->validate(Argument::any())->shouldHaveBeenCalledTimes($isPath ? 1 : 2);
    }

    public function testValidateSignedRouteMissingRouteParamsAttribute(): void
    {
        $request = Request::create('http://test.org/valid-signature');
        $event = new RequestEvent($this->prophesize(HttpKernelInterface::class)->reveal(), $request, null);

        $this->dispatcher->dispatch($event);

        $this->signerProphecy->validate(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function testValidateSignedRouteMissingSignedRouteParam(): void
    {
        $request = Request::create('http://test.org/valid-signature');
        $request->attributes->set('_route_params', ['_locale' => 'fr']);
        $event = new RequestEvent($this->prophesize(HttpKernelInterface::class)->reveal(), $request, null);

        $this->dispatcher->dispatch($event);

        $this->signerProphecy->validate(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function testValidateSignedRouteFalseSignedRouteParam(): void
    {
        $request = Request::create('http://test.org/valid-signature');
        $request->attributes->set('_route_params', ['_signed' => false]);
        $event = new RequestEvent($this->prophesize(HttpKernelInterface::class)->reveal(), $request, null);

        $this->dispatcher->dispatch($event);

        $this->signerProphecy->validate(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function testValidateSignedRouteInvalidSignature(): void
    {
        $this->expectException(AccessDeniedHttpException::class);
        $this->expectExceptionMessage('URL is either missing a valid signature or have a bad signature.');

        $request = Request::create('http://test.org/invalid-signature');
        $request->attributes->set('_route_params', ['_signed' => true]);
        $event = new RequestEvent($this->prophesize(HttpKernelInterface::class)->reveal(), $request, null);

        $this->signerProphecy->validate('/invalid-signature')->willReturn(false);
        $this->signerProphecy->validate('http://test.org/invalid-signature')->willReturn(false);

        $this->dispatcher->dispatch($event);

        $this->signerProphecy->validate(Argument::any())->shouldHaveBeenCalledOnce();
    }

    /** @return iterable<string, array<string, string>> */
    public function provideValidateSignedRouteCases(): iterable
    {
        yield 'absolutePath' => [
            'validUrl' => '/valid-signature',
        ];

        yield 'absoluteUrl' => [
            'validUrl' => 'http://test.org/valid-signature',
        ];
    }
}
