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
use Psr\Http\Message\UriInterface;

/**
 * @internal
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

            /**
             * @param UriInterface|string $url
             */
            protected function createSignature($url, string $expiration): string
            {
                $url = (string) $url;

                return "{$url}::{$expiration}::{$this->signatureKey}";
            }

            protected function getExpirationTimestamp($expiration): string
            {
                return $expiration instanceof \DateTime ? 'datetime' : (string) $expiration;
            }
        };
    }

    public function testSignDefaultExpiration(): void
    {
        $signedUrl = $this->signer->sign('http://test.org/valid-signature');

        static::assertSame('http://test.org/valid-signature?exp=5&sign=http://test.org/valid-signature::5::secret', $signedUrl);
    }

    public function testSignWithExpiration(): void
    {
        $signedUrl = $this->signer->sign('http://test.org/valid-signature', 7);

        static::assertSame('http://test.org/valid-signature?exp=7&sign=http://test.org/valid-signature::7::secret', $signedUrl);
    }
}
