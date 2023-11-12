<?php

declare(strict_types=1);

namespace Ride\Account\Signup;

use Ramsey\Uuid\Uuid;

class Signup
{

    function execute(SignupInput $input): SignupOutput
    {
        $accountId = Uuid::uuid4()->toString();
        $pdoConnection = new \PDO('pgsql:host=database;', "postgres", "123456");
        $accountWithSameEmailStatement = $pdoConnection->prepare(
            "select count(account_id) from cccat14.account where email = ?"
        );
        $accountWithSameEmailStatement->execute([$input->email]);
        $accountCountWithSameEmail = $accountWithSameEmailStatement->fetchColumn();
        if ($accountCountWithSameEmail > 0) throw new \Exception("Duplicated account");
        if ($this->isInvalidName($input->name)) throw new \Exception("Invalid name");
        if ($this->isInvalidEmail($input->email)) throw new \Exception("Invalid email");
        if (!$this->validateCpf($input->cpf)) throw new \Exception("Invalid cpf");
        if ($input->isDriver && $this->isInvalidCarPlate($input->carPlate)) {
            throw new \Exception("Invalid car plate");
        }
        $insertStatement = $pdoConnection->prepare(
            "insert into cccat14.account (
                         account_id,
                         name,
                         email,
                         password,
                         cpf,
                         car_plate,
                         is_passenger,
                         is_driver
                    ) values (:account_id, :name, :email, :password, :cpf, :car_plate, :is_passenger, :is_driver)"
        );
        $insertStatement->bindValue(':account_id', $accountId);
        $insertStatement->bindValue(':name', $input->name);
        $insertStatement->bindValue(':email', $input->email);
        $insertStatement->bindValue(':password', $input->password);
        $insertStatement->bindValue(':cpf', $input->cpf);
        $insertStatement->bindValue(':car_plate', $input->carPlate);
        $insertStatement->bindValue(':is_passenger', $input->isPassenger, \PDO::PARAM_BOOL);
        $insertStatement->bindValue(':is_driver', $input->isDriver, \PDO::PARAM_BOOL);
        $insertSuccess = $insertStatement->execute();
        if ($insertSuccess) {
            return new SignupOutput(
                accountId: $accountId,
            );
        } else {
            throw new \Exception("Sql statement error");
        }
    }

    private function isInvalidName(string $name): bool
    {
        return preg_match("/[a-zA-Z] [a-zA-Z]+/", $name) == 0;
    }

    private function isInvalidEmail(string $email): bool
    {
        return preg_match("/^(.+)@(.+)$/", $email) == 0;
    }

    private function isInvalidCarPlate(string $carPlate): bool
    {
        return preg_match("/[A-Z]{3}[0-9]{4}/", $carPlate) == 0;
    }

    private function validateCpf(string $cpf): bool
    {
        if (!$cpf) return false;
        $cpf = $this->clean($cpf);
        if ($this->isInvalidLength($cpf)) return false;
        if ($this->allDigitsAreTheSame($cpf)) return false;
        $dg1 = $this->calculateDigit($cpf, 10);
        $dg2 = $this->calculateDigit($cpf, 11);
        return $this->extractCheckDigit($cpf) === $dg1 . $dg2;
    }

    private function clean(string $cpf): string
    {
        return preg_replace('/[^0-9]/', "", $cpf);
    }

    private function isInvalidLength(string $cpf): bool
    {
        return strlen($cpf) !== 11;
    }

    function allDigitsAreTheSame(string $cpf): bool
    {
        return count(array_unique(str_split($cpf))) === 1;
    }

    function calculateDigit(string $cpf, int $factor): int
    {
        $total = 0;
        foreach (str_split($cpf) as $digit) {
            if ($factor > 1) $total += $digit * $factor--;
        }
        $rest = $total % 11;
        return ($rest < 2) ? 0 : 11 - $rest;
    }

    function extractCheckDigit(string $cpf): string
    {
        return substr($cpf, -2);
    }
}


