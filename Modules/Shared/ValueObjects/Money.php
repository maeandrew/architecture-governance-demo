<?php

declare(strict_types=1);

namespace Modules\Shared\ValueObjects;

final class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency,
    ) {}
}
