<?php namespace Schedulizer\Tests\Bin\Traits {

    class SettersSerializersTest extends \PHPUnit_Framework_TestCase {

        protected $mockObj;

        public function setUp(){
            $this->mockObj = $this->getObjectForTrait('Concrete\Package\Schedulizer\Src\Bin\Traits\SettersSerializers');
        }

        public function testSetPropertiesFromArray(){
            $this->mockObj->setPropertiesFromArray(array(
                'one' => 'value1',
                'rpg' => array('some', 'array', 'values')
            ));
            $this->assertObjectHasAttribute('one', $this->mockObj);
            $this->assertObjectHasAttribute('rpg', $this->mockObj);
            $this->assertInternalType('string', $this->mockObj->one);
            $this->assertInternalType('array', $this->mockObj->rpg);
        }

        public function testSetPropertiesFromObject(){
            $this->mockObj->setPropertiesFromObject((object)array(
                'one' => 'value1',
                'rpg' => array('some', 'array', 'values')
            ));
            $this->assertObjectHasAttribute('one', $this->mockObj);
            $this->assertObjectHasAttribute('rpg', $this->mockObj);
            $this->assertInternalType('string', $this->mockObj->one);
            $this->assertInternalType('array', $this->mockObj->rpg);
        }

        public function testJsonSerialization(){
            $this->mockObj->setPropertiesFromArray(array(
                'one' => 'value1',
                'rpg' => array('a', 'couple', 'values'),
                'kvd' => array('keya' => 'valuea', 'keyb' => 'valueb'),
                'sto' => (object) array('asd' => 'fgh', 'jkl' => '123')
            ));
            $serialized = json_encode($this->mockObj);
            $this->assertInternalType('string', $serialized);
        }

    }

}