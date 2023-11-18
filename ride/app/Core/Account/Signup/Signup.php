<?php

declare(strict_types=1);

namespace App\Core\Account\Signup;

use App\Core\Account\Account;
use App\Core\Account\AccountDAO;
use App\Core\Account\CpfValidator;
use Ramsey\Uuid\Uuid;

readonly class Signup
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
        if (!CpfValidator::validate($input->cpf)) throw new \Exception("Invalid cpf");
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
}
