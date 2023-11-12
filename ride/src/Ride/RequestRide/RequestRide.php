<?php

declare(strict_types=1);

namespace Ride\Ride\RequestRide;

use Ramsey\Uuid\Uuid;

class RequestRide
{
    public function execute(RequestRideInput $input): RequestRideOutput
    {
        $rideId = Uuid::uuid4()->toString();
        $pdoConnection = new \PDO('pgsql:host=database;', "postgres", "123456");
        $queryForUncompletedRides = $pdoConnection->prepare(
            "select count(ride_id) from cccat14.ride where status != '4' and passenger_id = ?"
        );
        $queryForUncompletedRides->execute([$input->passengerId]);
        $isUncompletedRides = $queryForUncompletedRides->fetchColumn();
        if ($isUncompletedRides) throw new \Exception("You can not request a ride with active rides");
        $saveRideStatement = $pdoConnection->prepare(
            "insert into cccat14.ride (ride_id, passenger_id, status, from_lat, from_long, to_lat, to_long, date) values (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $insertSuccess = $saveRideStatement->execute([
            $rideId,
            $input->passengerId,
            1,
            $input->startingPoint->latitude,
            $input->startingPoint->longitude,
            $input->destination->latitude,
            $input->destination->longitude,
            date("Y-m-d H:i:s")
        ]);
        if ($insertSuccess) {
            return new RequestRideOutput(rideId: $rideId);
        } else {
            throw new \Exception("Sql statement error");
        }
    }
}