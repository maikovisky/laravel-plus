<?php

namespace Maikovisky\LaravelPlus;

use Illuminate\Support\ServiceProvider;

class LaravelPlusServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var type 
     */
    protected $defer = false;
    
    /**
     * The console commands.
     *
     * @var bool
     */
    protected $commands = [
        'Maikovisky\LaravelPlus\CrudNewCommand',
    ];
    
    
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravel-plus'];
    }
}
