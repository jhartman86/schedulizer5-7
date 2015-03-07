<?php
// For custom install stuff
define('EPHEMERAL_ONLY_DURING_INSTALL', true);

// Setup error logging
error_reporting(E_ALL & ~E_NOTICE);
ini_set('log_errors', true);

// Define error handler
function customErrorHandler($errno, $errstr, $errfile, $errline){
    if( !(error_reporting() & $errno) ){
        // Error code not included in error_reporting
        return;
    }

    switch($errno){
        case E_NOTICE:
            fwrite(STDERR, "\n************** ERROR: NOTICE **************\n");
            break;
        case E_STRICT:
            fwrite(STDERR, "\n************** ERROR: STRICT **************\n");
            break;
        case E_WARNING:
            fwrite(STDERR, "\n************** ERROR: WARNING **************\n");
            break;
        case E_CORE_ERROR:
            fwrite(STDERR, "\n************** ERROR: CORE_ERROR **************\n");
            break;
        case E_CORE_WARNING:
            fwrite(STDERR, "\n************** ERROR: CORE_WARNING **************\n");
            break;
        case E_COMPILE_ERROR:
            fwrite(STDERR, "\n************** ERROR: COMPILE_ERROR **************\n");
            break;
        case E_COMPILE_WARNING:
            fwrite(STDERR, "\n************** ERROR: COMPILE_WARNING **************\n");
            break;
        case E_USER_NOTICE:
            fwrite(STDERR, "\n************** ERROR: USER_NOTICE **************\n");
            break;
        case E_USER_DEPRECATED:
            fwrite(STDERR, "\n************** ERROR: USER_DEPRECATED **************\n");
            break;
        case E_USER_ERROR:
            fwrite(STDERR, "\n************** ERROR: USER_ERROR **************\n");
            break;
        case E_USER_WARNING:
            fwrite(STDERR, "\n************** ERROR: USER_WARNING **************\n");
            break;
        case E_RECOVERABLE_ERROR:
            fwrite(STDERR, "\n************** ERROR: RECOVERABLE_ERROR **************\n");
            break;
        case E_DEPRECATED:
            fwrite(STDERR, "\n************** ERROR: DEPRECATED **************\n");
            break;
        case E_USER_DEPRECATED:
            fwrite(STDERR, "\n************** ERROR: USER_DEPRECATED **************\n");
            break;
    }
    fwrite(STDERR, "Report: " . $errstr . " on line " . $errline . " in " . $errfile . " [Type:".$errno."].\n\n");
}
// Bind custom error handling script
set_error_handler('customErrorHandler');

// Define constants commonly used by C5
define('C5_ENVIRONMENT_ONLY', true);
define('C5_EXECUTE', true);
define('DIR_BASE', dirname(dirname(__FILE__)) . '/web');