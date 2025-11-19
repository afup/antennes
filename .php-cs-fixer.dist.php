<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PER-CS3x0' => true,
        'no_unused_imports' => true,
    ])
    ->setFinder($finder)
;
