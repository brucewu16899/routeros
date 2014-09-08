<?php namespace Ceesco53\Routeros\Facades;

use Illuminate\Support\Facades\Facade;

class Routeros extends Facade {
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor() { return 'routeros'; }
}