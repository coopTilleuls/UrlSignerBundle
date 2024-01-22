<?php

declare(strict_types=1);

$header = <<<'HEADER'
This file is part of CoopTilleulsUrlSignerBundle.

(c) Les-Tilleuls.coop <contact@les-tilleuls.coop>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHPUnit100Migration:risky' => true,
        'declare_strict_types' => true,
        'final_class' => true,
        'header_comment' => [
            'header' => $header,
            'location' => 'after_open',
        ],
        'method_chaining_indentation' => false,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true],
        'php_unit_internal_class' => false,
        'php_unit_test_class_requires_covers' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude(['vendor', 'features/app/var'])
    )
;
