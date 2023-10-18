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

use Spatie\UrlSigner\Contracts\UrlSigner;

interface UrlSignerInterface extends UrlSigner
{
    public function sign(string $url, \DateTimeInterface|int $expiration, string $signatureKey = null): string;

    public static function getName(): string;
}
