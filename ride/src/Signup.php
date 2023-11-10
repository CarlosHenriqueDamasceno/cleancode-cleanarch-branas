<?php

namespace App;

use Ramsey\Uuid\Uuid;

class Signup
{

    function execute(SignupInput $input): SignupOutput
    {
        $pdoConnection = new \PDO('postgres:host=localhost;dbname=cccat14', "root", "123456");
        try {
            $accountId = Uuid::uuid4()->toString();
            $queryForAccountWithSameEmail = $pdoConnection->prepare("select * from account where email = ?");
            $queryForAccountWithSameEmail->execute([$input->email]);
            $accountWithSameEmail = $queryForAccountWithSameEmail->fetch();
            if (isset($accountWithSameEmail)) throw new \Exception("Duplicated account");
            if ($this->isInvalidName($input->name)) throw new \Exception("Invalid name");
            if ($this->isInvalidEmail($input->email)) throw new \Exception("Invalid email");
            if (!$this->validateCpf($input->cpf)) throw new \Exception("Invalid cpf");
            if ($input->isDriver && $this->isInvalidCarPlate($input->carPlate)) {
                throw new \Exception("Invalid car plate");
            }
            $insertStatement = $pdoConnection->prepare(
                "insert into account (account_id, name, email, cpf, car_plate, is_passenger, is_driver) values (?, ?, ?, ?, ?, ?, ?)"
            );
            $insertSuccess = $insertStatement->execute(
                [
                    $accountId,
                    $input->name,
                    $input->email,
                    $input->cpf,
                    $input->carPlate,
                    $input->isPassenger,
                    $input->isDriver
                ]
            );
            if ($insertSuccess) {
                return new SignupOutput(
                    id: $pdoConnection->lastInsertId(),
                    name: $input->name,
                    email: $input->email,
                    cpf: $input->cpf,
                    password: $input->password,
                    isPassenger: $input->isPassenger,
                    isDriver: $input->isDriver,
                    carPlate: $input->carPlate
                );
            } else {
                throw new \Exception("Sql statement error");
            }
        } finally {
            $pdoConnection = null;
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

    private function clean(string $cpf): bool
    {
        return str_replace("/\D/g", "", $cpf);
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


