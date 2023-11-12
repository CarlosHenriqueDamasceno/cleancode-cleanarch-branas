<?php

declare(strict_types=1);

namespace Ride\Account\GetAccount;

class GetAccount
{
    public function execute(string $id): GetAccountOutput
    {
        $pdoConnection = new \PDO('pgsql:host=database;', "postgres", "123456");
        $queryForAccount = $pdoConnection->prepare("select * from cccat14.account where account_id = ?");
        $queryForAccount->execute([$id]);
        $account = $queryForAccount->fetch();
        return new GetAccountOutput(
            id: $account['account_id'],
            name: $account['name'],
            email: $account['email'],
            cpf: $account['cpf'],
            password: $account['password'],
            isPassenger: $account['is_passenger'],
            isDriver: $account['is_driver'],
            carPlate: $account['car_plate']
        );
    }
}