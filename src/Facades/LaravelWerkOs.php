<?php

namespace Sb\LaravelWerkos\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Sb\LaravelWerkOs\LaravelWorkOs
 */
class LaravelWerkOs extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Sb\LaravelWerkos\LaravelWerkos::class;
    }
}
