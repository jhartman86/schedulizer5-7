<?php //namespace Schedulizer\Tests\Event {
//
//    use Concrete\Package\Schedulizer\Src\Event;
//    use \Doctrine\ORM\Tools\SchemaTool;
//
//    /**
//     * Class EventDatabaseTest
//     * @package Schedulizer\Tests\Event
//     */
//    class EventDatabaseTest extends \Schedulizer\Tests\DatabaseTestCase
//    {
//
//        use \Schedulizer\Tests\EntityManagerTrait;
//
//        const TABLE_NAME = 'SchedulizerCalendar';
//
//        public static function setUpBeforeClass(){
//            $static = new self();
//            $calendarMeta = array(
//                $static->packageMetadatas('Concrete\Package\Schedulizer\Src\Event'),
//                $static->packageMetadatas('Concrete\Package\Schedulizer\Src\EventRepeat'),
//                $static->packageMetadatas('Concrete\Package\Schedulizer\Src\EventRepeatNullify')
//            );
//            $schemaTool = new SchemaTool($static->packageEntityManager());
//            $schemaTool->dropSchema($calendarMeta);
//            $schemaTool->createSchema($calendarMeta);
//        }
//
//        public static function tearDownAfterClass(){
//            self::setUpBeforeClass();
//        }
//
//        /**
//         * Use Doctrine's destroy/create schema facilities to destroy and
//         * create for each test.
//         */
//        public function setUp(){
//            // $this->packageEntityManager()->clear() ?
//            parent::setUp();
//        }
//
//        /**
//         * @todo: test ALL properties
//         */
//        public function testEventGetInstanceByID(){
//            $instance = Event::getByID(1);
//            $this->assertEquals(1, $instance->getID());
//            $this->assertEquals('My Event Name', $instance->getTitle());
//            $this->assertEquals('Lorem ipsum dolor sit amet consect', $instance->getDescription());
//            $this->assertInstanceOf('DateTime', $instance->getStartUTC());
//            $this->assertInstanceOf('DateTime', $instance->getEndUTC());
//            $this->assertInstanceOf('DateTime', $instance->getRepeatEndUTC());
//            $this->assertInstanceOf('DateTime', $instance->getCreatedUTC());
//            $this->assertInstanceOf('DateTime', $instance->getModifiedUTC());
//        }
//
//        public function testEventCreate(){
//            $rowsBefore = $this->getConnection()->getRowCount('SchedulizerEvent');
//            Event::create(array(
//                'calendarID' => 13,
//                'startUTC' => '2015-02-19 14:30:00',
//                'endUTC' => '2015-02-19 17:15:00',
//                'title' => 'A new event',
//                'description' => 'this is the descr',
//                'useCalendarTimezone' => 1,
//                //'timezoneName' => 'America/New_York',
//                'isAllDay' => false,
//                'isRepeating' => false,
//                'repeatIndefinite' => false,
//                'ownerID' => 14
//            ));
//            $this->assertEquals(($rowsBefore + 1), $this->getConnection()->getRowCount('SchedulizerEvent'));
//        }
//
//    }
//}