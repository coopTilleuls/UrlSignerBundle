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

namespace Tests\Behat;

use Behat\Behat\Context\Context;
use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UrlSignerContext implements Context
{
    private KernelBrowser $client;
    private UrlSignerInterface $urlSigner;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(KernelBrowser $client, UrlSignerInterface $urlSigner, UrlGeneratorInterface $urlGenerator)
    {
        $this->client = $client;
        $this->urlSigner = $urlSigner;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @When I create a signed URL
     */
    public function iCreateASignedUrl(): void
    {
        $this->client->request('GET', '/sign');
    }

    /**
     * @When I create an absolute signed URL
     */
    public function iCreateAnAbsoluteSignedUrl(): void
    {
        $this->client->request('GET', '/sign?referenceType='.UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @When I request the signed URL
     */
    public function iRequestTheSignedUrl(): void
    {
        $signedUrl = $this->client->getResponse()->getContent();
        if (!$signedUrl) {
            throw new \RuntimeException('No signed URL received.');
        }
        $this->client->request('GET', $signedUrl);
    }

    /**
     * @When I request a signed route without a valid signature
     */
    public function iRequestASignedRouteWithoutAValidSignature(): void
    {
        $signedRoute = $this->urlGenerator->generate('secured_document', ['id' => 42]);

        $this->client->request('GET', $signedRoute);
    }

    /**
     * @Then I should get a valid signed URL
     */
    public function iShouldGetAValidSignedUrl(): void
    {
        $signedUrl = $this->client->getResponse()->getContent();
        if (!$signedUrl) {
            throw new \RuntimeException('No signed URL received.');
        }
        if (!$this->urlSigner->validate($signedUrl)) {
            throw new \RuntimeException('The signature is invalid.');
        }
    }

    /**
     * @Then I should receive a successful response
     */
    public function iShouldReceiveASuccessfulResponse(): void
    {
        if (!$this->client->getResponse()->isOk()) {
            throw new \RuntimeException('The response is not successful.');
        }
    }

    /**
     * @Then I should receive a forbidden response
     */
    public function iShouldReceiveAForbiddenResponse(): void
    {
        if (!$this->client->getResponse()->isForbidden()) {
            throw new \RuntimeException('The response is not forbidden.');
        }
    }
}
