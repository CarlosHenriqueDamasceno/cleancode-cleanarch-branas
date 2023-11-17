<?php

declare(strict_types=1);

namespace App\Core\Account;

readonly class Account
{
    public function __construct(
        public string $accountId,
        public string $name,
        public string $email,
        public string $cpf,
        public string $password,
        public bool $isPassenger,
        public bool $isDriver = false,
        public string|null $carPlate = null,
    ) {
    }
}
