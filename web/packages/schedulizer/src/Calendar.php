<?php namespace Concrete\Package\Schedulizer\Src {

    use DateTimeZone;
    use Concrete\Package\Schedulizer\Src\Persistable\Contracts\Persistant;
    use Concrete\Package\Schedulizer\Src\Persistable\Mixins\Crud;

    /**
     * Class Calendar
     * @package Concrete\Package\Schedulizer\Src
     * @definition({"table":"SchedulizerCalendar"})
     */
    class Calendar extends Persistant {

        use Crud;

        /** @definition({"cast":"datetime", "declarable":false, "autoSet":["onCreate"]}) */
        protected $createdUTC;

        /** @definition({"cast":"datetime", "declarable":false, "autoSet":["onCreate","onUpdate"]}) */
        protected $modifiedUTC;

        /** @definition({"cast":"string","nullable":true}) */
        protected $title;

        /** @definition({"cast":"int"}) */
        protected $ownerID;

        /** @definition({"cast":"string"}) */
        protected $defaultTimezone = 'UTC';

        /** @param $setters */
        public function __construct( $setters = null ){
            $this->mergePropertiesFrom( $setters );
        }

        /** @return string */
        public function __toString(){ return ucwords( $this->title ); }

        /** @return DateTime|null */
        public function getModifiedUTC(){ return $this->modifiedUTC; }

        /** @return DateTime|null */
        public function getCreatedUTC(){ return $this->createdUTC; }

        /** @return string|null */
        public function getTitle(){ return $this->title; }

        /** @return int|null */
        public function getOwnerID(){ return $this->ownerID; }

        /** @return string */
        public function getDefaultTimezone(){ return $this->defaultTimezone; }

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
         * Return properties for JSON serialization
         * @return array|mixed
         */
        public function jsonSerialize(){
            if( ! $this->isPersisted() ){
                $properties = (object) get_object_vars($this);
                unset($properties->id);
                unset($properties->createdUTC);
                unset($properties->modifiedUTC);
                return $properties;
            }
            $properties                 = (object) get_object_vars($this);
            $properties->createdUTC     = $properties->createdUTC->format('c');
            $properties->modifiedUTC    = $properties->modifiedUTC->format('c');
            return $properties;
        }

        /****************************************************************
         * Fetch Methods
         ***************************************************************/

        public static function fetchAll(){
            return (array) self::fetchMultipleBy(function( \PDO $connection, $tableName ){
                return $connection->prepare("SELECT * FROM {$tableName}");
            });
        }
    }

}