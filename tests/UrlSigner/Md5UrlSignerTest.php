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

use CoopTilleuls\UrlSignerBundle\UrlSigner\Md5UrlSigner;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Md5UrlSigner::class)]
final class Md5UrlSignerTest extends TestCase
{
    private Md5UrlSigner $signer;

    #[\Override]
    protected function setUp(): void
    {
        $this->signer = new Md5UrlSigner('secret', 5, 'exp', 'sign');
    }

    public function testCreateSignature(): void
    {
        $url = 'http://test.org/valid-signature';
        $signedUrl = $this->signer->sign($url);

        self::assertFalse($this->signer->validate($url));
        self::assertTrue($this->signer->validate($signedUrl));
    }

    public function testGetName(): void
    {
        self::assertSame('md5', Md5UrlSigner::getName());
    }
}
