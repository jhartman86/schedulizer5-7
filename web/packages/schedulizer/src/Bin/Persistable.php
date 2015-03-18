<?php namespace Concrete\Package\Schedulizer\Src\Bin {

    date_default_timezone_set('UTC');

    /**
     * Class Base
     * @package Concrete\Package\Schedulizer\Src\Abstracts
     */
    abstract class Persistable implements InterfacePersistable, \JsonSerializable {

        const PACKAGE_HANDLE    = 'schedulizer';
        const TIMESTAMP_FORMAT  = 'Y-m-d H:i:s';
        const DEFAULT_TIMEZONE  = 'UTC';
        const STATUS_ACTIVE     = 1;
        const STATUS_INACTIVE   = 0;

    }

}