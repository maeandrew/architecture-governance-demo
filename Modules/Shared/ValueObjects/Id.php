<?php

declare(strict_types=1);

namespace Modules\Shared\ValueObjects;

final class Id
{
    public function __construct(public readonly string $value) {}
}
