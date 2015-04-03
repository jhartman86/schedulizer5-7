<?php namespace Concrete\Package\Schedulizer\Src\Bin {

    use \Doctrine\Common\NotifyPropertyChanged;

    /**
     * Class Base
     * @package Concrete\Package\Schedulizer\Src\Abstracts
     */
    abstract class Persistable implements InterfacePersistable, NotifyPropertyChanged, \JsonSerializable {

        const PACKAGE_HANDLE    = 'schedulizer';
        const TIMESTAMP_FORMAT  = 'Y-m-d H:i:s';
        const DEFAULT_TIMEZONE  = 'UTC';
        const STATUS_ACTIVE     = 1;
        const STATUS_INACTIVE   = 0;

    }

}