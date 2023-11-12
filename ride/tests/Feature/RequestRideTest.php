<?php

declare(strict_types=1);

namespace Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ride\Account\Signup\Signup;
use Ride\Account\Signup\SignupInput;
use Ride\Ride\GetRide\GetRide;
use Ride\Ride\Location;
use Ride\Ride\RequestRide\RequestRide;
use Ride\Ride\RequestRide\RequestRideInput;
use Ride\Ride\RideStatus;

final class RequestRideTest extends TestCase
{
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
        $signup = new Signup();
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

        $requestRide = new RequestRide();
        $getRide = new GetRide();
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
        $signup = new Signup();
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

        $requestRide = new RequestRide();
        $requestRide->execute($input);
        $this->expectExceptionMessage("You can not request a ride with active rides");
        $requestRide->execute($input);
    }
}