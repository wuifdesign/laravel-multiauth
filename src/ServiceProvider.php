<?php
namespace WuifDesign\MultiAuth;

use \Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('auth', function ($app) {
            $app['auth.loaded'] = true;
            return new MultiAuth($app);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('auth');
    }
}
