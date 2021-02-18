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

use Spatie\UrlSigner\UrlSigner;

interface UrlSignerInterface extends UrlSigner
{
    /**
     * @param string             $url
     * @param \DateTime|int|null $expiration
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function sign($url, $expiration = null): string;

    public static function getName(): string;
}
