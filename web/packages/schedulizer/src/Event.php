<?php namespace Concrete\Package\Schedulizer\Src {

    use DateTime;
    use DateTimeZone;
    use Concrete\Package\Schedulizer\Src\Persistable\Contracts\Persistant;
    use Concrete\Package\Schedulizer\Src\Persistable\Mixins\Crud;

    /**
     * @package Concrete\Package\Schedulizer\Src
     * @definition({"table":"SchedulizerEvent"})
     */
    class Event extends Persistant {

        use Crud;

                // timezone overrides
        const   USE_CALENDAR_TIMEZONE_TRUE      = true,
                USE_CALENDAR_TIMEZONE_FALSE     = false,
                // open ended?
                OPEN_ENDED_TRUE                 = true,
                OPEN_ENDED_FALSE                = false,
                // all day booleans
                ALL_DAY_TRUE                    = true,
                ALL_DAY_FALSE                   = false,
                // is recurring booleans
                IS_REPEATING_TRUE               = true,
                IS_REPEATING_FALSE              = false,
                // indefinite?
                REPEAT_INDEFINITE_TRUE          = true,
                REPEAT_INDEFINITE_FALSE         = false,
                // repeat monthly (specific day or "3rd {monday}"
                REPEAT_MONTHLY_METHOD_SPECIFIC  = true,
                REPEAT_MONTHLY_METHOD_ORDINAL   = false,
                // frequency handle
                REPEAT_TYPE_HANDLE_DAILY        = 'daily',
                REPEAT_TYPE_HANDLE_WEEKLY       = 'weekly',
                REPEAT_TYPE_HANDLE_MONTHLY      = 'monthly',
                REPEAT_TYPE_HANDLE_YEARLY       = 'yearly',
                // event color default
                EVENT_COLOR_DEFAULT             = '#E1E1E1',
                // alias? (only used when editing recurring events that are not the original)
                IS_ALIAS_TRUE                   = true,
                IS_ALIAS_FALSE                  = false;

        /** @definition({"cast":"int", "declarable":false}) */
        protected $id;

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

        /** @definition({"cast":"datetime","nullable":false}) */
        protected $startUTC;

        /** @definition({"cast":"datetime","nullable":false}) */
        protected $endUTC;

        /** @definition({"cast":"bool","nullable":true}) */
        protected $isOpenEnded = self::OPEN_ENDED_FALSE;

        /** @definition({"cast":"bool","nullable":true}) */
        protected $isAllDay = self::ALL_DAY_FALSE;

        /** @definition({"cast":"bool","nullable":false}) */
        protected $useCalendarTimezone = self::USE_CALENDAR_TIMEZONE_TRUE;

        /** @definition({"cast":"string","nullable":false}) */
        protected $timezoneName = 'UTC';

        /** @definition({"cast":"string","nullable":true}) */
        protected $eventColor = self::EVENT_COLOR_DEFAULT;

        /** @definition({"cast":"bool","nullable":false}) */
        protected $isRepeating = self::IS_REPEATING_FALSE;

        /** @definition({"cast":"string","nullable":true}) */
        protected $repeatTypeHandle;

        /** @definition({"cast":"int","nullable":true}) */
        protected $repeatEvery;

        /** @definition({"cast":"bool","nullable":false}) */
        protected $repeatIndefinite = self::REPEAT_INDEFINITE_FALSE;

        /** @definition({"cast":"datetime","nullable":false}) */
        protected $repeatEndUTC;

        /** @definition({"cast":"bool","nullable":true}) */
        protected $repeatMonthlyMethod;

        /** @definition({"cast":"int","nullable":false}) */
        protected $ownerID;

        /** @definition({"cast":"int","nullable":true}) */
        protected $fileID;

        /**
         * Sets up array collections for relationships.
         * Constructor
         * @param $setters
         */
        public function __construct( $setters = null ){
            $this->mergePropertiesFrom($setters);
        }

        protected function onBeforePersist(){
            if( !($this->startUTC instanceof DateTime) ){
                $this->startUTC = new DateTime($this->startUTC, new DateTimeZone('UTC'));
            }
            if( !($this->endUTC instanceof DateTime) ){
                $this->endUTC = new DateTime($this->endUTC, new DateTimeZone('UTC'));
            }
            if( !($this->repeatEndUTC instanceof DateTime) ){
                $this->repeatEndUTC = new DateTime($this->repeatEndUTC, new DateTimeZone('UTC'));
            }
//            if( $this->useCalendarTimezone === self::USE_CALENDAR_TIMEZONE_TRUE ){
//                if( ! $this->calendarInstance ){
//                    throw new \Exception('Events require a Calendar association');
//                }
//                $this->timezoneName = $this->calendarInstance->getDefaultTimezone();
//            }
        }

        /**
         * @return string
         */
        public function __toString(){
            return ucwords( $this->title );
        }

        /**
         * @return int|null
         */
        public function getCalendarID(){
            return $this->calendarID;
        }

        /**
         * @return Calendar
         */
        public function getCalendar(){
            return $this->calendarInstance;
        }

        /**
         * @return string
         */
        public function getTitle(){
            return $this->title;
        }

        /**
         * @return string|null
         */
        public function getDescription(){
            return $this->description;
        }

        /**
         * @return DateTime|null
         */
        public function getStartUTC(){
            return $this->startUTC;
        }

        /**
         * @return DateTime|null
         */
        public function getEndUTC(){
            return $this->endUTC;
        }

        /**
         * @return bool
         */
        public function getIsOpenEnded(){
            return $this->isOpenEnded;
        }

        /**
         * @return bool|null
         */
        public function getIsAllDay(){
            return $this->isAllDay;
        }

        /**
         * @return bool|null
         */
        public function getUseCalendarTimezone(){
            return $this->useCalendarTimezone;
        }

        /**
         * @return string|null
         */
        public function getTimezoneName(){
            return $this->timezoneName;
        }

        /**
         * @return string|null
         */
        public function getEventColor(){
            return $this->eventColor;
        }

        /**
         * @return bool|null
         */
        public function getIsRepeating(){
            return $this->isRepeating;
        }

        /**
         * @return string|null
         */
        public function getRepeatTypeHandle(){
            return $this->repeatTypeHandle;
        }

        /**
         * @return int|null
         */
        public function getRepeatEvery(){
            return $this->repeatEvery;
        }

        /**
         * @return bool|null
         */
        public function getRepeatIndefinite(){
            return $this->repeatIndefinite;
        }

        /**
         * @return DateTime|null
         */
        public function getRepeatEndUTC(){
            return $this->repeatEndUTC;
        }

        /**
         * @return int|null
         */
        public function getRepeatMonthlyMethod(){
            return $this->repeatMonthlyMethod;
        }

        /**
         * @return Int
         */
        public function getOwnerID(){
            return $this->ownerID;
        }

        /**
         * @return int|null
         */
        public function getFileID(){
            return $this->fileID;
        }


        /**
         * Return properties for JSON serialization
         * @return array|mixed
         */
        public function jsonSerialize(){
            $properties                 = (object) get_object_vars($this);
            $properties->startUTC       = $properties->startUTC->format('c');
            $properties->endUTC         = $properties->endUTC->format('c');
            $properties->repeatEndUTC   = $properties->repeatEndUTC->format('c');
            $properties->createdUTC     = $properties->createdUTC->format('c');
            $properties->modifiedUTC    = $properties->modifiedUTC->format('c');
            return $properties;
        }


        /****************************************************************
         * Fetch Methods
         ***************************************************************/

        /**
         * @param $calendarID
         * @return array|null [$this, $this]
         */
        public static function fetchAllByCalendarID( $calendarID ){
            return self::fetchMultipleBy(function( \PDO $connection, $tableName ) use ($calendarID){
                $statement = $connection->prepare("SELECT * FROM {$tableName} WHERE calendarID=:calendarID");
                $statement->bindValue(':calendarID', $calendarID);
                return $statement;
            });
        }

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