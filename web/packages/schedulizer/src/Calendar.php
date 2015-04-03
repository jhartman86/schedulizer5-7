<?php namespace Concrete\Package\Schedulizer\Src {

    use DateTimeZone;

    /**
     * Class Calendar
     * @package Concrete\Package\Schedulizer\Src
     * @definition({"table":"SchedulizerCalendar"})
     */
    class Calendar {

        use Persistable\Mixins\Persistable;

        /** @definition({"cast":"integer", "declarable":false}) */
        protected $id;

        /** @definition({"cast":"datetime", "declarable":false, "onCreateValue":"auto"}) */
        protected $createdUTC;

        /** @definition({"cast":"datetime", "declarable":false}) */
        protected $modifiedUTC;

        /** @definition({"cast":"string"}) */
        protected $title;

        /** @definition({"cast":"integer"}) */
        protected $ownerID;

        /** @definition({"cast":"string"}) */
        protected $defaultTimezone = 'UTC';

        protected function onAfterFetch( $record ){
            //$this->modifiedUTC = new \DateTime($record->modifiedUTC, new DateTimeZone('UTC'));
        }

        /**
         * Constructor
         */
        public function __construct( $setters = null ){
            $this->mergePropertiesFrom( $setters );
        }

        /**
         * @return string
         */
        public function __toString(){
            return ucwords( $this->title );
        }

        /**
         * @return string
         */
        public function getTitle(){
            return $this->title;
        }

        /**
         * @return Int
         */
        public function getOwnerID(){
            return $this->ownerID;
        }

        /**
         * @return string
         */
        public function getDefaultTimezone(){
            return $this->defaultTimezone;
        }

        /**
         * @return DateTimeZone
         */
        public function getCalendarTimezoneObj(){
            if( $this->_calendarTimezoneObj === null ){
                $this->_calendarTimezoneObj = new DateTimeZone( $this->getDefaultTimezone() );
            }
            return $this->_calendarTimezoneObj;
        }

        /**
         * @param $id Int
         * @return mixed SchedulizerCalendar|null
         */
//        public static function getByID( $id ){
//            return self::entityManager()->find(__CLASS__, $id);
//        }


        /**
         * Return properties for JSON serialization
         * @return array|mixed
         */
        public function jsonSerialize(){
            if( $this->id === null ){
                $properties = (object) get_object_vars($this);
                unset($properties->createdUTC);
                unset($properties->modifiedUTC);
                unset($properties->id);
                return $properties;
            }
            $properties                 = (object) get_object_vars($this);
            $properties->createdUTC     = $properties->createdUTC->format('c');
            $properties->modifiedUTC    = $properties->modifiedUTC->format('c');
            return $properties;
        }
    }

}