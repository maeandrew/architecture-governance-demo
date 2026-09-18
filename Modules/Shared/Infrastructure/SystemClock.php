<?php

declare(strict_types=1);

namespace Modules\Shared\Infrastructure;

use DateTimeImmutable;
use Modules\Shared\Contracts\ClockInterface;

final class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now');
    }
}
