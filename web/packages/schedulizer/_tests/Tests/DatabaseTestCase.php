<?php namespace Schedulizer\Tests {

    /**
     * Class PackageTest
     * @package Schedulizer\Tests
     * @todo checklist:
     *  Package won't install on versions <5.7.3.2
     *  Package install method creates proxy files
     *  Package install creates database tables correctly
     *  Package update doesn't wipe data
     */
    class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase {

        public function getConnection(){}
        public function getDataSet(){}

    }

}