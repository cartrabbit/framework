<?php namespace Cartrabbit\Framework\Providers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\ServiceProvider;

class CartrabbitServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerEloquent();

        $this->app->instance(
            'env',
            defined('CARTRABBIT_ENV') ? CARTRABBIT_ENV
                : (defined('WP_DEBUG') ? 'local'
                    : 'production')
        );

//        $this->app->instance(
//            'http',
//            \Illuminate\Http\Request::capture()
//        );
//
//        $this->app->alias(
//            'http',
//            'Illuminate\Http\Request'
//        );

        $this->app->instance(
            'router',
            $this->app->make('Cartrabbit\Framework\Router', ['app' => $this->app])
        );

        $this->app->bind(
            'route',
            'Cartrabbit\Framework\Route'
        );
//
//        $this->app->instance(
//            'enqueue',
//            $this->app->make('Cartrabbit\Framework\Enqueue', ['app' => $this->app])
//        );
//
//        $this->app->alias(
//            'enqueue',
//            'Cartrabbit\Framework\Enqueue'
//        );
//
        $this->app->instance(
            'panel',
            $this->app->make('Cartrabbit\Framework\Panel', ['app' => $this->app])
        );
//
        $this->app->alias(
            'panel',
            'Cartrabbit\Framework\Panel'
        );
//
//        $this->app->instance(
//            'shortcode',
//            $this->app->make('Cartrabbit\Framework\Shortcode', ['app' => $this->app])
//        );
//
//        $this->app->alias(
//            'shortcode',
//            'Cartrabbit\Framework\Shortcode'
//        );
//
//        $this->app->instance(
//            'widget',
//            $this->app->make('Cartrabbit\Framework\Widget', ['app' => $this->app])
//        );
//
//        $this->app->alias(
//            'widget',
//            'Cartrabbit\Framework\Widget'
//        );

        $this->app->instance(
            'session',
            $this->app->make('Symfony\Component\HttpFoundation\Session\Session', ['app' => $this->app])
        );

        $this->app->alias(
            'session',
            'Symfony\Component\HttpFoundation\Session\Session'
        );

//        $this->app->instance(
//            'notifier',
//            $this->app->make('Cartrabbit\Framework\Notifier', ['app' => $this->app])
//        );
//
//        $this->app->alias(
//            'notifier',
//            'Cartrabbit\Framework\Notifier'
//        );

        $this->app->singleton(
            'errors',
            function ()
            {
                return session_flashed('__validation_errors', []);
            }
        );

        $_GLOBALS['errors'] = $this->app['errors'];
    }

    /**
     * Registers Eloquent.
     *
     * @return void
     */
    protected function registerEloquent()
    {
        global $wpdb;

        $capsule = new Capsule($this->app);

        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
//            'charset' => DB_CHARSET,
//            'collation' => DB_COLLATE ?: $wpdb->collate,
            'charset' => $wpdb->charset,
            'collation' => $wpdb->collate,
            'prefix' => $wpdb->prefix
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    /**
     * Boots the service provider.
     *
     * @return void
     */
    public function boot()
    {
//        $this->app['session']->start();
    }

}
