<?php

declare(strict_types=1);

namespace Ride\Ride\RequestRide;

readonly class RequestRideOutput
{
    public function __construct(public string $rideId) {}
}