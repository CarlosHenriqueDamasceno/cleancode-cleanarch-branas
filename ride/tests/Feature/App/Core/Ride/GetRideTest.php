<?php

declare(strict_types=1);

namespace Tests\Feature\App\Core\Ride;

use App\Core\Ride\GetRide\GetRide;
use App\Core\Ride\Location;
use App\Core\Ride\RideDAO;
use App\Core\Ride\RideDAODatabase;
use App\Core\Ride\RideStatus;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GetRideTest extends TestCase
{
    private RideDAO $accountDAO;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountDAO = new RideDAODatabase();
    }

    private function insertNewAccount(): string
    {
        $randomNumber = rand();
        $expectedAccountId = Uuid::uuid4()->toString();
        $expectedName = "Jhon Doe";
        $expectedEmail = "jhonDoe$randomNumber@email.com";
        $expectedPassword = "123456";
        $expectedCpf = "97456321558";
        $expectedCarPlate = "DDD-3167";
        $expectedIsPassenger = true;
        $expectedIsDriver = false;
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
                $expectedAccountId,
                $expectedName,
                $expectedEmail,
                $expectedPassword,
                $expectedCpf,
                $expectedCarPlate,
                $expectedIsPassenger,
                $expectedIsDriver,
            ]
        );
        return $expectedAccountId;
    }

    private function insertNewRide(): array
    {
        $expectedRideId = Uuid::uuid4()->toString();
        $expectedPassengerId = $this->insertNewAccount();
        $expectedFrom =  new Location(
            latitude: "13123",
            longitude: "353543"
        );
        $expectedDestination =  new Location(
            latitude: "546456",
            longitude: "4564564"
        );
        $expectedDate = "2023-11-17 20:43:00";
        DB::insert(
            "insert into cccat14.ride (
                            ride_id,
                            passenger_id,
                            status,
                            from_lat,
                            from_long,
                            to_lat,
                            to_long,
                            date
                        )
                values (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $expectedRideId,
                $expectedPassengerId,
                RideStatus::REQUESTED->value,
                $expectedFrom->latitude,
                $expectedFrom->longitude,
                $expectedDestination->latitude,
                $expectedDestination->longitude,
                $expectedDate
            ]
        );
        return [
            "ride_id" => $expectedRideId,
            "passenger_id" => $expectedPassengerId,
            "status" => RideStatus::REQUESTED->value,
            "from_lat" => $expectedFrom->latitude,
            "from_long" => $expectedFrom->longitude,
            "destination_lat" => $expectedDestination->latitude,
            "destination_long" => $expectedDestination->longitude,
            "date" => $expectedDate
        ];
    }

    #[Test]
    public function shouldGetARide(): void
    {
        $rideData = $this->insertNewRide();
        $getRide = new GetRide($this->accountDAO);
        $output = $getRide->execute($rideData['ride_id']);
        $this->assertEquals($rideData['passenger_id'], $output->passengerId);
        $this->assertEquals($rideData['status'], $output->status->value);
        $this->assertEquals($rideData['from_lat'], $output->from->latitude);
        $this->assertEquals($rideData['from_long'], $output->from->longitude);
        $this->assertEquals($rideData['destination_lat'], $output->destination->latitude);
        $this->assertEquals($rideData['destination_long'], $output->destination->longitude);
        $this->assertEquals(new DateTimeImmutable($rideData['date']), $output->date);
    }
}
