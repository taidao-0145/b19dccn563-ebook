<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $repositories = [
            'UserRepositoryInterface' => 'UserRepository',
        ];
        foreach ($repositories as $key => $val) {
            $this->app->singleton("App\\Repositories\\Interfaces\\$key", "App\\Repositories\\$val");
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
