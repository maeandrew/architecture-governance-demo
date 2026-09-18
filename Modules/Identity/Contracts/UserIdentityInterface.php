<?php

declare(strict_types=1);

namespace Modules\Identity\Contracts;

interface UserIdentityInterface
{
    public function userId(): string;

    public function displayName(): string;
}
