<?php namespace WuifDesign\MultiAuth\Facades;

class MultiAuth extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'multiauth';
    }
}
