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

use Symfony\Component\HttpFoundation\Response;

class DocumentController
{
    public function __invoke(): Response
    {
        return new Response();
    }
}
