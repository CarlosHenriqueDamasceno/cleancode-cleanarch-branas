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
        $uncompletedRidesStatement = $pdoConnection->prepare(
            "select count(ride_id) from cccat14.ride where status != '4' and passenger_id = ?"
        );
        $uncompletedRidesStatement->execute([$input->passengerId]);
        $uncompletedRidesCount = $uncompletedRidesStatement->fetchColumn();
        if ($uncompletedRidesCount > 0) throw new \Exception("You can not request a ride with active rides");
        $saveRideStatement = $pdoConnection->prepare(
            "insert into cccat14.ride (ride_id, passenger_id, status, from_lat, from_long, to_lat, to_long, date)
                        values (:ride_id, :passenger_id, :status, :from_lat, :from_long, :to_lat, :to_long, :date)"
        );
        $insertSuccess = $saveRideStatement->execute([
            ":ride_id" => $rideId,
            ":passenger_id" => $input->passengerId,
            ":status" => 1,
            ":from_lat" => $input->startingPoint->latitude,
            ":from_long" => $input->startingPoint->longitude,
            ":to_lat" => $input->destination->latitude,
            ":to_long" => $input->destination->longitude,
            ":date" => date("Y-m-d H:i:s")
        ]);
        if ($insertSuccess) {
            return new RequestRideOutput(rideId: $rideId);
        } else {
            throw new \Exception("Sql statement error");
        }
    }
}