<?php

declare(strict_types=1);

use Tests\Support\ExceptionDocumentationValidator;

it('requires every skip violation to have a matching ADR reference', function () {
    $validator = ExceptionDocumentationValidator::fromPaths(__DIR__ . '/../../deptrac.yaml', __DIR__ . '/../../docs/adr');

    expect(fn () => $validator->assertDocumentationIsComplete())
        ->not->toThrow(\InvalidArgumentException::class);
})->group('architecture');

it('rejects undocumented skip violations', function () {
    $validator = ExceptionDocumentationValidator::fromPaths(__DIR__ . '/../../fixtures/exceptions/undocumented-dependency.yaml', __DIR__ . '/../../docs/adr');

    expect(fn () => $validator->assertDocumentationIsComplete())
        ->toThrow(\InvalidArgumentException::class, 'no ADR reference');
})->group('architecture');

it('accepts documented skip violations', function () {
    $validator = ExceptionDocumentationValidator::fromPaths(__DIR__ . '/../../fixtures/exceptions/documented-dependency.yaml', __DIR__ . '/../../fixtures/exceptions/adr');

    expect(fn () => $validator->assertDocumentationIsComplete())
        ->not->toThrow(\InvalidArgumentException::class);
})->group('architecture');

it('requires each ADR to contain the mandatory sections', function () {
    $files = glob(__DIR__ . '/../../docs/adr/*.md');
    $validator = ExceptionDocumentationValidator::fromPaths(__DIR__ . '/../../deptrac.yaml', __DIR__ . '/../../docs/adr');

    foreach ($files ?: [] as $file) {
        $validator->validateAdrStructure($file);
    }

    expect(true)->toBeTrue();
})->group('architecture');
