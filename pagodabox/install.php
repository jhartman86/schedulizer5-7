<?php
use Concrete\Core\Database\Connection\ConnectionFactory;
use Concrete\Core\Cache\Cache;
use Hautelook\Phpass\PasswordHash;
use Concrete\Core\Updater\Migrations\Configuration;

// Setup as ephemeral cache during install; error logging, constant definitions
require_once(__DIR__ . '/_shared.php');

fwrite(STDOUT, "\n************* BEGINNING INSTALLATION *************\n");

// If DBx key not in $_SERVER variables...
if( count(preg_grep("/DATABASE1./",array_keys($_SERVER))) === 0 ){
    $_SERVER['DATABASE1_HOST'] = '127.0.0.1';
    $_SERVER['DATABASE1_USER'] = 'root';
    $_SERVER['DATABASE1_PASS'] = 'root';
    $_SERVER['DATABASE1_NAME'] = 'concrete5_site';
}

$cliconfig = array(
    'admin-email'       => 'change@me.com',
    'admin-password'    => 'c5@dmin',
    'starting-point'    => 'elemental_blank',
    'site'              => 'Concrete5 Site',
    'core'              => DIR_BASE . '/concrete',
    // DB CREDENTIALS
    'db-server'         => $_SERVER['DATABASE1_HOST'],
    'db-username'       => $_SERVER['DATABASE1_USER'],
    'db-password'       => $_SERVER['DATABASE1_PASS'],
    'db-database'       => $_SERVER['DATABASE1_NAME']
);

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

// Test that the database is empty; fail if not
$db = Loader::db();
if( count($db->GetCol("SHOW TABLES")) > 0 ){
    fwrite(STDERR, "\nDatabase already installed; leaving existing installation untouched and moving on...\n\n");
    exit(0);
}

// Install the database: this is the same thing in the StartingPointPackage
// without the generateProxyClasses call to the (now) non-writable directory!
Package::installDB(DIR_BASE_CORE . '/config/db.xml');
// Index additional fields
$db->Execute('ALTER TABLE PagePaths ADD INDEX (`cPath` (255))');
$db->Execute('ALTER TABLE Groups ADD INDEX (`gPath` (255))');
$db->Execute('ALTER TABLE SignupRequests ADD INDEX (`ipFrom` (32))');
$db->Execute('ALTER TABLE UserBannedIPs ADD UNIQUE INDEX (ipFrom (32), ipTo(32))');
$db->Execute(
    'ALTER TABLE QueueMessages ADD FOREIGN KEY (`queue_id`) REFERENCES `Queues` (`queue_id`) ON DELETE CASCADE ON UPDATE CASCADE'
);
$configuration = new Configuration();
$version = $configuration->getVersion(Config::get('concrete.version_db'));
$version->markMigrated();

// Starting Point that ignores the things we did above manually
$startingPoint = \Concrete\Core\Package\StartingPointPackage::getClass(INSTALL_STARTING_POINT);
$routines      = $startingPoint->getInstallRoutines();

$skipMethods = array('install_database', 'finish');

// Redefine the error handlers, overriding any registered by C5
set_error_handler('customErrorHandler');

foreach($routines AS $r){
    if( !in_array($r->getMethod(), $skipMethods) ){
        fwrite(STDOUT, sprintf("%s: %s \n", $r->getProgress(), $r->getText()));
        call_user_func(array($startingPoint, $r->getMethod()));
    }
}

fwrite(STDOUT, "!!!!!! Installation Complete: OK !!!!!!\n");

exit(0);