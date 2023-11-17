<?php

declare(strict_types=1);

namespace App\Core\Account;

use Illuminate\Support\Facades\DB;
use stdClass;

class AccountDAODatabase implements AccountDAO
{
    public function save(Account $account): Account
    {
        DB::insert(
            "insert into cccat14.account (
                         account_id,
                         name,
                         email,
                         password,
                         cpf,
                         car_plate,
                         is_passenger,
                         is_driver
                    ) values (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $account->accountId,
                $account->name,
                $account->email,
                $account->password,
                $account->cpf,
                $account->carPlate,
                $account->isPassenger,
                $account->isDriver,
            ]
        );
        return $account;
    }

    public function getById(string $id): ?Account
    {
        $results = DB::select('select * from cccat14.account where account_id = :account_id', ['account_id' => $id]);
        if (empty($results)) return null;
        return $this->mapRecordToAccount($results[0]);
    }
    public function getByEmail(string $email): ?Account
    {
        $results = DB::select('select * from cccat14.account where email = :email', ['email' => $email]);
        if (empty($results)) return null;
        return $this->mapRecordToAccount($results[0]);
    }

    private function mapRecordToAccount(stdClass $record): Account
    {
        return new Account(
            $record->account_id,
            $record->name,
            $record->email,
            $record->cpf,
            $record->password,
            $record->is_passenger,
            $record->is_driver,
            $record->car_plate
        );
    }
}
