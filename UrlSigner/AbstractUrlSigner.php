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

use Spatie\UrlSigner\BaseUrlSigner;

abstract class AbstractUrlSigner extends BaseUrlSigner implements UrlSignerInterface
{
    private int $defaultExpiration;

    public function __construct(string $signatureKey, int $defaultExpiration, string $expiresParameter, string $signatureParameter)
    {
        parent::__construct($signatureKey, $expiresParameter, $signatureParameter);

        $this->defaultExpiration = $defaultExpiration;
    }

    /**
     * @param string             $url
     * @param \DateTime|int|null $expiration
     */
    public function sign($url, $expiration = null): string
    {
        return parent::sign($url, $expiration ?? $this->defaultExpiration);
    }
}
