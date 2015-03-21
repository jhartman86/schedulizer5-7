<?php namespace Schedulizer\Tests {

    /**
     * Break out the connection properties to a trait so it can be reused horizontally
     * across different classes; otherwise we'd need this in DatabaseTestCase, where
     * it extends \PHPUnit_Extensions_Database_TestCase.
     *
     * Class DatabaseConnectionTrait
     * @package Schedulizer\Tests
     */
    trait DatabaseConnectionTrait {

        private $conn = null;

        final public function getConnection(){
            if( $this->conn === null ){
                $config     = \Config::get('database');
                $settings   = $config['connections'][$config['default-connection']];
                $database   = \Database::getFactory()->createConnection(array(
                    'host'      => $settings['server'],
                    'user'      => $settings['username'],
                    'password'  => $settings['password'],
                    'database'  => $settings['database']
                ));
                $this->conn = $this->createDefaultDBConnection($database->getWrappedConnection(), 'test');
            }
            return $this->conn;
        }

    }

}