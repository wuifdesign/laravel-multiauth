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
    protected $managers = array();

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        foreach ($this->app['config']['auth.multi'] as $key => $config) {
            $this->managers[$key] = new AuthManager($this->app, $key, $config);
        }
    }

    /**
     * Returns a specific auth provider
     *
     * @param string $auth_key
     * @throws \Exception
     */
    public function type($auth_key)
    {
        if (array_key_exists($auth_key, $this->managers)) {
            return $this->managers[$auth_key];
        }

        throw new \Exception('Multi AuthManager "'.$auth_key.'" not found!');
    }

    /**
     * Login as a specific user
     *
     * @param int $id User ID
     * @param string $auth_key
     * @param boolean $remember
     */
    public function impersonate($id, $auth_key = null, $remember = false)
    {
        if($auth_key == null) {
            $auth_key = $this->currentType();
        }
        return $this->type($auth_key)->loginUsingId($id, $remember);
    }

    /**
     * Returns the current auth type set via route or the default value
     *
     * @return string
     * @throws \Exception
     */
    public function currentType()
    {
        if(Route::current()) {
            $action = Route::current()->getAction();
            if(isset($action['auth'])) {
                $actionAuth = $action['auth'];
                if(is_array($actionAuth)) {
                    return end($actionAuth);
                }
                return $actionAuth;
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
        $authName = $this->currentType();
        return call_user_func_array(array($this->type($authName), $name), $arguments);
    }
}