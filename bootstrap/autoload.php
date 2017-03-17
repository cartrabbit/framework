<?php

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
 * Get Cartrabit.
 */
$cartrabit = Cartrabit\Framework\Application::getInstance();

/**
 * Load all cartrabit.php files in plugin roots.
 */
$iterator = new DirectoryIterator(plugin_directory());


foreach ($iterator as $directory)
{
    if ( ! $directory->valid() || $directory->isDot() || ! $directory->isDir())
    {
        continue;
    }

    $root = $directory->getPath() . '/' . $directory->getFilename();

    if ( ! file_exists($root . '/cartrabit.config.php'))
    {
        continue;
    }

    $config = $cartrabit->getPluginConfig($root);

    $plugin = substr($root . '/plugin.php', strlen(plugin_directory()));
    $plugin = ltrim($plugin, '/');

    register_activation_hook($plugin, function () use ($cartrabit, $config, $root)
    {
        if ( ! $cartrabit->pluginMatches($config))
        {
            $cartrabit->pluginMismatched($root);
        }

        $cartrabit->pluginMatched($root);
        $cartrabit->loadPlugin($config);
        $cartrabit->activatePlugin($root);
    });

    register_deactivation_hook($plugin, function () use ($cartrabit, $root)
    {
        $cartrabit->deactivatePlugin($root);
    });

    // Ugly hack to make the install hook work correctly
    // as WP doesn't allow closures to be passed here
    register_uninstall_hook($plugin, create_function('', 'cartrabit()->deletePlugin(\'' . $root . '\');'));

    if ( ! is_plugin_active($plugin))
    {
        continue;
    }

    if ( ! $cartrabit->pluginMatches($config))
    {
        $cartrabit->pluginMismatched($root);

        continue;
    }

    $cartrabit->pluginMatched($root);

    @require_once $root.'/plugin.php';

    $cartrabit->loadPlugin($config);
}

/**
 * Boot Cartrabit.
 */
$cartrabit->boot();
