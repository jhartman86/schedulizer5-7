<?php namespace Concrete\Package\Schedulizer\Src {

    use Concrete\Package\Schedulizer\Src\Persistable\Contracts\Persistant;
    use Concrete\Package\Schedulizer\Src\Persistable\Mixins\Crud;

    /**
     * Class SchedulizerEventRepeatNullify
     * @package Concrete\Package\Schedulizer\Src
     */
    class EventRepeatNullify extends Persistant {

        use Crud;

        /** @definition({"cast":"int", "declarable":false}) */
        protected $id;

        /** @definition({"cast":"int"}) */
        protected $eventID;

        /** @definition({"cast":"datetime"}) */
        protected $modifiedUTC;

    }

}