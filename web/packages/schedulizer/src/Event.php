<?php namespace Concrete\Package\Schedulizer\Src {

    use DateTime,
        DateTimeZone,
        Concrete\Package\Schedulizer\Src\Persistable\Contracts\Persistant,
        Concrete\Package\Schedulizer\Src\Persistable\Mixins\Crud;

    /**
     * @package Concrete\Package\Schedulizer\Src
     * @definition({"table":"SchedulizerEvent"})
     */
    class Event extends Persistant {

        use Crud;

        const USE_CALENDAR_TIMEZONE_TRUE    = true,
              USE_CALENDAR_TIMEZONE_FALSE   = false,
              DEFAULT_TIMEZONE              = 'UTC',
              EVENT_COLOR_DEFAULT           = '#E1E1E1';

        /** @definition({"cast":"datetime", "declarable":false, "autoSet":["onCreate"]}) */
        protected $createdUTC;

        /** @definition({"cast":"datetime", "declarable":false, "autoSet":["onCreate","onUpdate"]}) */
        protected $modifiedUTC;

        /** @definition({"cast":"int"}) */
        protected $calendarID;

        /** @definition({"cast":"string","nullable":true}) */
        protected $title;

        /** @definition({"cast":"string","nullable":true}) */
        protected $description;

        /** @definition({"cast":"bool","nullable":false}) */
        protected $useCalendarTimezone = self::USE_CALENDAR_TIMEZONE_TRUE;

        /** @definition({"cast":"string","nullable":false}) */
        protected $timezoneName = self::DEFAULT_TIMEZONE;

        /** @definition({"cast":"string","nullable":true}) */
        protected $eventColor = self::EVENT_COLOR_DEFAULT;

        /** @definition({"cast":"int","nullable":false}) */
        protected $ownerID;

        /** @definition({"cast":"int","nullable":true}) */
        protected $fileID;

        /**
         * @param $setters
         */
        public function __construct( $setters = null ){
            $this->mergePropertiesFrom($setters);
        }

        /** @return string */
        public function __toString(){ return ucwords( $this->title ); }

        /** @return int|null */
        public function getCalendarID(){ return $this->calendarID; }

        /** @return DateTime|null */
        public function getModifiedUTC(){ return $this->modifiedUTC; }

        /** @return DateTime|null */
        public function getCreatedUTC(){ return $this->createdUTC; }

        /** @return string|null */
        public function getTitle(){ return $this->title; }

        /** @return string|null */
        public function getDescription(){ return $this->description; }

        /** @return bool|null */
        public function getUseCalendarTimezone(){ return $this->useCalendarTimezone; }

        /** @return string|null */
        public function getTimezoneName(){ return $this->timezoneName; }

        /** @return string|null */
        public function getEventColor(){ return $this->eventColor; }

        /** @return Int */
        public function getOwnerID(){ return $this->ownerID; }

        /** @return int|null */
        public function getFileID(){ return $this->fileID; }

        /**
         * Return properties for JSON serialization
         * @return array|mixed
         */
        public function jsonSerialize(){
            if( ! $this->isPersisted() ){
                $properties = (object) get_object_vars($this);
                unset($properties->id);
                return $properties;
            }
            $properties                 = (object) get_object_vars($this);
            $properties->_timeEntities  = (array) EventTime::fetchAllByEventID($this->id);
//            $properties->startUTC       = $properties->startUTC->format('c');
//            $properties->endUTC         = $properties->endUTC->format('c');
//            $properties->repeatEndUTC   = $properties->repeatEndUTC->format('c');
//            $properties->createdUTC     = $properties->createdUTC->format('c');
//            $properties->modifiedUTC    = $properties->modifiedUTC->format('c');
//            $properties->_repeaters     = (array) EventRepeat::getAllByEventID($this->id);
            return $properties;
        }

        /****************************************************************
         * Fetch Methods
         ***************************************************************/

        /**
         * @param $title
         * @return array|null [$this, $this]
         */
        public static function fetchAllByTitle( $title ){
            return self::fetchMultipleBy(function( \PDO $connection, $tableName ) use ($title){
                $statement = $connection->prepare("SELECT * FROM {$tableName} WHERE title LIKE :title");
                $statement->bindValue(':title', "%$title%");
                return $statement;
            });
        }

        /**
         * @param $ownerID
         * @return array|null [$this, $this]
         */
        public static function fetchAllByOwnerID( $ownerID ){
            return self::fetchMultipleBy(function( \PDO $connection, $tableName ) use ($ownerID){
                $statement = $connection->prepare("SELECT * FROM {$tableName} WHERE ownerID=:ownerID");
                $statement->bindValue(':ownerID', $ownerID);
                return $statement;
            });
        }

    }

}