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

namespace CoopTilleuls\UrlSignerBundle\DependencyInjection\Compiler;

use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

final class SignerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $signers = $this->getSigners($container);
        /** @var string $signerName */
        $signerName = $container->getParameter('url_signer.signer');
        $availableNames = [];

        foreach ($signers as $name => $signerId) {
            if ($name === $signerName) {
                $container->setAlias('url_signer.signer', $signerId);
                $container->setAlias(UrlSignerInterface::class, $signerId);

                return;
            }

            $availableNames[] = $name;
        }

        throw new InvalidArgumentException(sprintf("No URL signer with the name \"%s\" found. Available names are:\n%s", $signerName, implode("\n", array_map(static function (string $availableName) { return sprintf('- "%s"', $availableName); }, $availableNames))));
    }

    /**
     * @return array<string, string>
     */
    private function getSigners(ContainerBuilder $container): array
    {
        $signers = [];

        /** @var array<string, string[]> $signerServices */
        $signerServices = $container->findTaggedServiceIds('url_signer.signer');
        foreach ($signerServices as $signerServiceId => $signerServiceTags) {
            $signerServiceDefinition = $container->getDefinition($signerServiceId);
            $signerServiceDefinition->setBindings([
                'string $signatureKey' => '%url_signer.signature_key%',
                'int $defaultExpiration' => '%url_signer.default_expiration%',
                'string $expiresParameter' => '%url_signer.expires_parameter%',
                'string $signatureParameter' => '%url_signer.signature_parameter%',
            ]);

            /** @var class-string<UrlSignerInterface> $signerServiceClass */
            $signerServiceClass = $signerServiceDefinition->getClass();

            $signers[$signerServiceClass::getName()] = $signerServiceId;
        }

        return $signers;
    }
}
