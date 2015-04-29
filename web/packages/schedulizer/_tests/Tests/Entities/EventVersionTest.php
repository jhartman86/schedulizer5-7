<?php namespace Schedulizer\Tests\Entities {

    use \Concrete\Package\Schedulizer\Src\Calendar;
    use \Concrete\Package\Schedulizer\Src\Event;
    use \Concrete\Package\Schedulizer\Src\EventVersion;

    /**
     * Class EventVersionTest
     * @package Schedulizer\Tests\Entities
     * @group ev
     */
    class EventVersionTest extends \PHPUnit_Framework_TestCase {

//        public function testOne(){
//            $calObj = Calendar::create(array(
//                'title' => 'A calendar'
//            ));
//
//            $evObj = Event::create(array(
//                'title' => 'lorem ipsum dolor',
//                'calendarID' => $calObj->getID()
//            ));
//
//            $evObj->update(array(
//                'title' => 'changed it! ' . rand(0,3)
//            ));
//
//            return $calObj;
//        }
//
//        /**
//         * @depends testOne
//         */
//        public function testTwo( $calObj ){
//            Event::create(array(
//                'title' => 'second event',
//                'calendarID' => $calObj->getID()
//            ));
//        }

        public function testThree(){
            $evObj = Event::getByID(1);
            $evObj->update(array(
                'title' => 'another new version eh'
            ));
            print_r($evObj);
        }

    }

}