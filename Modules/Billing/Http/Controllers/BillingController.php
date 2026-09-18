<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use Modules\Billing\Actions\CreateInvoiceAction;
use Modules\Billing\Domain\Invoice;
use Modules\Identity\Contracts\UserIdentityInterface;

final class BillingController
{
    public function __construct(
        private readonly CreateInvoiceAction $action,
        private readonly UserIdentityInterface $user,
    ) {}

    public function issue(string $bookingId): Invoice
    {
        if ($this->user->userId() === '') {
            throw new \LogicException('An invoice requires an identified user.');
        }

        return $this->action->execute($bookingId);
    }
}
