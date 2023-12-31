<?php

declare(strict_types=1);

namespace  App\Core\Account\GetAccount;

readonly class GetAccountOutput
{
    public function __construct(
        public string $accountId,
        public string $name,
        public string $email,
        public string $cpf,
        public bool $isPassenger,
        public bool $isDriver = false,
        public string|null $carPlate = null,
    ) {
    }
}
