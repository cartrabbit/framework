<?php namespace Cartrabit\Framework;

use Cartrabit\Framework\Application;

/**
 * @see http://getcartrabit.com
 */
class Widget {

    /**
     * @var \Cartrabit\Framework\Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $widgets = [];

    /**
     * @param \Cartrabit\Framework\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app;

        add_action('widgets_init', [$this, 'boot']);
    }

    /**
     * Boot the widgets.
     *
     * @return void
     */
    public function boot()
    {
        global $wp_widget_factory;

        foreach ($this->widgets as $widget)
        {
            register_widget($widget['class']);

            if (method_exists($instance = $wp_widget_factory->widgets[$widget['class']], 'boot'))
            {
                $this->app->call(
                    [$instance, 'boot'],
                    ['app' => $this->app, 'plugin' => $widget['plugin']]
                );
            }
        }
    }

    /**
     * Adds a wdiget.
     *
     * @param  string $widget
     * @param  Plugin $plugin
     * @return void
     */
    public function add($widget, Plugin $plugin = null)
    {
        $this->widgets[] = [
            'class'  => $widget,
            'plugin' => $plugin
        ];
    }

}
