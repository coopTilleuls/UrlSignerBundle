<?php

/*
 * This file is part of CoopTilleulsUrlSignerBundle.
 *
 * (c) Les-Tilleuls.coop <contact@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoopTilleuls\UrlSignerBundle\Tests\Controller;

use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SignController extends AbstractController
{
    private UrlSignerInterface $urlSigner;

    public function __construct(UrlSignerInterface $urlSigner)
    {
        $this->urlSigner = $urlSigner;
    }

    public function __invoke(): Response
    {
        return new Response($this->urlSigner->sign($this->generateUrl('secured_document', ['id' => 42]), 3));
    }
}
