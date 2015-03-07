<?php 
use Concrete\Core\Database\Connection\ConnectionFactory;
use Concrete\Core\Cache\Cache;

// Setup as ephemeral cache during install; error logging, constant definitions
require_once(__DIR__ . '/_shared.php');

fwrite(STDOUT, "\n************* BEGINNING AFTER_EXEC HOOK *************\n");

define('DIR_CORE', DIR_BASE . '/concrete');

require DIR_CORE . "/bootstrap/configure.php";
require DIR_CORE . "/bootstrap/autoload.php";
require DIR_CORE . "/bootstrap/start.php";

Cache::disableAll();

fwrite(STDOUT, "Generating Doctrine Proxy Classes\n");

$dbm = Core::make('database/structure', \ORM::entityManager('core'));
$dbm->generateProxyClasses();

fwrite(STDOUT, "Doctrine Proxy Classes Created OK!\n");

exit(0);