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

use CoopTilleuls\UrlSignerBundle\UrlSigner\Sha256UrlSigner;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \CoopTilleuls\UrlSignerBundle\UrlSigner\Sha256UrlSigner
 */
final class Sha256UrlSignerTest extends TestCase
{
    private Sha256UrlSigner $signer;

    protected function setUp(): void
    {
        $this->signer = new Sha256UrlSigner('secret', 5, 'exp', 'sign');
    }

    public function testCreateSignature(): void
    {
        $url = 'http://test.org/valid-signature';
        $signedUrl = $this->signer->sign($url);

        static::assertFalse($this->signer->validate($url));
        static::assertTrue($this->signer->validate($signedUrl));
    }

    public function testGetName(): void
    {
        static::assertSame('sha256', Sha256UrlSigner::getName());
    }
}
