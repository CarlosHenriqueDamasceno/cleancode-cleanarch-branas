<?php

declare(strict_types=1);

namespace App\Core\Ride\RequestRide;

readonly class RequestRideOutput
{
    public function __construct(public string $rideId)
    {
    }
}
