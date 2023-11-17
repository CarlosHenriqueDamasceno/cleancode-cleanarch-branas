<?php

declare(strict_types=1);

namespace App\Core\Ride;

use App\Core\Ride\Location;
use App\Core\Ride\RideDAO;
use App\Core\Ride\RideStatus;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use stdClass;

class RideDAODatabase implements RideDAO
{
    public function save(Ride $ride): Ride
    {
        DB::insert(
            "insert into cccat14.ride (
                            ride_id,
                            passenger_id,
                            status,
                            from_lat,
                            from_long,
                            to_lat,
                            to_long,
                            date
                        )
                values (?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $ride->rideId,
                $ride->passengerId,
                $ride->status->value,
                $ride->from->latitude,
                $ride->from->longitude,
                $ride->destination->latitude,
                $ride->destination->longitude,
                $ride->date
            ]
        );
        return $ride;
    }

    public function getById(string $id): ?Ride
    {
        $rides =  DB::select(
            "select ride_id,
                    passenger_id,
                    status,
                    from_lat,
                    from_long,
                    to_lat,
                    to_long,
                    date
                from cccat14.ride where ride_id = ?",
            [$id]
        );
        if (empty($rides)) return null;
        return $this->mapRecordToRide($rides[0]);
    }

    public function getActiveByPassengerId(string $passengerId): ?Ride
    {
        $rides = DB::select("select * from cccat14.ride where status != '4' and passenger_id = ?", [$passengerId]);
        if (empty($rides)) return null;
        return $this->mapRecordToRide($rides[0]);
    }

    private function mapRecordToRide(stdClass $record): Ride
    {
        return new Ride(
            rideId: $record->ride_id,
            passengerId: $record->passenger_id,
            status: RideStatus::from((int)$record->status),
            from: new Location(
                latitude: $record->from_lat,
                longitude: $record->from_long
            ),
            destination: new Location(
                latitude: $record->to_lat,
                longitude: $record->to_long
            ),
            date: new DateTimeImmutable($record->date)
        );
    }
}
