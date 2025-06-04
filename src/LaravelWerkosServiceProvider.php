<?php

namespace Sb\LaravelWerkos;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelWerkosServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-werkos')
            ->hasConfigFile()
            ->hasMigration('create_laravel_werkos_table');
    }
}
