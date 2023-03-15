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

final class Md5UrlSigner extends AbstractUrlSigner
{
    public static function getName(): string
    {
        return 'md5';
    }

    /**
     * Generate a token to identify the secure action.
     */
    protected function createSignature(string $url, string $expiration, string $signatureKey): string
    {
        return hash_hmac(self::getName(), "{$url}::{$expiration}", $signatureKey);
    }
}
