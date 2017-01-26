<?php

namespace Mehedi\Bkash\Facades;

use Illuminate\Support\Facades\Facade;

class Bkash extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'bkash';
    }
}
