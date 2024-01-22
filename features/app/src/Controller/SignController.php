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

namespace CoopTilleuls\UrlSignerBundle\Tests\Controller;

use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class SignController extends AbstractController
{
    private UrlSignerInterface $urlSigner;

    public function __construct(UrlSignerInterface $urlSigner)
    {
        $this->urlSigner = $urlSigner;
    }

    public function __invoke(Request $request): Response
    {
        $referenceType = $request->query->get('referenceType', (string) UrlGeneratorInterface::ABSOLUTE_PATH);
        if (!is_numeric($referenceType)) {
            throw new \UnexpectedValueException('referenceType query parameter needs to be numeric.');
        }
        $referenceType = (int) $referenceType;

        return new Response($this->urlSigner->sign($this->generateUrl('secured_document', ['id' => 42], $referenceType), 3));
    }
}
