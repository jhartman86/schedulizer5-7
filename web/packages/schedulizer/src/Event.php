<?php namespace Concrete\Package\Schedulizer\Src {

    use DateTime,
        DateTimeZone,
        Concrete\Package\Schedulizer\Src\EventTime,
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

//        public static function createWithEventTimes( $postData ){
//            $eventObj = self::create($postData);
//            if( is_array($postData->_timeEntities) && !empty($postData->_timeEntities) ){
//                foreach($postData->_timeEntities AS $timeEntityData){
//                    $timeEntityData->eventID = $eventObj->getID();
//                    EventTime::createWithWeeklyRepeatSettings($timeEntityData);
//                }
//            }
//            return $eventObj;
//        }
//
//        public function updateWithEventTimes( $postData ){
//            $this->update($postData);
//            if( is_array($postData->_timeEntities) && !empty($postData->_timeEntities) ){
//                EventTime::purgeAllByEventID($this->getID());
//                foreach($postData->_timeEntities AS $timeEntityData){
//                    $timeEntityData->eventID = $this->getID();
//                    EventTime::createWithWeeklyRepeatSettings($timeEntityData);
//                }
//            }
//        }

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
            $properties->_tags          = (array) EventTag::fetchTagsByEventID($this->id);
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


        /**
         * Gets full data for an event; (includes serializing _timeEntity sub-resources).
         * @param $calendarID
         * @return $this|void
         */
        public static function fetchAllByCalendarID( $calendarID ){
            return self::fetchMultipleBy(function( \PDO $connection, $tableName ) use ($calendarID){
                $statement = $connection->prepare("SELECT * FROM {$tableName} WHERE calendarID=:calendarID");
                $statement->bindValue(':calendarID', $calendarID);
                return $statement;
            });
        }

        /**
         * Return a SIMPLE list of the events (ie. just the records) associated with a calendar.
         * This returns straight table results as opposed to the above where it will return a
         * list that gets serialized via jsonSerializable on all the instaniated event objects.
         * @param $calendarID
         * @return $this|void
         */
        public static function fetchSimpleByCalendarID( $calendarID ){
            /** @var $executedStatement \PDOStatement */
            $executedStatement = self::adhocQuery(function( \PDO $connection, $tableName ) use ($calendarID){
                $statement = $connection->prepare("SELECT * FROM {$tableName} WHERE calendarID=:calendarID");
                $statement->bindValue(':calendarID', $calendarID);
                return $statement;
            });
            return $executedStatement->fetchAll(\PDO::FETCH_OBJ);
        }

    }

}