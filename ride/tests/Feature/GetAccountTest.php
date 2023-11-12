<?php

declare(strict_types=1);

namespace Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ride\Account\GetAccount\GetAccount;

final class GetAccountTest extends TestCase
{

    private string $expectedAccountId;
    private string $expectedName;
    private string $expectedEmail;
    private string $expectedCpf;
    private string $expectedPassword;
    private string $expectedCarPlate;
    private bool $expectedIsPassenger;
    private bool $expectedIsDriver;

    protected function setUp(): void
    {
        parent::setUp();

        $randomNumber = rand();

        $this->expectedAccountId = Uuid::uuid4()->toString();
        $this->expectedName = "Jhon Doe";
        $this->expectedEmail = "jhonDoe$randomNumber@email.com";
        $this->expectedPassword = "123456";
        $this->expectedCpf = "97456321558";
        $this->expectedCarPlate = "DDD-3167";
        $this->expectedIsPassenger = true;
        $this->expectedIsDriver = false;

        $pdoConnection = new \PDO('pgsql:host=database;', "postgres", "123456");

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
        $insertStatement->bindValue(':account_id', $this->expectedAccountId);
        $insertStatement->bindValue(':name', $this->expectedName);
        $insertStatement->bindValue(':email', $this->expectedEmail);
        $insertStatement->bindValue(':password', $this->expectedPassword);
        $insertStatement->bindValue(':cpf', $this->expectedCpf);
        $insertStatement->bindValue(':car_plate', $this->expectedCarPlate);
        $insertStatement->bindValue(':is_passenger', $this->expectedIsPassenger, \PDO::PARAM_BOOL);
        $insertStatement->bindValue(':is_driver', $this->expectedIsDriver, \PDO::PARAM_BOOL);
        $insertStatement->execute();

        $pdoConnection = null;
    }

    #[Test]
    public function shouldGetAnAccount(): void
    {
        $getAccount = new GetAccount();
        $outputGetAccount = $getAccount->execute($this->expectedAccountId);
        $this->assertEquals($this->expectedName, $outputGetAccount->name);
        $this->assertEquals($this->expectedEmail, $outputGetAccount->email);
        $this->assertEquals($this->expectedPassword, $outputGetAccount->password);
        $this->assertEquals($this->expectedCpf, $outputGetAccount->cpf);
        $this->assertEquals($this->expectedCarPlate, $outputGetAccount->carPlate);
        $this->assertEquals($this->expectedIsPassenger, $outputGetAccount->isPassenger);
        $this->assertEquals($this->expectedIsDriver, $outputGetAccount->isDriver);
    }
}