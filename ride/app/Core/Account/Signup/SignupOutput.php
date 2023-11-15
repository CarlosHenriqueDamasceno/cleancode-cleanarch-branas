<?php

declare(strict_types=1);

namespace  App\Core\Account\Signup;

readonly class SignupOutput
{
    public function __construct(
        public string $accountId,
    ) {
    }
}
