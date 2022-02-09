<?php

namespace HDVinnie\Purifier\Facades;

use Illuminate\Support\Facades\Facade;

class Purifier extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'purifier';
    }
}
