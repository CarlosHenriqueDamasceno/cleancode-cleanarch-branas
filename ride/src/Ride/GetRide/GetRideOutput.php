<?php

declare(strict_types=1);

namespace Ride\Ride\GetRide;

use Ride\Ride\Location;
use Ride\Ride\RideStatus;

readonly class GetRideOutput
{
    public function __construct(
        public string $rideId,
        public string $passengerId,
        public RideStatus $status,
        public Location $startingPoint,
        public Location $destination,
        public string $date
    ) {}
}