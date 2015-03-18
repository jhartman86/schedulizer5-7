<?php namespace Concrete\Package\Schedulizer {
    defined('C5_EXECUTE') or die(_("Access Denied."));

    /** @link https://github.com/concrete5/concrete5-5.7.0/blob/develop/web/concrete/config/app.php#L10-L90 Aliases */
    use Package; /** @see \Concrete\Core\Package\Package */
    use Config; /** @see \Concrete\Core */
    use Loader; /** @see \Concrete\Core\Legacy\Loader */
    use BlockType; /** @see \Concrete\Core\Block\BlockType\BlockType */
    use SinglePage; /** @see \Concrete\Core\Page\Single */
    use \DateTime; /** @see \DateTime */
    use \DateTimeZone; /** @see \DateTimeZone */

    class Controller extends Package {

        // Package handle
        const PACKAGE_HANDLE    = 'schedulizer';
        // Config keys
        const DEFAULT_TIMEZONE  = 'DEFAULT_TIMEZONE';

        protected $pkgHandle                = self::PACKAGE_HANDLE;
        protected $appVersionRequired       = '5.7.3.2';
        protected $pkgVersion               = '0.12';

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

            new Src\Api\OnStart(function( $routes ){
                $routes->add('schedulizer_event_list', new \Symfony\Component\Routing\Route('/_schedulizer/event/list/{calendarID}{trailingSlash}', array(
                    '_controller'   => '\Concrete\Package\Schedulizer\Src\Api\EventListHandler::dispatch',
                    'calendarID'    => null,
                    'trailingSlash' => '/'
                ), array('calendarID' => '\d+|[/]{0,1}', 'trailingSlash' => '[/]{0,1}')));

                $routes->add('schedulizer_calendar', new \Symfony\Component\Routing\Route('/_schedulizer/calendar/{id}{trailingSlash}', array(
                    '_controller'   => '\Concrete\Package\Schedulizer\Src\Api\CalendarHandler::dispatch',
                    'id'            => null,
                    'trailingSlash' => '/'
                ), array('id' => '\d+|[/]{0,1}', 'trailingSlash' => '[/]{0,1}'), array(), '', array(), array('GET','POST','PUT','DELETE')));

                $routes->add('schedulizer_event', new \Symfony\Component\Routing\Route('/_schedulizer/event/{id}{trailingSlash}', array(
                    '_controller'   => '\Concrete\Package\Schedulizer\Src\Api\EventHandler::dispatch',
                    'id'            => null,
                    'trailingSlash' => '/'
                ), array('id' => '\d+|[/]{0,1}', 'trailingSlash' => '[/]{0,1}'), array(), '', array(), array('GET','POST','PUT','DELETE')));

                $routes->add('schedulizer_timezones', new \Symfony\Component\Routing\Route('/_schedulizer/timezones{trailingSlash}', array(
                    '_controller'   => '\Concrete\Package\Schedulizer\Src\Api\TimezonesHandler::getList',
                    'trailingSlash' => '/'
                ), array('trailingSlash' => '[/]{0,1}'), array(), '', array(), array('GET')));
            });
        }


        public function uninstall(){
            parent::uninstall();

            try {
                // delete mysql tables
                $db = Loader::db();
//                $db->Execute("DROP TABLE SchedulizerCalendar");
//                //$db->Execute("DROP TABLE SchedulizerCalendarAttributeValues");
//                $db->Execute("DROP TABLE SchedulizerEvent");
//                $db->Execute("DROP TABLE SchedulizerEventRepeat");
                //$db->Execute("DROP TABLE SchedulizerEventRepeatNullify");
                //$db->Execute("DROP TABLE SchedulizerCalendarSearchIndexAttributes");
            }catch(Exception $e){
                // fail gracefully
            }
        }


        /**
         * Ensure system dependencies are met (specifically, MySQL timezone tables and PHP datetime classes are working
         * correctly).
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
                 ->setupSinglePages();
//            $ci = new ContentImporter();
//            $ci->importContentFile($this->getPackagePath() . '/content.xml');
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
            return $this;
        }


        /**
         * @return Controller
         */
        private function setupSinglePages(){
            // Dashboard pages
            SinglePage::add('/dashboard/schedulizer/', $this->packageObject());
            SinglePage::add('/dashboard/schedulizer/calendars', $this->packageObject());
            SinglePage::add('/dashboard/schedulizer/settings', $this->packageObject());
            // Hidden
            $spManage = SinglePage::add('/dashboard/schedulizer/calendars/manage', $this->packageObject());
            if( is_object($spManage) ){
                $spManage->setAttribute('exclude_nav', 1);
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

    }

}