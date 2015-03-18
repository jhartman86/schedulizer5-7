#!/usr/bin/env php
<?php
require_once(__DIR__ . '/_shared.php');
define('DIR_CORE', DIR_BASE . '/concrete');
require DIR_CORE . "/bootstrap/configure.php";
require DIR_CORE . "/bootstrap/autoload.php";
require DIR_CORE . "/bootstrap/start.php";
Cache::disableAll();

fwrite(STDOUT, "\n************* AUTO-UPDATING INSTALLED CONCRETE5 PACKAGES *************\n");

set_exception_handler(function( $ex ){
    fwrite(STDOUT, "Failed on package update: ", $ex->getMessage());
    exit(0);
});

// Get a list of packages where updates are available
try {
    $upgradeable = Concrete\Core\Package\Package::getLocalUpgradeablePackages();
    if( !empty($upgradeable) ){
        // @note: $pkgObj is just the $pkgClass WITHOUT an ID, thats why we get an $instance below
        foreach($upgradeable AS $pkgObj){
            $handle     = $pkgObj->getPackageHandle();
            $vCurrent   = $pkgObj->getPackageCurrentlyInstalledVersion();
            $vAvailable = $pkgObj->getPackageVersion();
            fwrite(STDOUT, "--- Updating {$handle}: v{$vCurrent} -> v{$vAvailable}\n");
            /** @var $instance Concrete\Core\Package\Package */
            $instance   = Concrete\Core\Package\Package::getByHandle($handle);
            // Run upgrade process
            $tests = Concrete\Core\Package\Package::testForInstall($handle, false);
            if( is_array($tests) ){
                $tests = Concrete\Core\Package\Package::mapError($tests);
                fwrite(STDERR, sprintf("ERRORS: %s", print_r($tests,true)));
            }else{
                $instance->upgradeCoreData();
                $instance->upgrade();
                fwrite(STDOUT, "    OK!\n");
            }
        }
        exit(0);
    }

    // If we get here, nothing to do...
    fwrite(STDOUT, "No Packages to update, moving on...\n\n");
}catch(Exception $ex){
    fwrite(STDERR, sprintf("\n %s caught: %s", get_class($ex), $ex->getMessage()));
    exit(0);
}


//$pkgObj = Concrete\Core\Package\Package::getByHandle('schedulizer');
//if( is_object($pkgObj) ){
//    $pkgObj->uninstall();
//}
//
//Concrete\Core\Package\Package::getClass('schedulizer')->install();

exit(0);