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
    abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase {

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


        public function getDataSet( $override = null ){
            $reflector   = new \ReflectionClass(get_called_class());
            $fixturePath = dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . 'fixtures';
            $fileName    = (is_string($override)) ? sprintf('%s.xml', $override) : sprintf('%s.xml', $reflector->getShortName());
            return $this->createXMLDataSet($fixturePath . DIRECTORY_SEPARATOR . $fileName);
        }

    }

}