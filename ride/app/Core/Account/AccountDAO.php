<?php

declare(strict_types=1);

namespace App\Core\Account;

interface AccountDAO
{
    public function save(Account $account): Account;
    public function getById(string $id): ?Account;
    public function getByEmail(string $email): ?Account;
}
