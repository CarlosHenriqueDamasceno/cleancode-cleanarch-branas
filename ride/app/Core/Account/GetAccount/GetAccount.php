<?php

declare(strict_types=1);

namespace App\Core\Account\GetAccount;

use App\Core\Account\AccountDAO;

class GetAccount
{

    public function __construct(private AccountDAO $accountDAO)
    {
    }

    public function execute(string $id): ?GetAccountOutput
    {
        $account = $this->accountDAO->getById($id);
        if (is_null($account)) return null;
        return new GetAccountOutput(
            accountId: $account->accountId,
            name: $account->name,
            email: $account->email,
            cpf: $account->cpf,
            isPassenger: $account->isPassenger,
            isDriver: $account->isDriver,
            carPlate: $account->carPlate
        );
    }
}
