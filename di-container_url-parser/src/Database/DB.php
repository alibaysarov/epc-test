<?php

namespace App\Database;

class DB
{
    public function __construct(string $host, string $user, string $pass, string $db)
    {
    }
    public function getAll():array
    {
        return [1,2,3];
    }
}