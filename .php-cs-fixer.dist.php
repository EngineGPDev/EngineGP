<?php
use PhpCsFixer\Config;
use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

ini_set("memory_limit", -1); 

$finder = (new Finder())
    ->in(__DIR__)
    ->exclude([
        'vendor',
    ])
;

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@PSR1' => true,
        '@PSR12' => true,
    ])
    ->setFinder($finder)
;
