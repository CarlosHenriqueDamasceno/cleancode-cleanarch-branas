<?php

declare(strict_types=1);

namespace App\Core\Ride\RequestRide;

use App\Core\Ride\Location;

readonly class RequestRideInput
{
    public function __construct(
        public string $passengerId,
        public Location $startingPoint,
        public Location $destination
    ) {
    }
}
