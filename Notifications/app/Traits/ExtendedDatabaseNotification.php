<?php

namespace App\Traits;

use Illuminate\Notifications\DatabaseNotification;

class ExtendedDatabaseNotification extends DatabaseNotification
{
    /**
     * @var used to store connection configuration
     */
    protected $connection;

    /**
     * @method is used to initialize the database connection from environment variables
     */
    public function __construct()
    {
        $this->connection = env('DATABASE_CONNECTION', 'mysql');
    }
}
