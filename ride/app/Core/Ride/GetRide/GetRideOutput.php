<?php

declare(strict_types=1);

namespace App\Core\Ride\GetRide;

use App\Core\Ride\Location;
use App\Core\Ride\RideStatus;

readonly class GetRideOutput
{
    public function __construct(
        public string $rideId,
        public string $passengerId,
        public RideStatus $status,
        public Location $startingPoint,
        public Location $destination,
        public string $date
    ) {
    }
}
