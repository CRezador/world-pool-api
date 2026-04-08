<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->exclude([
        'vendor',
        'storage',
    ]);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,

        // Arrays
        'array_indentation' => true,
        'array_syntax' => ['syntax' => 'short'],
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,

        // Strings
        'single_quote' => true,

        // Imports
        'no_unused_imports' => true,

        // Espaçamento
        'binary_operator_spaces' => true,
        'unary_operator_spaces' => true,
        'concat_space' => ['spacing' => 'one'],
        'no_spaces_around_offset' => true,

        // Código
        'no_whitespace_in_blank_line' => true,
        'single_blank_line_before_namespace' => true,
        'blank_line_before_statement' => true,

        // Sintaxe moderna
        'trailing_comma_in_multiline' => true,
        'declare_equal_normalize' => true,

        // Limpeza
        'no_whitespace_before_comma_in_array' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'object_operator_without_whitespace' => true,
    ])
    ->setIndent('    ') // 4 espaços (PSR-12)
    ->setLineEnding("\n")
    ->setFinder($finder);
