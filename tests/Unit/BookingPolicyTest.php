<?php

declare(strict_types=1);

use Modules\Booking\Domain\BookingPolicy;
use Modules\Catalog\Domain\EventStatus;

it('allows booking an available event', function () {
    expect((new BookingPolicy())->canPlaceBooking(new EventStatus(true)))->toBeTrue();
});

it('rejects booking an unavailable event', function () {
    expect((new BookingPolicy())->canPlaceBooking(new EventStatus(false)))->toBeFalse();
});
