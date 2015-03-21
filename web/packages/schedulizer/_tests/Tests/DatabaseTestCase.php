<?php namespace Schedulizer\Tests {

    /**
     * Class DatabaseTestCase
     * @package Schedulizer\Tests
     */
    abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase {

        use \Schedulizer\Tests\DatabaseConnectionTrait;

        public function getDataSet( $override = null ){
            $reflector   = new \ReflectionClass(get_called_class());
            $fixturePath = dirname($reflector->getFileName()) . DIRECTORY_SEPARATOR . 'fixtures';
            $fileName    = (is_string($override)) ? sprintf('%s.xml', $override) : sprintf('%s.xml', $reflector->getShortName());
            return $this->createXMLDataSet($fixturePath . DIRECTORY_SEPARATOR . $fileName);
        }

    }

}