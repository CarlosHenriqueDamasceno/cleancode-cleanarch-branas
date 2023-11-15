<?php

declare(strict_types=1);

namespace App\Core\Ride;

enum RideStatus: int
{
    case REQUESTED = 1;
    case ACCEPTED = 2;
    case IN_PROGRESS = 3;
    case DONE = 4;
}
