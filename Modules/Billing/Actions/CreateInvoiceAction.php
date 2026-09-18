<?php

declare(strict_types=1);

namespace Modules\Billing\Actions;

use Modules\Billing\Domain\Invoice;
use Modules\Identity\Contracts\UserIdentityInterface;
use Modules\Shared\ValueObjects\Money;

final class CreateInvoiceAction
{
    public function __construct(
        private readonly UserIdentityInterface $user,
    ) {}

    public function execute(string $bookingId): Invoice
    {
        return new Invoice($bookingId, $this->user->userId(), new Money(1250, 'USD'));
    }
}
