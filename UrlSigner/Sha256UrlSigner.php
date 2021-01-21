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

use Psr\Http\Message\UriInterface;
use Spatie\UrlSigner\BaseUrlSigner;

final class Sha256UrlSigner extends BaseUrlSigner implements UrlSignerInterface
{
    public static function getName(): string
    {
        return 'sha256';
    }

    /**
     * Generate a token to identify the secure action.
     *
     * @param UriInterface|string $url
     */
    protected function createSignature($url, string $expiration): string
    {
        $url = (string) $url;

        return hash_hmac('sha256', "{$url}::{$expiration}", $this->signatureKey);
    }
}
