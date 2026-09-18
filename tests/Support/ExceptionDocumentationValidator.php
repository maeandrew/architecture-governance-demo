<?php

declare(strict_types=1);

namespace Tests\Support;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

final class ExceptionDocumentationValidator
{
    /**
     * @param array<string, mixed> $config
     * @param array<int, string> $adrFiles
     */
    public function __construct(private array $config, private array $adrFiles) {}

    public static function fromPaths(string $configPath, string $adrDirectory): self
    {
        if (!is_file($configPath)) {
            throw new RuntimeException(sprintf('Config file not found: %s', $configPath));
        }

        $config = Yaml::parseFile($configPath);
        /** @var list<string> $files */
        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($adrDirectory, \FilesystemIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if ($file instanceof \SplFileInfo && $file->isFile() && str_ends_with((string) $file->getFilename(), '.md')) {
                $files[] = $file->getPathname();
            }
        }

        /** @var array<string, mixed> $parsed */
        $parsed = is_array($config) ? $config : [];

        return new self($parsed, $files);
    }

    /**
     * @return array<int, array{class: string, dependency: string}>
     */
    public function collectSkipViolations(): array
    {
        $deptrac = $this->config['deptrac'] ?? [];
        $skipViolations = is_array($deptrac) ? ($deptrac['skip_violations'] ?? []) : [];
        $entries = [];

        if (!is_array($skipViolations)) {
            return $entries;
        }

        foreach ($skipViolations as $source => $dependencies) {
            if (!is_array($dependencies)) {
                continue;
            }

            foreach ($dependencies as $dependency) {
                if (is_string($dependency)) {
                    $entries[] = ['class' => (string) $source, 'dependency' => $dependency];
                }
            }
        }

        return $entries;
    }

    public function assertDocumentationIsComplete(): void
    {
        foreach ($this->collectSkipViolations() as $entry) {
            $source = $entry['class'];
            $dependency = $entry['dependency'];
            $reference = sprintf('%s -> %s', $source, $dependency);
            $hasReference = false;

            foreach ($this->adrFiles as $adrFile) {
                $contents = file_get_contents($adrFile);
                if ($contents !== false && str_contains($contents, $reference)) {
                    $hasReference = true;
                    break;
                }
            }

            if (!$hasReference) {
                throw new InvalidArgumentException(sprintf('Skip violation %s has no ADR reference.', $reference));
            }
        }
    }

    public function validateAdrStructure(string $adrFile): void
    {
        $contents = file_get_contents($adrFile);
        if ($contents === false) {
            throw new RuntimeException(sprintf('Unable to read ADR file: %s', $adrFile));
        }

        foreach (['## Status', '## Context', '## Decision', '## Consequences', '## Review'] as $section) {
            if (!str_contains($contents, $section)) {
                throw new InvalidArgumentException(sprintf('ADR %s is missing required section: %s', $adrFile, $section));
            }
        }
    }
}
