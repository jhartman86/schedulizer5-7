<?php

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository as ConfigRepository;
use Concrete\Core\Config\FileLoader;
use Illuminate\Filesystem\Filesystem;

class EphemeralConfigSaver implements Concrete\Core\Config\SaverInterface {
    public function save($item, $value, $environment, $group, $namespace = null){}
}

/**
 * ----------------------------------------------------------------------------
 * Instantiate concrete5
 * ----------------------------------------------------------------------------
 */
$app = new Application();


/**
 * ----------------------------------------------------------------------------
 * Detect the environment based on the hostname of the server
 * ----------------------------------------------------------------------------
 */
$app->detectEnvironment(
    array(
        'local' => array(
            'hostname'
        ),
        'production' => array(
            'live.site'
        )
    ));

$file_system  = new Filesystem();
$ephem_loader = new FileLoader($file_system);
$ephem_saver  = new EphemeralConfigSaver();
$app->instance('config', new ConfigRepository($ephem_loader, $ephem_saver, $app->environment()));

return $app;