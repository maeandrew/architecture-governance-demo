<?php

declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;

/** @return list<string> */
function modulePhpFiles(): array
{
    $files = [];
    $iterator = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator(__DIR__ . '/../../Modules', \FilesystemIterator::SKIP_DOTS),
    );

    foreach ($iterator as $file) {
        if ($file instanceof \SplFileInfo && $file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }

    return $files;
}

function currentModule(string $content): ?string
{
    if (preg_match('/namespace Modules\\\\([^\\\\;]+)\\\\/', $content, $matches) !== 1) {
        return null;
    }

    return $matches[1];
}

/** @return array<string, list<string>> */
function documentedSkipViolations(): array
{
    $config = Yaml::parseFile(__DIR__ . '/../../deptrac.yaml');
    $deptrac = is_array($config) && is_array($config['deptrac'] ?? null) ? $config['deptrac'] : [];
    $skips = $deptrac['skip_violations'] ?? [];

    if (!is_array($skips)) {
        return [];
    }

    $result = [];
    foreach ($skips as $class => $dependencies) {
        if (is_string($class) && is_array($dependencies)) {
            $result[$class] = array_values(array_filter($dependencies, 'is_string'));
        }
    }

    return $result;
}

function isDocumentedException(string $class, string $dependency): bool
{
    $skips = documentedSkipViolations();
    $dependencies = $skips[$class] ?? [];

    return is_array($dependencies) && in_array($dependency, $dependencies, true);
}

// Deptrac is the authoritative boundary check. This test is a fast, dependency-free
// safety net that reads the same skip_violations, so both tools agree on exceptions.
it('keeps module boundaries free of direct internal imports', function () {
    $files = modulePhpFiles();
    $violations = [];

    foreach ($files ?: [] as $file) {
        $content = file_get_contents($file);
        if ($content === false) {
            continue;
        }

        if (str_contains($content, 'namespace Modules\\')) {
            $module = currentModule($content);
            preg_match('/namespace (Modules\\\\[^;]+);/', $content, $namespaceMatch);
            preg_match('/final class ([A-Za-z0-9_]+)/', $content, $classMatch);
            $class = ($namespaceMatch[1] ?? '') . '\\' . ($classMatch[1] ?? '');
            preg_match_all('/^use (Modules\\\\[^;]+);/m', $content, $imports);

            $dependencies = $imports[1];
            foreach ($dependencies as $dependency) {
                $parts = explode('\\', $dependency);
                $targetModule = $parts[1] ?? '';
                $isContract = ($parts[2] ?? '') === 'Contracts';
                $isShared = $targetModule === 'Shared';
                if ($module !== $targetModule && !$isContract && !$isShared
                    && !isDocumentedException($class, $dependency)) {
                    $violations[] = sprintf('%s imports cross-module internal class %s. Use a public contract or document this exact exception in an ADR.', $file, $dependency);
                }
            }
        }
    }

    if ($violations !== []) {
        throw new \RuntimeException(implode("\n", $violations));
    }

    expect(true)->toBeTrue();
})->group('architecture');

arch('debug helpers are not used in modules', function () {
    expect(['dd', 'dump', 'var_dump'])->not->toBeUsed();
});
