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

namespace Tests\UrlSigner;

use CoopTilleuls\UrlSignerBundle\UrlSigner\AbstractUrlSigner;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \CoopTilleuls\UrlSignerBundle\UrlSigner\AbstractUrlSigner
 */
final class AbstractUrlSignerTest extends TestCase
{
    private AbstractUrlSigner $signer;

    protected function setUp(): void
    {
        $this->signer = new class('secret', 5, 'exp', 'sign') extends AbstractUrlSigner {
            public static function getName(): string
            {
                return 'abstract';
            }

            protected function createSignature(string $url, string $expiration, string $signatureKey): string
            {
                return "{$url}::{$expiration}::{$signatureKey}";
            }

            protected function getExpirationTimestamp(int|\DateTimeInterface $expirationInSeconds): string
            {
                return $expirationInSeconds instanceof \DateTimeInterface ? 'datetime' : (string) $expirationInSeconds;
            }
        };
    }

    public function testSignDefaultExpiration(): void
    {
        $signedUrl = $this->signer->sign('http://test.org/valid-signature');

        self::assertSame('http://test.org/valid-signature?exp=5&sign=http%3A%2F%2Ftest.org%2Fvalid-signature%3A%3A5%3A%3Asecret', $signedUrl);
    }

    public function testSignWithExpiration(): void
    {
        $signedUrl = $this->signer->sign('http://test.org/valid-signature', 7);

        self::assertSame('http://test.org/valid-signature?exp=7&sign=http%3A%2F%2Ftest.org%2Fvalid-signature%3A%3A7%3A%3Asecret', $signedUrl);
    }
}
