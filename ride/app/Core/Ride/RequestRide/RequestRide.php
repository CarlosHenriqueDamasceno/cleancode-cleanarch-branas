<?php

declare(strict_types=1);

namespace App\Core\Ride\RequestRide;

use App\Core\Ride\Location;
use App\Core\Ride\Ride;
use App\Core\Ride\RideDAO;
use App\Core\Ride\RideStatus;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class RequestRide
{
    public function __construct(private RideDAO $accountDAO)
    {
    }

    public function execute(RequestRideInput $input): RequestRideOutput
    {
        $rideId = Uuid::uuid4()->toString();
        $activeRide = $this->accountDAO->getActiveByPassengerId($input->passengerId);
        if (!is_null($activeRide)) throw new \Exception("You can not request a ride with active rides");
        $ride = new Ride(
            rideId: $rideId,
            passengerId: $input->passengerId,
            status: RideStatus::REQUESTED,
            from: new Location(
                $input->startingPoint->latitude,
                $input->startingPoint->longitude
            ),
            destination: new Location(
                $input->destination->latitude,
                $input->destination->longitude
            ),
            date: new DateTimeImmutable()
        );
        $ride = $this->accountDAO->save($ride);
        return new RequestRideOutput(rideId: $rideId);
    }
}
