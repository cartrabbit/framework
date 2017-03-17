<?php namespace Cartrabit\Framework\Providers;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cookie\CookieJar;
use Cartrabit\Framework\Session;

/**
 * @see http://getcartrabit.com
 */
class CartrabitServiceProvider extends ServiceProvider {

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

        $this->app->instance(
            'http',
            \Cartrabit\Framework\Http::capture()
        );

        $this->app->alias(
            'http',
            'Cartrabit\Framework\Http'
        );

        $this->app->instance(
            'router',
            $this->app->make('Cartrabit\Framework\Router', ['app' => $this->app])
        );

        $this->app->bind(
            'route',
            'Cartrabit\Framework\Route'
        );

        $this->app->instance(
            'enqueue',
            $this->app->make('Cartrabit\Framework\Enqueue', ['app' => $this->app])
        );

        $this->app->alias(
            'enqueue',
            'Cartrabit\Framework\Enqueue'
        );

        $this->app->instance(
            'panel',
            $this->app->make('Cartrabit\Framework\Panel', ['app' => $this->app])
        );

        $this->app->alias(
            'panel',
            'Cartrabit\Framework\Panel'
        );

        $this->app->instance(
            'shortcode',
            $this->app->make('Cartrabit\Framework\Shortcode', ['app' => $this->app])
        );

        $this->app->alias(
            'shortcode',
            'Cartrabit\Framework\Shortcode'
        );

        $this->app->instance(
            'widget',
            $this->app->make('Cartrabit\Framework\Widget', ['app' => $this->app])
        );

        $this->app->alias(
            'widget',
            'Cartrabit\Framework\Widget'
        );

        $this->app->instance(
            'session',
            $this->app->make('Cartrabit\Framework\Session', ['app' => $this->app])
        );

        $this->app->alias(
            'session',
            'Cartrabit\Framework\Session'
        );

        $this->app->instance(
            'notifier',
            $this->app->make('Cartrabit\Framework\Notifier', ['app' => $this->app])
        );

        $this->app->alias(
            'notifier',
            'Cartrabit\Framework\Notifier'
        );

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
            'charset' => DB_CHARSET,
            'collation' => DB_COLLATE ?: $wpdb->collate,
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
        $this->app['session']->start();
    }

}
