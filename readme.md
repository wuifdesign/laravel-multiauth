## Laravel MultiAuth

[![GitHub release](https://img.shields.io/github/tag/wuifdesign/laravel-multiauth.svg)](https://github.com/wuifdesign/laravel-multiauth)
[![Packagist](https://img.shields.io/packagist/v/wuifdesign/laravel-multiauth.svg)](https://packagist.org/packages/wuifdesign/laravel-multiauth)
[![License](https://poser.pugx.org/wuifdesign/laravel-multiauth/license)](https://github.com/wuifdesign/laravel-multiauth/blob/master/LICENSE)

[![Twitter](https://img.shields.io/twitter/url/https/github.com/wuifdesign/laravel-multiauth.svg?style=social)](https://twitter.com/intent/tweet?text=Wow:&amp;amp;url=https%3A%2F%2Fgithub.com%2Fwuifdesign%2Flaravel-multiauth)

**Laravel**: 5.0, 5.1

This Package extends the default Auth library to allow logging in with accounts from different database tables or even different databases.
For example if you want to save your backend and a frontend users in a different table to keep them separated.

**Works with the default Laravel 5 AuthController and PasswordController!**

## Installation ##

The easiest way is to run the following command:

    composer require wuifdesign/laravel-multiauth

Otherwise you can include the package in your `composer.json` file,

    "require": {
        "wuifdesign/laravel-multiauth": "0.3.*"
    }

and update or install via composer:

    composer update

Now you have to open up your `app/config/app.php` and add

    'providers' => [
        ...
        WuifDesign\MultiAuth\ServiceProvider::class
    ]

Configuration is pretty easy too, take `app/config/auth.php` with its default values

    return array(

        'driver' => 'eloquent',
        'model' => 'User',
        'table' => 'users',

        'password' => array(
            'email' => 'emails.password',
            'table' => 'password_reminders',
            'expire' => 60,
        ),

    );

and replace the first three options (driver, model, table) and replace them with the following

    return array(

        'default' => 'user',
        'multi' => array(
            'admin' => array(
                'driver' => 'eloquent',
                'model'  => Admins::class,
            ),
            'user' => array(
                'driver' => 'eloquent',
                'model'  => Users::class,
                'password' => [
                    'email' => 'users.emails.password',
                ]
            )
        ),

        'password' => array(
            'email' => 'emails.password',
            'table' => 'password_reminders',
            'expire' => 60,
        ),
    );

## Usage ##

Everything is done by using routes. Just add a key "auth" to the route array with the value you used as a key in your `app/config/auth.php`

    Route::get('/', array(
        'uses' => function () {
            return 'Hello World';
        },
        'middleware' => [ 'auth' ],
        'auth' => 'admin',
    ));

Now if you call Auth::check() or any other function, it will use the driver and model set in the config for the key "admin".

**If you don't add a "auth" to the route, the "default" type defined in the `app/config/auth.php` will be used.**

If you want to check a specific auth while in a route using a different auth, you can use `Auth::type($auth_key)` to get the wanted auth manager.

    Auth::type('admin')->check();

To get the current auth_key used by the route, or the default value, if you haven't set it in the route use.

    Auth::currentType();

If you want to login as a different user, just use `Auth::impersonate($id, $auth_key = null, $remember = false)`. If you
don't parse a auth_key, the key set via route, or the default one will be used.

    Auth::impersonate(3, 'admin');
