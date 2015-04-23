<?php namespace Concrete\Package\Schedulizer {
    defined('C5_EXECUTE') or die(_("Access Denied."));

    /** @link https://github.com/concrete5/concrete5-5.7.0/blob/develop/web/concrete/config/app.php#L10-L90 Aliases */
    use Package; /** @see \Concrete\Core\Package\Package */
    use Database;
    use Config; /** @see \Concrete\Core */
    use Loader; /** @see \Concrete\Core\Legacy\Loader */
    use BlockType; /** @see \Concrete\Core\Block\BlockType\BlockType */
    use SinglePage; /** @see \Concrete\Core\Page\Single */
    use \DateTime; /** @see \DateTime */
    use \DateTimeZone; /** @see \DateTimeZone */
    use \Concrete\Core\Attribute\Key\Category AS AttributeKeyCategory;
    use \Concrete\Core\Attribute\Type AS AttributeType;
    use \Concrete\Package\Schedulizer\Src\Api\ApiOnStart;


    /**
     * Class Controller
     * @package Concrete\Package\Schedulizer
     * Make Doctrine suck less: http://labs.octivi.com/mastering-symfony2-performance-doctrine/
     */
    class Controller extends Package {

        // Package handle
        const PACKAGE_HANDLE    = 'schedulizer';
        // Config keys
        const DEFAULT_TIMEZONE  = 'DEFAULT_TIMEZONE';

        protected $pkgHandle                = self::PACKAGE_HANDLE;
        protected $appVersionRequired       = '5.7.3.2';
        protected $pkgVersion               = '0.46';

        public function getPackageName(){ return t('Schedulizer'); }
        public function getPackageDescription(){ return t('Schedulizer Calendar Package'); }


        /**
         * Pass in a handle-ized string and get the config key back.
         * @param $key
         * @return string
         */
        public static function configKey( $key ){
            return sprintf('%s.%s', self::PACKAGE_HANDLE, $key);
        }


        /**
         * C5's routing is hacked such that it doesn't mix into symphony's API, so we
         * override the whole thing here with our own routing detection PRIOR to letting
         * the C5 router run...
         */
        public function on_start(){
            define('SCHEDULIZER_IMAGE_PATH', DIR_REL . '/packages/' . $this->pkgHandle . '/images/');

            // Make the package-specific entity manager accessible via "make"; Note that
            // passing TRUE as the last argument to bind() has the effect of registering
            // in the service container as a singleton!
            \Core::bind('SchedulizerDB', function(){
                return Database::connection(Database::getDefaultConnection())->getWrappedConnection();
            }, true);

            // Composer Autoloader
            require __DIR__ . '/vendor/autoload.php';

            // @todo: add installation support tests for current timezone and provide notifications
            if( @date_default_timezone_get() !== 'UTC' ){
                @date_default_timezone_set('UTC');
            }

            ApiOnStart::execute(function( $apiOnStart ){
                /** @var $apiOnStart \Concrete\Package\Schedulizer\Src\Api\OnStart */
                // GET,POST,PUT,DELETE
                $apiOnStart->addRoute('calendar', 'CalendarResource');
                // GET,POST,PUT,DELETE
                $apiOnStart->addRoute('event', 'EventResource');
                // GET,POST,DELETE
                $apiOnStart->addRoute('event_time_nullify', 'EventTimeNullifyResource');
                // GET,POST,PUT,DELETE
                $apiOnStart->addRoute('event_tags', 'EventTagsResource');
                // GET
                $apiOnStart->addRoute('event_list', 'EventListResource');
                // GET
                $apiOnStart->addRoute('timezones', 'TimezoneResource');
            });
        }


        public function uninstall(){
            parent::uninstall();

            $tables   = array(
                'SchedulizerCalendar',
                'SchedulizerEvent',
                'SchedulizerEventTag',
                'SchedulizerTaggedEvents',
                'SchedulizerEventTime',
                'SchedulizerEventTimeWeekdays',
                'SchedulizerEventTimeNullify'
            );
            try {
                $database = Loader::db();
                $database->Execute(sprintf("SET foreign_key_checks = 0; DROP TABLE IF EXISTS %s; SET foreign_key_checks = 1", join(',', $tables)));
            }catch(\Exception $e){ /* do nothing */ }
        }


        /**
         * Ensure system dependencies are met (specifically, MySQL timezone tables and PHP datetime classes are working
         * correctly).
         * @todo: Tests for foreign key support and cascading deletes
         * @return bool
         * @throws \Exception
         */
        private function checkDependencies(){
            $support = new Src\Install\Support(Loader::db());

            if( ! $support->phpVersion() ){
                throw new \Exception(t("Schedulizer requires PHP 5.4 or greater; you are running %s.", phpversion()));
                return false;
            }

            if( ! $support->mysqlHasTimezoneTables() ){
                throw new \Exception('Schedulizer requires that MySQL has timezone tables installed, which they appear not to be. Please contact your hosting provider.');
                return false;
            }

            if( ! $support->phpDateTimeZoneConversionsCorrect() ){
                throw new \Exception('The DateTime class in PHP is not making correct conversions. Please ensure your PHP version is >= 5.4.');
                return false;
            }

            if( ! $support->phpDateTimeSupportsOrdinals() ){
                throw new \Exception('Your PHP version/installation does not support DateTime ordinals (relative) words. Please ensure your version is >= 5.4.');
                return false;
            }

            return true;
        }


        /**
         * @return void
         */
        public function upgrade(){
            $this->checkDependencies();
            parent::upgrade();
            $this->installAndUpdate();
        }


        /**
         * @return void
         */
        public function install() {
            $this->checkDependencies();
            $this->_packageObj = parent::install();
            $this->installAndUpdate();
        }


        /**
         * @todo: install via content.xml: 5.7.3.1 doesn't hook into packages properly
         */
        private function installAndUpdate(){
            $this->setupBlocks()
                 ->setupSinglePages()
                 ->setupAttributeCategories();

            /** @var $connection \PDO :: Setup foreign key associations */
            try {
                $connection = Database::connection(Database::getDefaultConnection())->getWrappedConnection();
                $connection->query("ALTER TABLE SchedulizerEvent ADD CONSTRAINT FK_calendar FOREIGN KEY (calendarID) REFERENCES SchedulizerCalendar(id) ON UPDATE CASCADE ON DELETE CASCADE");
                $connection->query("ALTER TABLE SchedulizerEventTime ADD CONSTRAINT FK_event FOREIGN KEY (eventID) REFERENCES SchedulizerEvent(id) ON UPDATE CASCADE ON DELETE CASCADE");
                $connection->query("ALTER TABLE SchedulizerEventTimeWeekdays ADD CONSTRAINT FK_eventTime FOREIGN KEY (eventTimeID) REFERENCES SchedulizerEventTime(id) ON UPDATE CASCADE ON DELETE CASCADE");
                $connection->query("ALTER TABLE SchedulizerEventTimeNullify ADD CONSTRAINT FK_eventTime2 FOREIGN KEY (eventTimeID) REFERENCES SchedulizerEventTime(id) ON UPDATE CASCADE ON DELETE CASCADE");
                // Tag associations
                $connection->query("ALTER TABLE SchedulizerTaggedEvents ADD CONSTRAINT FK_taggedEvent FOREIGN KEY (eventID) REFERENCES SchedulizerEvent(id) ON DELETE CASCADE");
                $connection->query("ALTER TABLE SchedulizerTaggedEvents ADD CONSTRAINT FK_taggedEvent2 FOREIGN KEY (eventTagID) REFERENCES SchedulizerEventTag(id) ON DELETE CASCADE");
            }catch(\Exception $e){ /** @todo: log out */ }
        }


        /**
         * @todo: Implement upgrade check; only set defaults on install.
         * @return Controller
         */
//        private function defaultSettings(){
//            $this->packageConfigObject()->save(self::configKey(self::DEFAULT_TIMEZONE), Config::get('app.timezone'));
//            return $this;
//        }


        /**
         * @return Controller
         */
        private function setupBlocks(){
            if(!is_object(BlockType::getByHandle('schedulizer'))) {
                BlockType::installBlockTypeFromPackage('schedulizer', $this->packageObject());
            }

            if(!is_object(BlockType::getByHandle('schedulizer_event'))) {
                BlockType::installBlockTypeFromPackage('schedulizer_event', $this->packageObject());
            }
            return $this;
        }


        /**
         * @return Controller
         */
        private function setupSinglePages(){
            // Dashboard pages
            SinglePage::add('/dashboard/schedulizer/', $this->packageObject());
            SinglePage::add('/dashboard/schedulizer/calendars', $this->packageObject());
            SinglePage::add('/dashboard/schedulizer/attributes', $this->packageObject());
            SinglePage::add('/dashboard/schedulizer/settings', $this->packageObject());
            // Hidden
            $spManage = SinglePage::add('/dashboard/schedulizer/calendars/manage', $this->packageObject());
            if( is_object($spManage) ){
                $spManage->setAttribute('exclude_nav', 1);
            }

            return $this;
        }


        /**
         * @return $this
         */
        private function setupAttributeCategories(){
            if( ! AttributeKeyCategory::getByHandle('schedulizer_event') ){
                $attrKeyCat = AttributeKeyCategory::add('schedulizer_event', AttributeKeyCategory::ASET_ALLOW_MULTIPLE, $this->packageObject());
                $attrKeyCat->associateAttributeKeyType( $this->attributeType('text') );
                $attrKeyCat->associateAttributeKeyType( $this->attributeType('boolean') );
                $attrKeyCat->associateAttributeKeyType( $this->attributeType('number') );
                $attrKeyCat->associateAttributeKeyType( $this->attributeType('textarea') );
                $attrKeyCat->associateAttributeKeyType( $this->attributeType('select') );
                $attrKeyCat->associateAttributeKeyType( $this->attributeType('image_file') );
            }

            return $this;
        }


        /**
         * @return \Concrete\Core\Config\Repository\Liaison
         */
        private function packageConfigObject(){
            if( $this->_packageConfigObj === null ){
                $this->_packageConfigObj = $this->packageObject()->getConfig();
            }
            return $this->_packageConfigObj;
        }


        /**
         * Get the package object; if it hasn't been instantiated yet, load it.
         * @return \Concrete\Core\Package\Package
         */
        private function packageObject(){
            if( $this->_packageObj === null ){
                $this->_packageObj = Package::getByHandle( $this->pkgHandle );
            }
            return $this->_packageObj;
        }


        /**
         * @return AttributeType
         */
        private function attributeType( $handle ){
            if( is_null($this->{"at_{$handle}"}) ){
                $attributeType = AttributeType::getByHandle($handle);
                if( is_object($attributeType) ){
                    $this->{"at_{$handle}"} = $attributeType;
                }
            }
            return $this->{"at_{$handle}"};
        }

    }

}