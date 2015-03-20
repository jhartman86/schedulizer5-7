<?php namespace Schedulizer\Tests\Calendar {

    use Concrete\Package\Schedulizer\Src\Calendar;
    use Concrete\Core\Package\Package;
    use \Doctrine\ORM\Tools\SchemaTool;

    /**
     * Class CalendarDatabaseTest
     * @see http://jamesmcfadden.co.uk/phpunit-and-doctrine-2-orm-caching-issues/
     * @package Schedulizer\Tests\Calendar
     */
    class CalendarDatabaseTest extends \Schedulizer\Tests\DatabaseTestCase {

        const TABLE_NAME = 'SchedulizerCalendar';

        protected static $packageObj    = null;
        protected static $structManager = null;
        protected static $entityManager = null;
        protected static $metadatas     = null;

        public static function setUpBeforeClass(){
            self::$packageObj    = Package::getClass('schedulizer');
            self::$structManager = self::$packageObj->getDatabaseStructureManager();
            self::$entityManager = self::$structManager->getEntityManager();
            self::$metadatas     = self::$structManager->getMetadatas();
        }

        /**
         * Use Doctrine's destroy/create schema facilities to destroy and
         * create for each test.
         */
        public function setUp(){
            $schemaTool = new SchemaTool(self::$entityManager);
            $schemaTool->dropSchema(self::$metadatas);
            $schemaTool->createSchema(self::$metadatas);
            parent::setUp();
//            $structManager = Package::getByHandle('schedulizer')->getDatabaseStructureManager();
//            $entityManager = $structManager->getEntityManager();
//            $entityManager->clear();
//            $schemaTool = new SchemaTool($entityManager);
//            $entities   = $structManager->getMetadatas();
//            $schemaTool->dropSchema($entities);
//            $schemaTool->createSchema($entities);
//            parent::setUp();
        }

        public function testCalendarGetsInstanceByID(){
            $instance = Calendar::getByID(1);
            $this->assertInstanceOf('Concrete\Package\Schedulizer\Src\Calendar', $instance);
        }

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

        public function testCalendarCreate(){
            $rowsBefore = $this->getConnection()->getRowCount(self::TABLE_NAME);
            Calendar::create(array(
                'title'             => 'My Title',
                'ownerID'           => 22,
                'defaultTimezone'   => 'America/Los_Angeles'
            ));
            $this->assertEquals(($rowsBefore + 1), $this->getConnection()->getRowCount(self::TABLE_NAME), 'Inserting Calendar Failed');
        }

        public function testCalendarDelete(){
            $rowsBefore = $this->getConnection()->getRowCount(self::TABLE_NAME);
            Calendar::getByID(1)->delete();
            $this->assertEquals(($rowsBefore - 1), $this->getConnection()->getRowCount(self::TABLE_NAME), 'Deleting Calendar Failed');
        }

        public function testCalendarUpdateIsPersisted(){
            Calendar::getByID(1)->update(array(
                'title'             => 'FancyPants',
                'defaultTimezone'   => 'Canada',
                'ownerID'           => 1999
            ));
            $resultingTable = $this->getConnection()->createQueryTable(self::TABLE_NAME, "SELECT * FROM SchedulizerCalendar WHERE id = 1");
            $expectedTable  = $this->getDataSet('CalendarDatabaseUpdate')->getTable(self::TABLE_NAME);
            $this->assertTablesEqual($expectedTable, $resultingTable);
        }

    }

}