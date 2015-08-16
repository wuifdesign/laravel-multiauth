<?php
namespace WuifDesign\MultiAuth;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

class MultiAuth
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Registered AuthManagers.
     *
     * @var array
     */
    protected $providers = array();

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        foreach ($this->app['config']['auth.multi'] as $key => $config) {
            $this->providers[$key] = new AuthManager($this->app, $key, $config);
        }
    }

    /**
     * Returns a specific auth provider
     *
     * @param string $authName
     * @throws \Exception
     */
    public function type($authName)
    {
        if (array_key_exists($authName, $this->providers)) {
            return $this->providers[$authName];
        }

        throw new \Exception('Multi AuthManager "'.$authName.'" not found!');
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $arguments = array())
    {
        $action = Route::current()->getAction();

        if(isset($action['auth'])) {
            $authName = $action['auth'];
        } else {
            if($this->app['config']['auth.default'] === null) {
                throw new \Exception('No auth name given and no default setting for MultiAuth found!');
            }
            $authName = $this->app['config']['auth.default'];
        }

        if (array_key_exists($authName, $this->providers)) {
            return call_user_func_array(array($this->providers[$authName], $name), $arguments);
        }

        throw new \Exception('Multi AuthManager "'.$authName.'" not found!');
    }
}