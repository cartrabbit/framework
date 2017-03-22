<?php
/**
 * Cartrabbit - A PHP Framework For Wordpress
 *
 * @package  Cartrabbit
 * @author   Ashlin <ashlin@flycart.org>
 * Based on Herbert Framework
 */

/**
 * Ensure this is only ran once.
 */
if (defined('CARTRABBIT_AUTOLOAD'))
{
    return;
}

define('CARTRABBIT_AUTOLOAD', microtime(true));

@require 'helpers.php';

/**
 * Load the WP plugin system.
 */
if (array_search(ABSPATH . 'wp-admin/includes/plugin.php', get_included_files()) === false)
{
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Get Cartrabbit.
 */
$cartrabbit = Cartrabbit\Framework\Application::getInstance();

/**
 * Load all cartrabbit.php files in plugin roots.
 */
$iterator = new DirectoryIterator(plugin_directory());


foreach ($iterator as $directory)
{
    if ( ! $directory->valid() || $directory->isDot() || ! $directory->isDir())
    {
        continue;
    }

    $root = $directory->getPath() . '/' . $directory->getFilename();

    if ( ! file_exists($root . '/cartrabbit.config.php'))
    {
        continue;
    }

    $config = $cartrabbit->getPluginConfig($root);

    $plugin = substr($root . '/plugin.php', strlen(plugin_directory()));
    $plugin = ltrim($plugin, '/');

    register_activation_hook($plugin, function () use ($cartrabbit, $config, $root)
    {
        if ( ! $cartrabbit->pluginMatches($config))
        {
            $cartrabbit->pluginMismatched($root);
        }

        $cartrabbit->pluginMatched($root);
        $cartrabbit->loadPlugin($config);
        $cartrabbit->activatePlugin($root);
    });

    register_deactivation_hook($plugin, function () use ($cartrabbit, $root)
    {
        $cartrabbit->deactivatePlugin($root);
    });

    // Ugly hack to make the install hook work correctly
    // as WP doesn't allow closures to be passed here
    register_uninstall_hook($plugin, create_function('', 'cartrabbit()->deletePlugin(\'' . $root . '\');'));

    // To register the plugin
    $activePlugin = new \Cartrabbit\Framework\Base\Plugin(plugin_dir_path( $directory->getPath().'/'.$plugin ));
    $cartrabbit->registerPlugin($activePlugin);

    if ( ! is_plugin_active($plugin))
    {
        continue;
    }

    if ( ! $cartrabbit->pluginMatches($config))
    {
        $cartrabbit->pluginMismatched($root);

        continue;
    }

    $cartrabbit->pluginMatched($root);

    @require_once $root.'/plugin.php';

    $cartrabbit->loadPlugin($config);
}

/**
 * Boot Cartrabbit.
 */
$cartrabbit->boot();
