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

namespace CoopTilleuls\UrlSignerBundle\UrlSigner;

use Spatie\UrlSigner\MD5UrlSigner as SpatieMd5UrlSigner;

final class Md5UrlSigner extends SpatieMd5UrlSigner implements UrlSignerInterface
{
    public function getName(): string
    {
        return 'md5';
    }
}
