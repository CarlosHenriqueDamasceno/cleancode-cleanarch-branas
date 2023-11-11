<?php

namespace App\Signup;

readonly class SignupOutput
{
    public function __construct(
        public string $accountId,
    ) {}
}