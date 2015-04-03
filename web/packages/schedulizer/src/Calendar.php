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

        /**
         * @var $id int
         * @definition({"cast":"int", "declarable":false})
         */
        protected $id;

        /**
         * @var $createdUTC \DateTime
         * @definition({"cast":"datetime", "declarable":false, "autoSet":["onCreate"]})
         */
        protected $createdUTC;

        /**
         * @var $modifiedUTC \DateTime
         * @definition({"cast":"datetime", "declarable":false, "autoSet":["onCreate","onUpdate"]})
         */
        protected $modifiedUTC;

        /**
         * @var $title string
         * @definition({"cast":"string","nullable":true})
         */
        protected $title;

        /**
         * @var $ownerID int
         * @definition({"cast":"int"})
         */
        protected $ownerID;

        /**
         * @var $defaultTimezone string
         * @definition({"cast":"string"})
         */
        protected $defaultTimezone = 'UTC';

        /** @param $setters */
        public function __construct( $setters = null ){
            $this->mergePropertiesFrom( $setters );
        }

        /** @return string */
        public function __toString(){ return ucwords( $this->title ); }

        /** @return int|null */
        public function getID(){ return $this->id; }

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