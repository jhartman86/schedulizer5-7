#!/usr/bin/env php
<?php
use Concrete\Core\Cache\Cache;
use Concrete\Core\Package\Package;

// Setup as ephemeral cache during install; error logging, constant definitions
require_once(__DIR__ . '/_shared.php');

fwrite(STDOUT, "\n************* BEGINNING AFTER_EXEC HOOK *************\n");

define('DIR_CORE', DIR_BASE . '/concrete');

require DIR_CORE . "/bootstrap/configure.php";
require DIR_CORE . "/bootstrap/autoload.php";
require DIR_CORE . "/bootstrap/start.php";

Cache::disableAll();

fwrite(STDOUT, "Generating Doctrine Proxy Classes\n");

/**
 * Core proxies
 */
$coreDBM = Core::make('database/structure', \ORM::entityManager('core'));
try {
    $coreDBM->destroyProxyClasses('ConcreteCore');
}catch(\Exception $e){ /* move on */ }
if( $coreDBM->hasEntities() ){
    $coreDBM->generateProxyClasses();
}

/**
 * Application proxies
 * @todo: flushAll() metadatacacheimpl (see package destroyproxyclasses() method); need redis and all service connections
 * @see Concrete/Controllers/SinglePage/Dashboard/System/Environment/Entities
 */
$applicationDBM = Core::make('database/structure', \ORM::entityManager());
try {
    $applicationDBM->destroyProxyClasses('ApplicationSrc');
}catch(\Exception $e){ /* move on */ }
if( $applicationDBM->hasEntities() ){
    $applicationDBM->generateProxyClasses();
}

/**
 * Package proxies
 * @todo: flushAll() metadatacacheimpl (see package destroyproxyclasses() method); need redis and all service connections
 * @see \Concrete\Core\Package\Package destroyproxyclasses() method
 */
// Get all package directory names (acts as the $pkgHandle)
$packageHandles = array_map(function( $absolutePath ){
    return basename($absolutePath);
}, array_filter(glob(DIR_BASE . '/packages/*'), 'is_dir'));

// Load the classes, purge and recreate proxies
foreach($packageHandles AS $pkgHandle){
    $packageObj = Package::getClass($pkgHandle);
    $packageDBM = $packageObj->getDatabaseStructureManager();
    try {
        $packageDBM->destroyProxyClasses('ConcretePackage' . camelcase($packageObj->getPackageHandle()) . 'Src');
    }catch(\Exception $e){ /* move on */ }
    if( $packageDBM->hasEntities() ){
        $packageDBM->generateProxyClasses();
    }
}

fwrite(STDOUT, "Doctrine Proxy Classes Created OK!\n");
exit(0);