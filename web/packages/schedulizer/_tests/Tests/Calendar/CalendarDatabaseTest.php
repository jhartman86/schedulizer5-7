<?php namespace Schedulizer\Tests\Calendar {

    use Concrete\Package\Schedulizer\Src\Calendar;
    use \Doctrine\ORM\Tools\SchemaTool;

    /**
     * Class CalendarDatabaseTest
     * @package Schedulizer\Tests\Calendar
     */
    class CalendarDatabaseTest extends \Schedulizer\Tests\DatabaseTestCase {

        use \Schedulizer\Tests\EntityManagerTrait;

        const TABLE_NAME = 'SchedulizerCalendar';

        public static function setUpBeforeClass(){
            $static       = new self();
            $calendarMeta = array($static->packageMetadatas('Concrete\Package\Schedulizer\Src\Calendar'));
            $schemaTool   = new SchemaTool($static->packageEntityManager());
            $schemaTool->dropSchema($calendarMeta);
            $schemaTool->createSchema($calendarMeta);
        }

        /**
         * Use Doctrine's destroy/create schema facilities to destroy and
         * create for each test.
         */
        public function setUp(){
            // $this->packageEntityManager()->clear() ?
            parent::setUp();
        }

        /**
         * GetByID method returns a calendar instance
         */
        public function testCalendarGetsInstanceByID(){
            $instance = Calendar::getByID(1);
            $this->assertInstanceOf('Concrete\Package\Schedulizer\Src\Calendar', $instance);
        }

        /**
         * GetByID returns a POPULATED calendar instance
         */
        public function testCalendarDataHydrationByDoctrine(){
            /** @var $instance \Concrete\Package\Schedulizer\Src\Calendar */
            $instance = Calendar::getByID(1);
            $this->assertEquals(1, $instance->getID());
            $this->assertEquals('Title 1', $instance->getTitle());
            $this->assertEquals('UTC', $instance->getDefaultTimezone());
            $this->assertEquals(12, $instance->getOwnerID());
            $this->assertInstanceOf('DateTime', $instance->getCreatedUTC());
            $this->assertInstanceOf('DateTime', $instance->getModifiedUTC());
        }

        /**
         * Calendar is persisted on create
         */
        public function testCalendarCreate(){
            $rowsBefore = $this->getConnection()->getRowCount(self::TABLE_NAME);
            Calendar::create(array(
                'title'             => 'My Title',
                'ownerID'           => 22,
                'defaultTimezone'   => 'America/Los_Angeles'
            ));
            $this->assertEquals(($rowsBefore + 1), $this->getConnection()->getRowCount(self::TABLE_NAME), 'Inserting Calendar Failed');
        }

        /**
         * Calendar record is actually removed from the db
         */
        public function testCalendarDelete(){
            $rowsBefore = $this->getConnection()->getRowCount(self::TABLE_NAME);
            Calendar::getByID(1)->delete();
            $this->assertEquals(($rowsBefore - 1), $this->getConnection()->getRowCount(self::TABLE_NAME), 'Deleting Calendar Failed');
        }

        /**
         * Updating an existing calendar instance is persisted properly
         * @todo: this one is tricky since the modifiedUTC method SHOULD be different; for
         * now we're just ommitting the result
         */
        public function testCalendarUpdateIsPersisted(){
            Calendar::getByID(1)->update(array(
                'title'             => 'FancyPants',
                'defaultTimezone'   => 'Canada',
                'ownerID'           => 1999
            ));

            // Load another dataset into a data set filter
            $expectedTable = new \PHPUnit_Extensions_Database_DataSet_DataSetFilter($this->getDataSet('CalendarDatabaseUpdate'));
            $expectedTable->setExcludeColumnsForTable('SchedulizerCalendar', array('modifiedUTC'));

            // Query for result WITHOUT modifiedUTC column
            $resultingTable = $this->getConnection()->createQueryTable(self::TABLE_NAME,
                "SELECT id, title, ownerID, defaultTimezone, createdUTC FROM SchedulizerCalendar WHERE id = 1"
            );

            $this->assertTablesEqual($expectedTable->getTable(self::TABLE_NAME), $resultingTable);
        }

    }

}