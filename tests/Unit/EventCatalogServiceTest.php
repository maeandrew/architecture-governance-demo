<?php

declare(strict_types=1);

use Modules\Catalog\Services\InMemoryEventCatalogService;

it('finds seeded events by identifier', function () {
    $catalog = InMemoryEventCatalogService::fromSeed();

    expect($catalog->findById('evt_01'))->toBe('Spring Launch')
        ->and($catalog->findById('missing'))->toBeNull();
});

it('exposes the availability status of seeded events', function () {
    $catalog = InMemoryEventCatalogService::fromSeed();

    expect($catalog->statusOf('evt_01')?->isAvailable())->toBeTrue()
        ->and($catalog->statusOf('evt_02')?->isAvailable())->toBeFalse()
        ->and($catalog->statusOf('missing'))->toBeNull();
});
