# UrlSignerBundle

[![Actions Status](https://github.com/coopTilleuls/UrlSignerBundle/workflows/CI/badge.svg)](https://github.com/coopTilleuls/UrlSignerBundle/actions)
[![Type Coverage](https://shepherd.dev/github/coopTilleuls/UrlSignerBundle/coverage.svg)](https://shepherd.dev/github/coopTilleuls/UrlSignerBundle)

Create and validate signed URLs with a limited lifetime in Symfony.

This bundle is based on [spatie/url-signer](https://github.com/spatie/url-signer).

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Open a command console, enter your project directory and execute:

```console
composer require tilleuls/url-signer-bundle
```

**If you're using [Symfony Flex](https://github.com/symfony/flex), all configuration is already done.
You can customize it in `config/packages/url_signer.yaml` file.**

Otherwise, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    CoopTilleuls\UrlSignerBundle\CoopTilleulsUrlSignerBundle::class => ['all' => true],
];
```

## Configuration

Add a signature key:

```yml
# config/packages/url_signer.yaml
coop_tilleuls_url_signer:
    signature_key: 'your_signature_key'
```

You can change the signer used to create the signature:

```yml
# config/packages/url_signer.yaml
coop_tilleuls_url_signer:
    signer: 'md5' # 'sha256' by default
```

The default expiration time (in days) can be changed too:

```yml
# config/packages/url_signer.yaml
coop_tilleuls_url_signer:
    default_expiration: 3 # 1 by default
```

You can also customize the URL parameter names:

```yml
# config/packages/url_signer.yaml
coop_tilleuls_url_signer:
    expires_parameter: 'exp' # 'expires' by default
    signature_parameter: 'sign' # 'signature' by default
```

## Usage

### Generate a Signed URL

To create a temporary signed URL for a route, you first need to inject the URL signer to your service or controller:

```php
// src/Controller/DocumentController.php
namespace App\Controller;

use CoopTilleuls\UrlSignerBundle\UrlSigner\UrlSignerInterface;

class DocumentController
{
    public function __construct(
        public UrlSignerInterface $urlSigner,
    ) {}
}
```

If autowiring is enabled (the default Symfony configuration) in your application, you have nothing more to do.

Otherwise, inject the `url_signer.signer` service in the configuration:

```yml
# config/services.yaml
services:
    App\Controller\DocumentController:
        arguments:
            $urlSigner: 'url_signer.signer'
```

You can now use the URL signer to generate a signed URL:

```php
// src/Controller/DocumentController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DocumentController extends AbstractController
{
    private function generateSignedUrl(): string
    {
        $url = $this->generateUrl('secured_document', ['id' => 42]);
        // Will expire after one hour.
        $expiration = (new \DateTime('now'))->add(new \DateInterval('PT1H'));
        // An integer can also be used for the expiration: it will correspond to a number of days. For 3 days:
        // $expiration = 3;

        // Not passing the second argument will use the default expiration time (1 day by default).
        // return $this->urlSigner->sign($url);

        // Will return a URL like this: /documents/42?expires=1611316656&signature=82f6958bd5c96fda58b7a55ade7f651fadb51e12171d58ed271e744bcc7c85c3
        return $this->urlSigner->sign($url, $expiration);
    }
}
```

### Validate Signed Route Requests

To deny access to a route if the signature is not valid,
add a `_signed` [extra parameter](https://symfony.com/doc/current/routing.html#extra-parameters) to the route configuration:

```yml
# config/routes.yaml
secured_document:
    path: /documents/{id}
    controller: App\Controller\DocumentController::index
    defaults:
        _signed: true
```

If the signature is invalid (bad signature or expired URL), the request will receive a 403 response (access denied).

## Custom Signer

If you need to use a specific hash algorithm for generating the signature, you can create your own signer.

Create a class extending the `AbstractUrlSigner` class:

```php
// src/UrlSigner/CustomUrlSigner.php
namespace App\UrlSigner;

use CoopTilleuls\UrlSignerBundle\UrlSigner\AbstractUrlSigner;
use Psr\Http\Message\UriInterface;

class CustomUrlSigner extends AbstractUrlSigner
{
    public static function getName(): string
    {
        return 'custom';
    }

    protected function createSignature(UriInterface|string $url, string $expiration): string
    {
        $url = (string) $url;

        return hash_hmac('algo', "{$url}::{$expiration}", $this->signatureKey);
    }
}
```

If autoconfiguring is enabled (the default Symfony configuration) in your application, you are done.

Otherwise, register and tag your service:

```yml
# config/services.yaml
services:
    App\UrlSigner\CustomUrlSigner:
        # You don't need to specify the arguments
        tags: ['url_signer.signer']
```

You can now use your custom signer:

```yml
# config/packages/url_signer.yaml
coop_tilleuls_url_signer:
    signer: 'custom'
```

## Credits

Created by [Alan Poulain](https://github.com/alanpoulain) for [Les-Tilleuls.coop](https://les-tilleuls.coop/).
