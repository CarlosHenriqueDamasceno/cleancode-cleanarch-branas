<?php

declare(strict_types=1);

namespace App\Core\Ride\GetRide;

use App\Core\Ride\Location;
use App\Core\Ride\RideStatus;
use DateTimeImmutable;

readonly class GetRideOutput
{
    public function __construct(
        public string $rideId,
        public string $passengerId,
        public RideStatus $status,
        public Location $from,
        public Location $destination,
        public DateTimeImmutable $date
    ) {
    }
}
