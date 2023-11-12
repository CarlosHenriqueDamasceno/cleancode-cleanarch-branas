<?php

declare(strict_types=1);

namespace Ride\Ride;

readonly class Location
{

    public function __construct(public string $latitude, public string $longitude) {}
}