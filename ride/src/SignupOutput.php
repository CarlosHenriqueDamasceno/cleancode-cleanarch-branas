<?php

namespace App;

readonly class SignupOutput
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $cpf,
        public string $password,
        public bool $isPassenger,
        public bool $isDriver = false,
        public string|null $carPlate = null,
    ) {}
}