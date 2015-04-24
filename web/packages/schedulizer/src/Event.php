<?php namespace Concrete\Package\Schedulizer\Src {

    use DateTime,
        DateTimeZone,
        \Concrete\Package\Schedulizer\Src\EventTime,
        \Concrete\Package\Schedulizer\Src\Persistable\Contracts\Persistant,
        \Concrete\Package\Schedulizer\Src\Persistable\Mixins\Crud,
        \Concrete\Package\Schedulizer\Src\Attribute\Mixins\AttributableEntity;

    /**
     * @package Concrete\Package\Schedulizer\Src
     * @definition({"table":"SchedulizerEvent"})
     */
    class Event extends Persistant {

        use Crud, AttributableEntity;

        const USE_CALENDAR_TIMEZONE_TRUE    = true,
              USE_CALENDAR_TIMEZONE_FALSE   = false,
              DEFAULT_TIMEZONE              = 'UTC',
              EVENT_COLOR_DEFAULT           = '#E1E1E1';

        // Required for AttributableEntity trait
        const ATTR_KEY_CLASS    = '\Concrete\Package\Schedulizer\Src\Attribute\Key\SchedulizerEventKey',
              ATTR_VALUE_CLASS  = '\Concrete\Package\Schedulizer\Src\Attribute\Value\SchedulizerEventValue';

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

        /** @return array Get all associated event times */
        public function getEventTimes(){
            return (array) EventTime::fetchAllByEventID($this->id);
        }

        /** @return array Get all associated tags */
        public function getEventTags(){
            return (array) EventTag::fetchTagsByEventID($this->id);
        }

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
            $properties->_timeEntities  = $this->getEventTimes();
            $properties->_tags          = $this->getEventTags();
            return $properties;
        }

        /**
         * Callback from the Persistable stuff, executed before entity gets
         * removed entirely. We use this to clear out any attribute stuff.
         */
        protected function onBeforeDelete(){
            $id = $this->id;
            // Delete from primary attribute values table
            self::adhocQuery(function(\PDO $connection) use ($id){
                $statement = $connection->prepare("DELETE FROM SchedulizerEventAttributeValues WHERE eventID=:eventID");
                $statement->bindValue(':eventID', $id);
                return $statement;
            });
            // Delete from search indexed table
            self::adhocQuery(function(\PDO $connection) use ($id){
                $statement = $connection->prepare("DELETE FROM SchedulizerEventSearchIndexAttributes WHERE eventID=:eventID");
                $statement->bindValue(':eventID', $id);
                return $statement;
            });
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