<?php
// Bootstrap the framework DO NOT edit this
require COREPATH.'bootstrap.php';

\Autoloader::add_classes(array(
	// Add classes you want to override here
	// Example: 'View' => APPPATH.'classes/view.php',
));

// Register the autoloader
\Autoloader::register();

/**
 * Your environment.  Can be set to any of the following:
 *
 * Fuel::DEVELOPMENT
 * Fuel::TEST
 * Fuel::STAGING
 * Fuel::PRODUCTION
 */
if (isset($_SERVER['argv']))
{
	if (isset($_SERVER['OS']) && $_SERVER['OS'] == 'Windows_NT')
	{
		// Development environment on Windows
		\Fuel::$env = \Fuel::DEVELOPMENT;
	}
	else
	{
		\Fuel::$env = \Arr::get($_SERVER, 'FUEL_ENV', \Arr::get($_ENV, 'FUEL_ENV', \Fuel::PRODUCTION));
	}
}
else
{
	\Fuel::$env = \Arr::get($_SERVER, 'FUEL_ENV', \Arr::get($_ENV, 'FUEL_ENV', \Fuel::PRODUCTION));
}

// Initialize the framework with the config file.
\Fuel::init('config.php');
