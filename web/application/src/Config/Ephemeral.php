<?php namespace Application\Src\Config {

    use \Concrete\Core\Config\Repository\Repository as ConfigRepository;
    use \Concrete\Core\Config\FileLoader;
    use \Illuminate\Filesystem\Filesystem;

    class Ephemeral implements \Concrete\Core\Config\SaverInterface {
        public function save($item, $value, $environment, $group, $namespace = null){}

        /**
         * From bootstrap/start.php, pass in an instance of $app to this method to
         * override C5's default method of persisting config settings to flat file
         * storage.
         *
         * @param \Concrete\Core\Application\Application $app
         */
        public static function bindToApp( \Concrete\Core\Application\Application $app ){
            $fileSystem     = new FileSystem();
            $configLoader   = new FileLoader($fileSystem);
            $configSaver    = new self();
            $app->instance('config', new ConfigRepository($configLoader, $configSaver, $app->environment()));
        }
    }

}