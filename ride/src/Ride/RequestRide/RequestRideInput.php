<?php

declare(strict_types=1);

namespace Ride\Ride\RequestRide;

use Ride\Ride\Location;

readonly class RequestRideInput
{
    public function __construct(
        public string $passengerId,
        public Location $startingPoint,
        public Location $destination
    ) {}
}