<?php
namespace App\Traits;

use Illuminate\Notifications\DatabaseNotification;

class ExtendedDatabaseNotification extends DatabaseNotification
{
    protected $connection = 'mysql';
}