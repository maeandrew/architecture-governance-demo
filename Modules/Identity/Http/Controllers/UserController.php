<?php

declare(strict_types=1);

namespace Modules\Identity\Http\Controllers;

use Modules\Identity\Contracts\UserIdentityInterface;

final class UserController
{
    public function __construct(private readonly UserIdentityInterface $user)
    {
    }

    public function showName(): string
    {
        return $this->user->displayName();
    }
}
