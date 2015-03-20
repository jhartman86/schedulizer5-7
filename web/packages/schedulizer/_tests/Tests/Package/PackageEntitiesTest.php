<?php namespace Schedulizer\Tests\Package {

    use \Concrete\Core\Package\Package;

    /**
     * Class PackageTest
     * @package Schedulizer\Tests
     * @todo checklist:
     *  Package won't install on versions <5.7.3.2
     *  Package install method creates proxy files
     *  Package install creates database tables correctly
     *  Package update doesn't wipe data
     */
    class PackageEntitiesTest extends \PHPUnit_Framework_TestCase {

        const PROXY_PATH_AND_PREFIX         = DIR_CONFIG_SITE . '/doctrine/proxies/__CG__ConcretePackageSchedulizerSrc';

        protected $cNameCalendar            = 'Concrete\Package\Schedulizer\Src\Calendar';
        protected $cNameEvent               = 'Concrete\Package\Schedulizer\Src\Event';
        protected $cNameEventRepeat         = 'Concrete\Package\Schedulizer\Src\EventRepeat';
        protected $cNameEventRepeatNullify  = 'Concrete\Package\Schedulizer\Src\EventRepeatNullify';

        protected static $packageObj    = null;
        protected static $structManager = null;
        protected static $entityManager = null;
        protected static $metadatas     = null;

        public static function setUpBeforeClass(){
            self::$packageObj    = Package::getClass('schedulizer');
            self::$structManager = self::$packageObj->getDatabaseStructureManager();
            self::$entityManager = self::$structManager->getEntityManager();
            self::$metadatas     = self::$structManager->getMetadatas();
            // NUKE ANY PREVIOUSLY GENERATED PROXY CLASSES!
            self::$structManager->destroyProxyClasses('ConcretePackageSchedulizerSrc');
        }

        public static function tearDownAfterClass(){
            self::$structManager->destroyProxyClasses('ConcretePackageSchedulizerSrc');
        }

        /**
         * Make sure Concrete is doing its job...
         */
        public function testCorePackageDetectionOfEntities(){
            $this->assertTrue(self::$structManager->hasEntities());
        }

        public function testCalendarClassEntityMetadataDetected(){
            $this->assertContains(
                $this->cNameCalendar,
                array_keys(self::$metadatas),
                'Doctrine metadata parser failed to parse Calendar'
            );
        }

        public function testCalendarClassMetadataCorrect(){
            /** @var $metaDef \Doctrine\ORM\Mapping\ClassMetadata */
            $metaDef = self::$entityManager->getClassMetadata($this->cNameCalendar);
            $columns = $metaDef->getColumnNames();
            $this->assertContains('id', $columns);
            $this->assertContains('title', $columns);
            $this->assertContains('defaultTimezone', $columns);
            $this->assertContains('ownerID', $columns);
            $this->assertContains('createdUTC', $columns);
            $this->assertContains('modifiedUTC', $columns);
        }

        public function testEventClassEntityMetadataDetected(){
            $this->assertContains(
                $this->cNameEvent,
                array_keys(self::$metadatas),
                'Doctrine metadata parser failed to parse Event'
            );
        }

        public function testEventClassMetadataCorrect(){
            /** @var $metaDef \Doctrine\ORM\Mapping\ClassMetadata */
            $metaDef = self::$entityManager->getClassMetadata($this->cNameEvent);
            $columns = $metaDef->getColumnNames();
            $this->assertContains('id', $columns);
            $this->assertContains('calendarID', $columns);
            $this->assertContains('title', $columns);
            $this->assertContains('description', $columns);
            $this->assertContains('startUTC', $columns);
            $this->assertContains('endUTC', $columns);
            $this->assertContains('isAllDay', $columns);
            $this->assertContains('useCalendarTimezone', $columns);
            $this->assertContains('timezoneName', $columns);
            $this->assertContains('eventColor', $columns);
            $this->assertContains('isRepeating', $columns);
            $this->assertContains('repeatTypeHandle', $columns);
            $this->assertContains('repeatEvery', $columns);
            $this->assertContains('repeatIndefinite', $columns);
            $this->assertContains('repeatEndUTC', $columns);
            $this->assertContains('repeatMonthlyMethod', $columns);
            $this->assertContains('ownerID', $columns);
            $this->assertContains('createdUTC', $columns);
            $this->assertContains('modifiedUTC', $columns);
        }

        public function testEventRepeatClassEntityMetadataDetected(){
            $this->assertContains(
                $this->cNameEventRepeat,
                array_keys(self::$metadatas),
                'Doctrine metadata parser failed to parse EventRepeat'
            );
        }

        public function testEventRepeatClassMetadataCorrect(){
            /** @var $metaDef \Doctrine\ORM\Mapping\ClassMetadata */
            $metaDef = self::$entityManager->getClassMetadata($this->cNameEventRepeat);
            $columns = $metaDef->getColumnNames();
            $this->assertContains('id', $columns);
            $this->assertContains('eventID', $columns);
            $this->assertContains('repeatWeek', $columns);
            $this->assertContains('repeatDay', $columns);
            $this->assertContains('repeatWeekday', $columns);
        }

        public function testEventRepeatNullifyClassEntityMetadataDetected(){
            $this->assertContains(
                $this->cNameEventRepeatNullify,
                array_keys(self::$metadatas),
                'Doctrine metadata parser failed to parse EventRepeatNullify'
            );
        }

        public function testEventRepeatNullifyClassMetadataCorrect(){
            /** @var $metaDef \Doctrine\ORM\Mapping\ClassMetadata */
            $metaDef = self::$entityManager->getClassMetadata($this->cNameEventRepeatNullify);
            $columns = $metaDef->getColumnNames();
            $this->assertContains('eventID', $columns);
            $this->assertContains('hideOnDate', $columns);
        }


        /**
         * Works just fine... need to test?
         */
        public function testDestroyingProxyClasses(){

        }

        public function testCreatingProxyClasses(){
            self::$structManager->generateProxyClasses();
            $this->assertFileExists(sprintf('%sCalendar.php', self::PROXY_PATH_AND_PREFIX));
            $this->assertFileExists(sprintf('%sEvent.php', self::PROXY_PATH_AND_PREFIX));
            $this->assertFileExists(sprintf('%sEventRepeat.php', self::PROXY_PATH_AND_PREFIX));
            $this->assertFileExists(sprintf('%sEventRepeatNullify.php', self::PROXY_PATH_AND_PREFIX));
        }

    }

}