<?php namespace Application\StartingPointPackage\Virgin {

    use Loader;
    use Package;
    use Config;
    use Concrete\Core\Updater\Migrations\Configuration;

    class Controller extends \Concrete\Core\Package\StartingPointPackage {

        protected $pkgHandle = 'virgin';
        public function getPackageName(){ return t($this->pkgHandle); }
        public function getPackageDescription(){ return t($this->pkgHandle); }


        /**
         * Override parent method
         * @note: we're removing generating of doctrine proxy classes within the try {} method
         * below (see parent for whats removed); generation is done in the after_exec hook of
         * pagodabox install while directories are still writable
         */
        public function install_database(){
            // Already configured? Bail out...
            $db = Loader::db();
            if( count($db->GetCol("SHOW TABLES")) > 0 ){
                fwrite(STDERR, "\nDatabase already installed; leaving existing installation untouched and moving on...\n\n");
                exit(0);
            }

            try {
                Package::installDB(DIR_BASE_CORE . '/config/db.xml');
                $this->indexAdditionalDatabaseFields();
                $configuration = new Configuration();
                $version = $configuration->getVersion(Config::get('concrete.version_db'));
                $version->markMigrated();
            }catch(\Exception $e){
                fwrite(STDERR, "\nUnable to install database: ". $db->ErrorMsg() ."\n\n");
                exit(0);
            }
        }


        /**
         * Removes all calls to writable things in the finish method and just
         * clears the cache
         */
        public function finish(){
            $config = \Core::make('config');
            $config->clearCache();
            \Core::make('cache')->flush();
        }

    }

}