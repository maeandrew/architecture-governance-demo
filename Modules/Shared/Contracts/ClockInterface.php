<?php

declare(strict_types=1);

namespace Modules\Shared\Contracts;

interface ClockInterface
{
    public function now(): \DateTimeImmutable;
}
