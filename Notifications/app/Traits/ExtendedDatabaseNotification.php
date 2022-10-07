<?php

namespace App\Traits;

use Illuminate\Notifications\DatabaseNotification;

class ExtendedDatabaseNotification extends DatabaseNotification
{
    protected $connection;

    public function __construct()
    {
        $this->connection = env('DATABASE_CONNECTION', 'mysql');
    }
}