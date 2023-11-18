<?php

declare(strict_types=1);

namespace App\Core\Ride\GetRide;

use App\Core\Ride\RideDAO;

readonly class GetRide
{

    public function __construct(private RideDAO $rideDAO)
    {
    }

    public function execute(string $rideId): GetRideOutput
    {
        $ride = $this->rideDAO->getById($rideId);
        if (is_null($ride)) return null;
        return new GetRideOutput(
            rideId: $ride->rideId,
            passengerId: $ride->passengerId,
            status: $ride->status,
            from: $ride->from,
            destination: $ride->destination,
            date: $ride->date
        );
    }
}
