<?php
namespace WuifDesign\MultiAuth;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

class MultiAuth
{

    /**
     * Registered multiauth providers.
     *
     * @var array
     */
    protected $providers = array();

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        foreach ($app['config']['auth.multi'] as $key => $config) {
            $this->providers[$key] = new AuthManager($app, $key, $config);
        }
    }

    /**
     * @param string $name
     * @param array $arguments
     * @throws \Exception
     */
    public function __call($name, $arguments = array())
    {
        $authName = Route::current()->getAction()['auth'];

        if (array_key_exists($authName, $this->providers)) {
            return call_user_func_array(array($this->providers[$authName], $name), $arguments);
        }

        throw new \Exception('MulitAuth Provider not found');
    }
}