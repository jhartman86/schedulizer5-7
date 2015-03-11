#!/usr/bin/env php
<?php
use Concrete\Core\Cache\Cache;
use Hautelook\Phpass\PasswordHash;

// Setup as ephemeral cache during install; error logging, constant definitions
require_once(__DIR__ . '/_shared.php');

fwrite(STDOUT, "\n************* BEGINNING INSTALLATION *************\n");

$_defaults = array(
    'site'              => 'Concrete5',
    'admin-email'       => 'change@me.com',
    'admin-password'    => 'c5@dmin',
    'starting-point'    => 'virgin',
    'db-server'         => $_SERVER['DATABASE1_HOST'],
    'db-username'       => $_SERVER['DATABASE1_USER'],
    'db-password'       => $_SERVER['DATABASE1_PASS'],
    'db-database'       => $_SERVER['DATABASE1_NAME'],
    'target'            => DIR_BASE,
    'core'              => DIR_BASE . '/concrete'
);

$cliArguments = array();
foreach(array_slice($argv, 1) AS $arg){
    $opt = explode('=', $arg);
    $cliArguments[str_replace('--', '', $opt[0])] = trim(isset($opt[1]) ? $opt[1] : '', '\'"');
}

$cliconfig = array_merge($_defaults, $cliArguments);

// Configurations
require $cliconfig['core'] . "/bootstrap/configure.php";
// Autoloader
require $cliconfig['core'] . "/bootstrap/autoload.php";
// CMS
$cms = require $cliconfig['core'] . "/bootstrap/start.php";

// Database connection
\Database::extend('install', function() use($cliconfig){
    return \Database::getFactory()->createConnection(array(
        'host' => $cliconfig['db-server'],
        'user' => $cliconfig['db-username'],
        'password' => $cliconfig['db-password'],
        'database' => $cliconfig['db-database']
    ));
});
\Database::setDefaultConnection('install');
$cms['config']['database.connections.install'] = array();

// Disable all caches
Cache::disableAll();

// Install data setup
$passHash = new PasswordHash(Config::get('concrete.user.password.hash_cost_log2'), Config::get('concrete.user.password.hash_portable'));
define('INSTALL_USER_EMAIL', $cliconfig['admin-email']);
define('INSTALL_USER_PASSWORD_HASH', $passHash->HashPassword($cliconfig['admin-password']));
define('INSTALL_STARTING_POINT', $cliconfig['starting-point']);
define('SITE', $cliconfig['site']);

$startingPoint = StartingPointPackage::getClass(INSTALL_STARTING_POINT);
$routines      = $startingPoint->getInstallRoutines();

// Redefine the error handlers, overriding any registered by C5
set_error_handler('customErrorHandler');

foreach($routines AS $r){
    fwrite(STDOUT, sprintf("%s: %s \n", $r->getProgress(), $r->getText()));
    call_user_func(array($startingPoint, $r->getMethod()));
}

fwrite(STDOUT, "!!!!!! Installation Complete: OK !!!!!!\n");
exit(0);