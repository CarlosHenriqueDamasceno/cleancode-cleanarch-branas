<?php

declare(strict_types=1);

namespace App\Core\Account\Signup;

use App\Core\Account\Account;
use App\Core\Account\AccountDAO;
use Ramsey\Uuid\Uuid;

class Signup
{
    public function __construct(private AccountDAO $accountDAO)
    {
    }

    public function execute(SignupInput $input): SignupOutput
    {
        $accountId = Uuid::uuid4()->toString();
        $accountWithSameEmail = $this->accountDAO->getByEmail($input->email);
        if (!is_null($accountWithSameEmail)) throw new \Exception("Duplicated account");
        if ($this->isInvalidName($input->name)) throw new \Exception("Invalid name");
        if ($this->isInvalidEmail($input->email)) throw new \Exception("Invalid email");
        if (!$this->validateCpf($input->cpf)) throw new \Exception("Invalid cpf");
        if ($input->isDriver && $this->isInvalidCarPlate($input->carPlate)) {
            throw new \Exception("Invalid car plate");
        }
        $account = new Account(
            $accountId,
            $input->name,
            $input->email,
            $input->cpf,
            $input->password,
            $input->isPassenger,
            $input->isDriver,
            $input->carPlate
        );
        $account = $this->accountDAO->save($account);
        return new SignupOutput(
            accountId: $accountId,
        );
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
        $cpf = $this->cleanCpfInput($cpf);
        if ($this->isInvalidLength($cpf)) return false;
        if ($this->allDigitsAreTheSame($cpf)) return false;
        $dg1 = $this->calculateDigit($cpf, 10);
        $dg2 = $this->calculateDigit($cpf, 11);
        return $this->extractCheckDigit($cpf) === $dg1 . $dg2;
    }

    private function cleanCpfInput(string $cpf): string
    {
        return preg_replace('/[^0-9]/', "", $cpf);
    }

    private function isInvalidLength(string $cpf): bool
    {
        return strlen($cpf) !== 11;
    }

    private function allDigitsAreTheSame(string $cpf): bool
    {
        return count(array_unique(str_split($cpf))) === 1;
    }

    private function calculateDigit(string $cpf, int $factor): int
    {
        $total = 0;
        foreach (str_split($cpf) as $digit) {
            if ($factor > 1) $total += $digit * $factor--;
        }
        $rest = $total % 11;
        return ($rest < 2) ? 0 : 11 - $rest;
    }

    private function extractCheckDigit(string $cpf): string
    {
        return substr($cpf, -2);
    }
}
