<?php

declare(strict_types=1);

namespace Modules\Billing\Domain;

use Modules\Shared\ValueObjects\Money;

final class Invoice
{
    public function __construct(
        public readonly string $bookingId,
        public readonly string $userId,
        public readonly Money $amount,
    ) {}
}
