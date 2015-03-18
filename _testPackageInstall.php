#!/usr/bin/env php
<?php
define('C5_ENVIRONMENT_ONLY', true);
define('C5_EXECUTE', true);
define('DIR_BASE', dirname(__FILE__) . '/web');
define('DIR_CORE', DIR_BASE . '/concrete');
require DIR_CORE . "/bootstrap/configure.php";
require DIR_CORE . "/bootstrap/autoload.php";
require DIR_CORE . "/bootstrap/start.php";
Cache::disableAll();

$pkgObj = Concrete\Core\Package\Package::getByHandle('schedulizer');
if( is_object($pkgObj) ){
    $pkgObj->uninstall();
}

Concrete\Core\Package\Package::getClass('schedulizer')->install();

exit(0);