<?php

declare(strict_types=1);

namespace Ride\Account\Signup;

readonly class SignupOutput
{
    public function __construct(
        public string $accountId,
    ) {}
}