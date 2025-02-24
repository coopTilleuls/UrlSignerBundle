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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AbstractUrlSigner::class)]
final class AbstractUrlSignerTest extends TestCase
{
    private AbstractUrlSigner $signer;
    private AbstractUrlSigner $signerDateTime;

    #[\Override]
    protected function setUp(): void
    {
        $this->signer = new Signer('secret', 5, 'exp', 'sign');
        $this->signerDateTime = new Signer('secret', '2023-09-14', 'exp', 'sign');
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

    public function testSignDefaultExpirationWithDateTime(): void
    {
        $signedUrl = $this->signerDateTime->sign('http://test.org/valid-signature');

        self::assertSame('http://test.org/valid-signature?exp=1694649600&sign=http%3A%2F%2Ftest.org%2Fvalid-signature%3A%3A1694649600%3A%3Asecret', $signedUrl);
    }

    public function testSignWithExpirationWithDateTime(): void
    {
        $signedUrl = $this->signerDateTime->sign('http://test.org/valid-signature', 7);

        self::assertSame('http://test.org/valid-signature?exp=7&sign=http%3A%2F%2Ftest.org%2Fvalid-signature%3A%3A7%3A%3Asecret', $signedUrl);
    }
}

final class Signer extends AbstractUrlSigner
{
    #[\Override]
    public static function getName(): string
    {
        return 'abstract';
    }

    #[\Override]
    protected function createSignature(string $url, string $expiration, string $signatureKey): string
    {
        return "{$url}::{$expiration}::{$signatureKey}";
    }

    #[\Override]
    protected function getExpirationTimestamp(\DateTimeInterface|int $expirationInSeconds): string
    {
        return (string) ($expirationInSeconds instanceof \DateTimeInterface ? $expirationInSeconds->getTimestamp() : $expirationInSeconds);
    }
}
