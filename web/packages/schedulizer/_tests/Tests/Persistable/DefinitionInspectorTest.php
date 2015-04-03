<?php namespace Schedulizer\Tests\Persistable {

    use \Concrete\Package\Schedulizer\Src\Calendar;
    use \Concrete\Package\Schedulizer\Src\Persistable\DefinitionInspector;

    /**
     * @group persistable
     * @package Schedulizer\Tests\Persistable
     * @todo:
     * ✓ Package installs OK
     * ✗ Package won't install on versions < 5.7.3.2
     * ✗ Package update doesn't wipe data
     * ✗ Package update adjust schema correctly
     * ✗ Package uninstall deletes tables
     * ✗ Package uninstall wipes proxy classes
     */
    class DefinitionInspectorTest extends \PHPUnit_Framework_TestCase {

        public function testInspectObject(){
            //$parsed = DefinitionInspector::parse(new Calendar());
        }

//        public function testCalendarGet(){
//            $calendar = Calendar::getByID(1);
//            print_r($calendar);
//        }

        public function testCalendarCreate(){
            Calendar::create(array(
                'id'    => 13,
                'title' => 'yolodsf',
                'createdUTC' => '2015-02-01 17:30:34'
            ));
        }
//
//        public function testCalendarUpdate(){
//            $cal = Calendar::getByID(2);
//            if(is_object($cal)){
//                $cal->update(array('title' => 'wtf mate'));
//            }
//        }

    }

}