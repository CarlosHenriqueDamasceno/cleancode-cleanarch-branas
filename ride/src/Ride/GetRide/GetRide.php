<?php

declare(strict_types=1);

namespace Ride\Ride\GetRide;

use Ride\Ride\Location;
use Ride\Ride\RideStatus;

class GetRide
{
    public function execute(string $rideId): GetRideOutput
    {
        $pdoConnection = new \PDO('pgsql:host=database;', "postgres", "123456");
        $getRideStatement = $pdoConnection->prepare(
            "select ride_id, passenger_id, status, from_lat, from_long, to_lat, to_long, date from cccat14.ride where ride_id = ?"
        );
        $getRideStatement->execute([$rideId]);
        $rideRow = $getRideStatement->fetch();
        return new GetRideOutput(
            rideId: $rideRow['ride_id'],
            passengerId: $rideRow['passenger_id'],
            status: RideStatus::from((int)$rideRow['status']),
            startingPoint: new Location(
                latitude: $rideRow['from_lat'],
                longitude: $rideRow['from_long']
            ),
            destination: new Location(
                latitude: $rideRow['to_lat'],
                longitude: $rideRow['to_long']
            ),
            date: $rideRow['date']
        );
    }
}