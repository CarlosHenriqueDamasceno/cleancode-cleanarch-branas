<?php

namespace App;

readonly class SignupInput
{
    public function __construct(
        public string $name,
        public string $email,
        public string $cpf,
        public string $password,
        public bool $isPassenger,
        public bool $isDriver = false,
        public string|null $carPlate = null
    ) {}
}