<?php

declare(strict_types=1);

namespace Tests\Feature\App\Core\Ride;

use App\Core\Account\AccountDAO;
use App\Core\Account\AccountDAODatabase;
use App\Core\Ride\Location;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

final class GetRideTest extends TestCase
{
    private string $expectedAccountId;
    private string $expectedName;
    private string $expectedEmail;
    private string $expectedCpf;
    private string $expectedPassword;
    private string $expectedCarPlate;
    private bool $expectedIsPassenger;
    private bool $expectedIsDriver;
    private Location $expectedFrom;
    private Location $expectedDestination;
    private DateTimeImmutable $expectedDate;
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
    }
}
