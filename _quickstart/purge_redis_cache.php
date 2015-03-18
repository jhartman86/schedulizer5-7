#!/usr/bin/env php
<?php

require_once(__DIR__ . '/_shared.php');

fwrite(STDOUT, "--- Attempting to purge redis cache\n");

if( ! class_exists('Redis') ){
    fwrite(STDOUT, "\nWarning: Redis class does exist, bailing out...\n");
    exit(0);
}

try {
    $redis = new Redis();

    if( $redis->connect($_SERVER['CACHE1_HOST'], $_SERVER['CACHE1_PORT']) !== false ){
        // If we get here, we have access to Redis
        $count = $redis->dbSize();
        fwrite(STDOUT, "\n $count keys detected\n");
        $redis->flushAll();
        fwrite(STDOUT, "\n Flushed all keys OK\n");
        exit(0);
    }

    fwrite(STDOUT, "\nUnable to connect to Redis, bailing out...\n");
    exit(0);

}catch(Exception $e){
    fwrite(STDOUT, "Caught Exception: ", $e->getMessage());
    exit(0);
}