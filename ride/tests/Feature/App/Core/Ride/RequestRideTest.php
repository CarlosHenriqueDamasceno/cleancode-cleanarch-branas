<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Account\AccountDAO;
use App\Core\Account\AccountDAODatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Core\Account\Signup\Signup;
use App\Core\Account\Signup\SignupInput;
use App\Core\Ride\GetRide\GetRide;
use App\Core\Ride\Location;
use App\Core\Ride\RequestRide\RequestRide;
use App\Core\Ride\RequestRide\RequestRideInput;
use App\Core\Ride\RideDAO;
use App\Core\Ride\RideDAODatabase;
use App\Core\Ride\RideStatus;

final class RequestRideTest extends TestCase
{

    private AccountDAO $accountDAO;
    private RideDAO $rideDAO;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountDAO = new AccountDAODatabase();
        $this->rideDAO = new RideDAODatabase();
    }

    #[Test]
    public function shouldRequestARide(): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "Jhon Doe",
            email: "jhon.doe$randomNumber@gmail.com",
            cpf: "97456321558",
            password: "123456",
            isPassenger: true,
        );
        $signup = new Signup($this->accountDAO);
        $outputSignup = $signup->execute($input);

        $input = new RequestRideInput(
            passengerId: $outputSignup->accountId,
            startingPoint: new Location(
                latitude: "13123",
                longitude: "353543"
            ),
            destination: new Location(
                latitude: "546456",
                longitude: "4564564"
            )
        );

        $requestRide = new RequestRide($this->rideDAO);
        $getRide = new GetRide($this->rideDAO);
        $outputRequestRide = $requestRide->execute($input);
        $outputGetRide = $getRide->execute($outputRequestRide->rideId);
        $this->assertInstanceOf(RideStatus::class, $outputGetRide->status);
        $this->assertEquals(RideStatus::REQUESTED->value, $outputGetRide->status->value);
    }

    #[Test]
    public function shouldNotRequestARideWithIsAlreadyActiveRides(): void
    {
        $randomNumber = rand();
        $input = new SignupInput(
            name: "Jhon Doe",
            email: "jhon.doe$randomNumber@gmail.com",
            cpf: "97456321558",
            password: "123456",
            isPassenger: true,
        );
        $signup = new Signup($this->accountDAO);
        $outputSignup = $signup->execute($input);

        $input = new RequestRideInput(
            passengerId: $outputSignup->accountId,
            startingPoint: new Location(
                latitude: "13123",
                longitude: "353543"
            ),
            destination: new Location(
                latitude: "546456",
                longitude: "4564564"
            )
        );

        $requestRide = new RequestRide($this->rideDAO);
        $requestRide->execute($input);
        $this->expectExceptionMessage("You can not request a ride with active rides");
        $requestRide->execute($input);
    }
}
