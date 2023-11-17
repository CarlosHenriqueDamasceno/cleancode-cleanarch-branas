<?php

declare(strict_types=1);

namespace App\Core\Ride;

interface RideDAO
{
    public function save(Ride $ride): Ride;
    public function getById(string $id): ?Ride;
    public function getActiveByPassengerId(string $passengerId): ?Ride;
}
