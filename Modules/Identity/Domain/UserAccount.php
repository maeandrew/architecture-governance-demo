<?php

declare(strict_types=1);

namespace Modules\Identity\Domain;

use Modules\Identity\Contracts\UserIdentityInterface;

final class UserAccount implements UserIdentityInterface
{
    public function __construct(
        private readonly string $id,
        private readonly string $displayName,
    ) {}

    public function userId(): string
    {
        return $this->id;
    }

    public function displayName(): string
    {
        return $this->displayName;
    }
}
