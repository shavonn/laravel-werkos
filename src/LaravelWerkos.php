<?php

namespace Sb\LaravelWerkos;

class LaravelWerkos
{
    public static function userManager(): UserManagement
    {
        return app(UserManagement::class);
    }
}
