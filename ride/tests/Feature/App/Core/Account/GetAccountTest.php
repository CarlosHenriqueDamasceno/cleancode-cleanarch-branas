<?php

declare(strict_types=1);

namespace Feature;

use App\Core\Account\AccountDAO;
use App\Core\Account\AccountDAODatabase;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use App\Core\Account\GetAccount\GetAccount;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

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
    private AccountDAO $accountDAO;

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
                    ) values (:account_id, :name, :email, :password, :cpf, :car_plate, :is_passenger, :is_driver)",
            [
                $this->expectedAccountId,
                $this->expectedName,
                $this->expectedEmail,
                $this->expectedPassword,
                $this->expectedCpf,
                $this->expectedCarPlate,
                $this->expectedIsPassenger,
                $this->expectedIsDriver,
            ]
        );

        $this->accountDAO = new AccountDAODatabase();
    }

    #[Test]
    public function shouldGetAnAccount(): void
    {
        $getAccount = new GetAccount($this->accountDAO);
        $outputGetAccount = $getAccount->execute($this->expectedAccountId);
        $this->assertEquals($this->expectedName, $outputGetAccount->name);
        $this->assertEquals($this->expectedEmail, $outputGetAccount->email);
        $this->assertEquals($this->expectedCpf, $outputGetAccount->cpf);
        $this->assertEquals($this->expectedCarPlate, $outputGetAccount->carPlate);
        $this->assertEquals($this->expectedIsPassenger, $outputGetAccount->isPassenger);
        $this->assertEquals($this->expectedIsDriver, $outputGetAccount->isDriver);
    }
}
