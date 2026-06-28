<?php

namespace App\Services;

class LogService
{

    public static function log(string $message)
    {
        // just to demonstrate service class, not registered, can be replaced with advanced logging logic

        $user = auth('sanctum')->user()->name ?? 'Guest';

        info($message . " By: " . $user);
    }
}
