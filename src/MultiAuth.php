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
     * @return mixed
     * @throws \Exception
     */
    protected function getCurrentAuthName()
    {
        if(Route::current()) {
            $action = Route::current()->getAction();
            if(isset($action['auth'])) {
                return $action['auth'];
            }
        }
        if($this->app['config']['auth.default'] === null) {
            throw new \Exception('No auth name given and no default setting for MultiAuth found!');
        }
        return $this->app['config']['auth.default'];
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $arguments = array())
    {
        $authName = $this->getCurrentAuthName();
        if (array_key_exists($authName, $this->providers)) {
            return call_user_func_array(array($this->providers[$authName], $name), $arguments);
        }
        throw new \Exception('Multi AuthManager "'.$authName.'" not found!');
    }
}