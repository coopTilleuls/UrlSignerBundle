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

use Spatie\UrlSigner\AbstractUrlSigner as SpatieAbstractUrlSigner;

abstract class AbstractUrlSigner extends SpatieAbstractUrlSigner implements UrlSignerInterface
{
    private int $defaultExpiration;

    public function __construct(string $signatureKey, int $defaultExpiration, string $expiresParameter, string $signatureParameter)
    {
        parent::__construct($signatureKey, $expiresParameter, $signatureParameter);

        $this->defaultExpiration = $defaultExpiration;
    }

    public function sign(string $url, int|\DateTimeInterface $expiration = null, string $signatureKey = null): string
    {
        return parent::sign($url, $expiration ?? $this->defaultExpiration, $signatureKey);
    }
}
