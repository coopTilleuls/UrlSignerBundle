<?php

declare(strict_types=1);

namespace CoopTilleuls\UrlSignerBundle\DependencyInjection\Compiler;

use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SignerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $signers = $this->getSigners($container);
        $signerName = $container->getParameter('url_signer.signer');

        foreach ($signers as $name => $signerId) {
            if ($name === $signerName) {
                $container->setAlias('url_signer.signer', $signerId);
                $container->setAlias(UrlSignerInterface::class, $signerId);
            }
        }
    }

    /**
     * @return array<string, string>
     */
    private function getSigners(ContainerBuilder $container): array
    {
        $signers = [];

        $signerServices = $container->findTaggedServiceIds('url_signer.signer');
        foreach ($signerServices as $signerServiceId => $signerServiceTags) {
            $signerService = $container->get($signerServiceId);

            $signers[$signerService->getName()] = $signerServiceId;
        }

        return $signers;
    }
}
