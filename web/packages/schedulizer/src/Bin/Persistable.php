<?php namespace Concrete\Package\Schedulizer\Src\Bin {

    /**
     * @package Concrete\Package\Schedulizer\Src\Bin
     */
    interface InterfacePersistable {
        public static function create( $data );
        public function update( $data );
        public function delete();
        public function save();
    }

    /**
     * Class Persistable
     * @package Concrete\Package\Schedulizer\Src\Abstracts
     */
    abstract class Persistable implements InterfacePersistable, \JsonSerializable {

        const PACKAGE_HANDLE    = 'schedulizer';
        const TIMESTAMP_FORMAT  = 'Y-m-d H:i:s';
        const DEFAULT_TIMEZONE  = 'UTC';

    }

}