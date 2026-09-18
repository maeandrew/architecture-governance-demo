<?php

declare(strict_types=1);

$modulesRoot = __DIR__ . '/../../Modules';

$conventions = [
    'Http/Controllers' => ['suffix' => 'Controller'],
    'Actions' => ['suffix' => 'Action'],
    'Services' => ['suffix' => 'Service'],
    'Contracts' => ['suffix' => 'Interface'],
];

// Only generate checks for directories that exist, so the suite does not report vacuous passes.
foreach (glob($modulesRoot . '/*', GLOB_ONLYDIR) ?: [] as $moduleDirectory) {
    $module = basename($moduleDirectory);

    foreach ($conventions as $directory => $convention) {
        if (!is_dir($moduleDirectory . '/' . $directory)) {
            continue;
        }

        $namespace = sprintf('Modules\\%s\\%s', $module, str_replace('/', '\\', $directory));

        arch(sprintf('%s uses the %s suffix', $namespace, $convention['suffix']), function () use ($namespace, $convention) {
            expect($namespace)->toHaveSuffix($convention['suffix']);
        });
    }

    if (is_dir($moduleDirectory . '/Contracts')) {
        $contracts = sprintf('Modules\\%s\\Contracts', $module);

        arch(sprintf('%s contains only interfaces', $contracts), function () use ($contracts) {
            expect($contracts)->toBeInterfaces();
        });
    }
}
