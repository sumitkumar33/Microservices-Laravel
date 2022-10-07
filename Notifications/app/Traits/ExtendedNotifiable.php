<?php

namespace App\Traits;

use Illuminate\Notifications\RoutesNotifications;

trait ExtendedNotifiable
{
    use HasDatabaseExtendedNotifications, RoutesNotifications;
}
