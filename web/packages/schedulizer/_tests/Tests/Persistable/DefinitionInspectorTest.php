<?php namespace Schedulizer\Tests\Persistable {

    use \Concrete\Package\Schedulizer\Src\Calendar;
    use \Concrete\Package\Schedulizer\Src\Event;
    use \Concrete\Package\Schedulizer\Src\EventRepeat;
    use Concrete\Package\Schedulizer\Src\EventRepeatNullify;
    use Concrete\Package\Schedulizer\Src\Persistable\DefinitionInspector;

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

//        public function testCreateEvent(){
//            Event::create(array(
//                'title' => 'frack ya',
//                'calendarID' => 'mk'
//            ));
//        }

        public function testSomething(){
            try {
                Event::getByID(1);
            }catch(\Exception $e){
                echo $e->getMessage();
            }

        }

//        /**
//         * @expectedException \Concrete\Package\Schedulizer\Src\Persistable\DefinitionInspectorException
//         */
//        public function testCalendarGet(){
//            Event::getByID(1);
//        }

//        public function testCalendarCreate(){
//            $cal = Calendar::create(array(
//                'title' => 'yolodsf',
//                'ownerID' => 22
//            ));
//            print_r($cal);
//            exit;
//        }
//
//        public function testCalendarUpdate(){
//            $cal = Calendar::getByID(3);
//            if(is_object($cal)){
//                $cal->update(array('title' => 'wtf mateasdfew'));
//            }
//            print_r($cal);
//        }

//        public function testCalendarDelete(){
//            Calendar::getByID(2)->delete();
//        }

    }

}