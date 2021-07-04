lines (14 sloc)  306 Bytes
  
<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->in([
        __DIR__ . '/src',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new \PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,
    ])
    ->setFinder($finder);