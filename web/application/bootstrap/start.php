<?php

use Concrete\Core\Application\Application;

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

/**
 * Override Concrete5's config persistence method.
 */
\Application\Src\Config\Ephemeral::bindToApp($app);

return $app;