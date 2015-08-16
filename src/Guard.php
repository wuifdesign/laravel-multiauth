<?php namespace WuifDesign\MultiAuth;

use Illuminate\Auth\Guard as IlluminateGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Guard extends IlluminateGuard
{

    /**
     * Name of the Auth Type
     *
     * @var string
     */
    protected $name;

    /**
     * @param \Illuminate\Contracts\Auth\UserProvider $provider
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param string $name
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(UserProvider $provider, SessionInterface $session, $name, Request $request = null)
    {
        parent::__construct($provider, $session, $request);

        $this->name = $name;
    }

    /**
     * Get the name of the Auth Type
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->name;
    }

    /**
     * Get a unique identifier for the auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return 'login_' . $this->name . '_' . md5(get_class($this));
    }

    /**
     * Get the name of the cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName()
    {
        return 'remember_' . $this->name . '_' . md5(get_class($this));
    }

}
