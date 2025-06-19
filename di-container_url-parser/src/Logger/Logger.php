<?php

namespace App\Logger;

class Logger
{
    public function log(string $message):void
    {
        var_dump($message);
    }
}