<?php namespace WuifDesign\MultiAuth;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/multiauth.php';
        $this->mergeConfigFrom($configPath, 'multiauth');

        $this->app['multiauth'] = $this->app->share(function($app) {
            return new Wuifdesign\Multiauth\Multiauth;
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/../config/debugbar.php' => config_path('multiauth.php')]);

        $this->app->middleware(['Wuifdesign\Multiauth\Middleware\Multiauth']);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('multiauth');
    }
}
