<?php

declare(strict_types=1);

namespace Modules\Billing\Domain;

use Modules\Booking\Domain\BookingState;

final class InvoiceFactory
{
    public function __construct(private BookingState $state)
    {
        if (!$this->state->confirmed) {
            throw new \LogicException('Invoice cannot be created for an unconfirmed booking.');
        }
    }
}
