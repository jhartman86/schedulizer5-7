<?php namespace Schedulizer\Tests\Event {

    use Concrete\Package\Schedulizer\Src\Calendar;
    use Concrete\Package\Schedulizer\Src\Event;
    use Concrete\Package\Schedulizer\Src\EventRepeat;
    use Concrete\Package\Schedulizer\Src\EventRepeatNullify;
    use Concrete\Package\Schedulizer\Src\EventTag;

    /**
     * Class EventDatabaseTest
     * @package Schedulizer\Tests\Event
     */
    class EventDatabaseTest extends \Schedulizer\Tests\DatabaseTestCase {

        use \Schedulizer\Tests\EntityManagerTrait;

        const TABLE_NAME_EVENT              = 'SchedulizerEvent';
        const TABLE_NAME_EVENTREPEAT        = 'SchedulizerEventRepeat';
        const TABLE_NAME_EVENTREPEATNULLIFY = 'SchedulizerEventRepeatNullify';
        const TABLE_NAME_EVENTTAG           = 'SchedulizerEventTag';
        const TABLE_NAME_TAGGEDEVENTS       = 'SchedulizerTaggedEvents'; // the join table

        /** @var $calendarObj Calendar */
        protected $calendarObj;

        protected static $newEventSettingSample = array(
            'startUTC'              => '2015-02-19 14:30:00',
            'endUTC'                => '2015-02-19 17:15:00',
            'title'                 => 'A new event',
            'description'           => 'this is the descr',
            'useCalendarTimezone'   => Event::USE_CALENDAR_TIMEZONE_TRUE,
            'isAllDay'              => false,
            'isRepeating'           => false,
            'repeatIndefinite'      => false,
            'ownerID'               => 14
        );

        public static function setUpBeforeClass(){
            $static = new self();
            $static->packageEntityManager()->clear();
            $static->destroySchema()->createSchema();
        }

        public static function tearDownAfterClass(){
            //self::setUpBeforeClass();
        }

        /**
         * Use Doctrine's destroy/create schema facilities to destroy and
         * create for each test.
         */
        public function setUp(){
            $this->execWithoutConstraints(function(){
                parent::setUp();
                $this->packageEntityManager()->clear();
            });
            $this->calendarObj = Calendar::getByID(1);
        }

        /**
         * @todo: test ALL properties
         */
        public function testEventGetInstanceByID(){
            $instance = Event::getByID(1);
            $this->assertEquals(1, $instance->getID());
            $this->assertEquals('First Event Name', $instance->getTitle());
            $this->assertEquals('Lorem ipsum dolor sit amet consect', $instance->getDescription());
            $this->assertInstanceOf('DateTime', $instance->getStartUTC());
            $this->assertInstanceOf('DateTime', $instance->getEndUTC());
            $this->assertInstanceOf('DateTime', $instance->getRepeatEndUTC());
            $this->assertInstanceOf('DateTime', $instance->getCreatedUTC());
            $this->assertInstanceOf('DateTime', $instance->getModifiedUTC());
        }

        /**
         * @expectedException \Exception
         */
        public function testCreateEventWithoutCalendarAssociationFails(){
            Event::create(self::$newEventSettingSample);
        }

        /**
         * Create an event with minimal info passed (whats required) and ensure
         * proper defaults are set.
         */
        public function testCreateEventHasProperDefaults(){
            /** @var $eventObj Event */
            $eventObj = Event::create(array(
                'title' => 'Something',
                'ownerID' => 12,
                'calendarInstance' => $this->calendarObj
            ));

            $this->assertEquals(1, $eventObj->getCalendar()->getID());
            $this->assertEquals(null, $eventObj->getDescription());
            $this->assertEquals(false, $eventObj->getOpenEnded());
            $this->assertEquals(false, $eventObj->getIsAllDay());
            $this->assertEquals(true, $eventObj->getUseCalendarTimezone());
            $this->assertEquals('America/New_York', $eventObj->getTimezoneName());
            $this->assertEquals(Event::EVENT_COLOR_DEFAULT, $eventObj->getEventColor());
            $this->assertEquals(false, $eventObj->getIsRepeating());
            $this->assertEquals(null, $eventObj->getRepeatTypeHandle());
            $this->assertEquals(null, $eventObj->getRepeatEvery());
        }

        /**
         * Test cascading persistence on the calendar for automatically creating a new
         * event.
         */
        public function testPersistingOneEventByCascadingCalendarSave(){
            $rowsBefore = $this->getConnection()->getRowCount('SchedulizerEvent');
            $event1 = new Event(self::$newEventSettingSample);
            $this->calendarObj->addEvent($event1);
            $this->calendarObj->save();
            $this->assertEquals(($rowsBefore + 1), $this->getConnection()->getRowCount('SchedulizerEvent'));
        }

        /**
         * Test cascading persistence on the calendar for automatically creating multiple
         * events.
         */
        public function testPersistingMultipleEventsByCascadingCalendarSave(){
            $rowsBefore = $this->getConnection()->getRowCount('SchedulizerEvent');
            $event1 = new Event(self::$newEventSettingSample);
            $this->calendarObj->addEvent($event1);
            $event2 = new Event(self::$newEventSettingSample);
            $this->calendarObj->addEvent($event2);
            $event3 = new Event(self::$newEventSettingSample);
            $this->calendarObj->addEvent($event3);
            $this->calendarObj->save();
            $this->assertEquals(($rowsBefore + 3), $this->getConnection()->getRowCount('SchedulizerEvent'));
        }

        /**
         * Test that creating a new event (via adding to the calendar events array collection)
         * is tracked via the Calendar's array collection.
         */
        public function testCalendarEventArrayCollectionSyncd(){
            $this->assertEquals(2, $this->calendarObj->getEvents()->count());
            $event1 = new Event(self::$newEventSettingSample);
            $this->calendarObj->addEvent($event1)->save();
            $this->assertEquals(3, $this->calendarObj->getEvents()->count());
        }

        /**
         * Test updating an existing event and then query the database directory to ensure
         * that it was captured.
         */
        public function testEventUpdate(){
            $eventObj = Event::getByID(3);

            $eventObj->update(array(
                'title'                 => 'MyNewTitle',
                'openEnded'             => Event::OPEN_ENDED_TRUE,
                'useCalendarTimezone'   => Event::USE_CALENDAR_TIMEZONE_FALSE,
                'timezoneName'          => 'America/New_York'
            ));

            $res = $this->getRawConnection()
                        ->query("SELECT * FROM SchedulizerEvent WHERE id = 3")
                        ->fetch(\PDO::FETCH_OBJ);

            $this->assertEquals('MyNewTitle', $res->title);
            $this->assertEquals(1, $res->openEnded);
            $this->assertEquals(0, $res->useCalendarTimezone);
            $this->assertEquals('America/New_York', $res->timezoneName);
        }

        /**
         * Test deleting a calendar cascades deletion to all associated events.
         */
        public function testDeletingACalendarCascadesDeletingAssociatedEvents(){
            $calendarID = $this->calendarObj->getID();

            $this->calendarObj->delete();

            // Count rows in SchedulizerCalendar table with ID of one just deleted
            $calendarRowCount = $this->getRawConnection()
                                     ->query("SELECT * FROM SchedulizerCalendar WHERE id = {$calendarID}")
                                     ->rowCount();

            // Count rows in SchedulizerEvent table with calendarID of one just deleted
            $eventRowCount = $this->getRawConnection()
                                  ->query("SELECT * FROM SchedulizerEvent WHERE calendarID = {$calendarID}")
                                  ->rowCount();

            $this->assertEquals(0, $calendarRowCount);
            $this->assertEquals(0, $eventRowCount);
        }

    }
}