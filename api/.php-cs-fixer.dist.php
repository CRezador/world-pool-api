<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@auto' => true,
        '@auto:risky' => true,
        '@PhpCsFixer:risky' => true,
        'declare_strict_types' => false,
        'static_lambda' => false,
        'void_return' => false,
    ])
    // 💡 by default, Fixer looks for `*.php` files excluding `./vendor/` - here, you can groom this config
    ->setFinder(
        (new Finder())
            ->in(__DIR__)
            ->exclude(['bootstrap/cache', 'vendor', 'storage'])
    )
;
