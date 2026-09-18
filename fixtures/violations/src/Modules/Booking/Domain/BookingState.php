<?php

declare(strict_types=1);

namespace Modules\Booking\Domain;

final class BookingState
{
    public function __construct(public readonly bool $confirmed = false) {}
}
