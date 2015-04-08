<?php namespace Concrete\Package\Schedulizer\Src {

    use DateTime;
    use DateTimeZone;
    use Concrete\Package\Schedulizer\Src\Persistable\Contracts\Persistant;
    use Concrete\Package\Schedulizer\Src\Persistable\Mixins\Crud;

    /**
     * @package Concrete\Package\Schedulizer\Src
     * @definition({"table":"SchedulizerEventTime"})
     */
    class EventTime extends Persistant {

        use Crud;

                // open ended?
        const   IS_OPEN_ENDED_TRUE              = true,
                IS_OPEN_ENDED_FALSE             = false,
                // all day booleans
                IS_ALL_DAY_TRUE                 = true,
                IS_ALL_DAY_FALSE                = false,
                // is recurring booleans
                IS_REPEATING_TRUE               = true,
                IS_REPEATING_FALSE              = false,
                // indefinite?
                REPEAT_INDEFINITE_TRUE          = true,
                REPEAT_INDEFINITE_FALSE         = false,
                // repeat monthly (specific day or "3rd {monday}"
                REPEAT_MONTHLY_METHOD_SPECIFIC  = 'specific',
                REPEAT_MONTHLY_METHOD_ORDINAL   = 'ordinal',
                // frequency handle
                REPEAT_TYPE_HANDLE_DAILY        = 'daily',
                REPEAT_TYPE_HANDLE_WEEKLY       = 'weekly',
                REPEAT_TYPE_HANDLE_MONTHLY      = 'monthly',
                REPEAT_TYPE_HANDLE_YEARLY       = 'yearly',
                // is it an alias?
                SYNTHETIC                       = true,
                NOT_SYNTHETIC                   = false;

        /** @definition({"cast":"int"}) */
        protected $eventID;

        /** @definition({"cast":"datetime","nullable":false}) */
        protected $startUTC;

        /** @definition({"cast":"datetime","nullable":false}) */
        protected $endUTC;

        /** @definition({"cast":"bool","nullable":true}) */
        protected $isOpenEnded = self::IS_OPEN_ENDED_FALSE;

        /** @definition({"cast":"bool","nullable":true}) */
        protected $isAllDay = self::IS_ALL_DAY_FALSE;

        /** @definition({"cast":"bool","nullable":false}) */
        protected $isRepeating = self::IS_REPEATING_FALSE;

        /** @definition({"cast":"string","nullable":true}) */
        protected $repeatTypeHandle = null; //self::REPEAT_TYPE_HANDLE_DAILY;

        /** @definition({"cast":"int","nullable":true}) */
        protected $repeatEvery = null;

        /** @definition({"cast":"bool","nullable":true}) */
        protected $repeatIndefinite = null; //self::REPEAT_INDEFINITE_TRUE;

        /** @definition({"cast":"datetime","nullable":true}) */
        protected $repeatEndUTC = null;

        /** @definition({"cast":"string","nullable":true}) */
        protected $repeatMonthlyMethod = null; //self::REPEAT_MONTHLY_METHOD_SPECIFIC;

        /** @definition({"cast":"int","nullable":true}) */
        protected $repeatMonthlySpecificDay = null;

        /** @definition({"cast":"int","nullable":true}) */
        protected $repeatMonthlyOrdinalWeek = null;

        /** @definition({"cast":"int","nullable":true}) */
        protected $repeatMonthlyOrdinalWeekday = null;

        /** @definition({"cast":"string","declarable":false}) */
        protected $weeklyDays;

        /**
         * Sets up array collections for relationships.
         * Constructor
         * @param $setters
         */
        public function __construct( $setters = null ){
            $this->mergePropertiesFrom($setters);
        }

        /**
         * Execute before persisting to the database.
         * @throws \Exception
         */
        protected function onBeforePersist(){
            // If no calendar ID is set, throw an exception
//            if( $this->calendarID === null ){
//                throw new \Exception('Event cannot be saved without a CalendarID');
//            }
//            // Set startUTC if not already set
//            if( ! $this->startUTC ){
//                $this->startUTC = new DateTime($this->startUTC, new DateTimeZone('UTC'));
//            }
//            // Set endUTC if not already set
//            if( ! $this->endUTC ){
//                $this->endUTC = new DateTime($this->endUTC, new DateTimeZone('UTC'));
//            }
//            // Set repeatEndUTC if not already set
//            if( ! $this->repeatEndUTC ){
//                $this->repeatEndUTC = new DateTime($this->repeatEndUTC, new DateTimeZone('UTC'));
//            }
//            // If event should inherit calendar timezone. Note, when trying to fetch the calendar,
//            // if the calendarID is invalid (calendar record doesn't exist), an exception will
//            // implicitly be thrown.
//            if( $this->useCalendarTimezone === self::USE_CALENDAR_TIMEZONE_TRUE ){
//                $this->timezoneName = $this->getCalendar()->getDefaultTimezone();
//            }
        }

//        /** @return DateTime|null */
//        public function getModifiedUTC(){ return $this->modifiedUTC; }
//
//        /** @return DateTime|null */
//        public function getCreatedUTC(){ return $this->createdUTC; }



//        /**
//         * @return Calendar
//         * @throws \Exception
//         */
//        public function getCalendar(){
//            if( $this->_calendar === null ){
//                $this->_calendar = Calendar::getByID($this->calendarID);
//                if( ! $this->_calendar ){
//                    throw new \Exception('Calendar associated with Event does not exist');
//                }
//            }
//            return $this->_calendar;
//        }

        /** @return int|null */
        public function getEventID(){ return $this->eventID; }

        /** @return DateTime|null */
        public function getStartUTC(){ return $this->startUTC; }

        /** @return DateTime|null */
        public function getEndUTC(){ return $this->endUTC; }

        /** @return bool */
        public function getIsOpenEnded(){ return $this->isOpenEnded; }

        /** @return bool|null */
        public function getIsAllDay(){ return $this->isAllDay; }

        /** @return bool|null */
        public function getIsRepeating(){ return $this->isRepeating; }

        /** @return string|null */
        public function getRepeatTypeHandle(){ return $this->repeatTypeHandle; }

        /** @return int|null */
        public function getRepeatEvery(){ return $this->repeatEvery; }

        /** @return bool|null */
        public function getRepeatIndefinite(){ return $this->repeatIndefinite; }

        /** @return DateTime|null */
        public function getRepeatEndUTC(){ return $this->repeatEndUTC; }

        /** @return string */
        public function getRepeatMonthlyMethod(){ return $this->repeatMonthlyMethod; }

        /** @return int|null */
        public function getRepeatMonthlySpecificDay(){ return $this->repeatMonthlySpecificDay; }

        /** @return int|null */
        public function getRepeatMonthlyOrdinalWeek(){ return $this->repeatMonthlyOrdinalWeek; }

        /** @return int|null */
        public function getRepeatMonthlyOrdinalWeekday(){ return $this->repeatMonthlyOrdinalWeekday; }


        /**
         * Convenience method for creating/updating events when updating via the API
         * and passing repeat settings...
         * @param $repeatSettings
         * @return void
         */
        public function setRepeaters( $repeatSettings = null ){
//            EventTimeRepeat::purgeAllByEventTimeID($this->id);
//
//            if( $this->isRepeating ){
//                switch( $this->repeatTypeHandle ){
//                    // Repeat daily or yearly...
//                    case self::REPEAT_TYPE_HANDLE_DAILY:
//                    case self::REPEAT_TYPE_HANDLE_YEARLY:
//                        EventTimeRepeat::create(array('eventTimeID' => $this->id));
//                        break;
//
//                    // Repeat weekly...
//                    case self::REPEAT_TYPE_HANDLE_WEEKLY:
//                        if( is_object($repeatSettings) && (is_array($repeatSettings->weekdayIndices) && !empty($repeatSettings->weekdayIndices)) ){
//                            foreach($repeatSettings->weekdayIndices AS $weekdayIndex){
//                                EventTimeRepeat::create(array('eventTimeID' => $this->id, 'repeatWeekday' => $weekdayIndex));
//                            }
//                        }
//                        break;
//
//                    // Repeat monthly...
//                    case self::REPEAT_TYPE_HANDLE_MONTHLY:
//                        // If its repeating only on a specific date(eg. "21st" of every month)
//                        if( $this->repeatMonthlyMethod === self::REPEAT_MONTHLY_METHOD_SPECIFIC ){
//                            EventTimeRepeat::create(array('eventTimeID' => $this->id, 'repeatDay' => $repeatSettings->monthlySpecificDay));
//                        }
//                        // Its repeating on an abstract (eg. "Second Thursday" of every month)
//                        if( $this->repeatMonthlyMethod === self::REPEAT_MONTHLY_METHOD_ORDINAL ){
//                            EventTimeRepeat::create(array('eventTimeID' => $this->id, 'repeatWeek' => $repeatSettings->monthlyDynamicWeek, 'repeatWeekday' => $repeatSettings->monthlyDynamicWeekday));
//                        }
//                        break;
//                }
//            }
        }


        /**
         * Pass in event time data, and if it has weeklyDays set as an array, this'll create it.
         * @param $data
         * @return $this
         */
        public static function createWithWeeklyRepeatSettings( $data ){
            if( is_array($data->weeklyDays) && !empty($data->weeklyDays) ){
                $eventTimeObj = self::create($data);
                foreach($data->weeklyDays AS $weekdayValue){
                    self::adhocQuery(function(\PDO $connection) use ($eventTimeObj, $weekdayValue){
                        $statement = $connection->prepare("INSERT INTO SchedulizerEventTimeWeekdays (eventTimeID, repeatWeeklyDay) VALUES (:eventTimeID,:repeatWeeklyDay)");
                        $statement->bindValue(':eventTimeID', $eventTimeObj->getID());
                        $statement->bindValue(':repeatWeeklyDay', (int)$weekdayValue);
                        return $statement;
                    });
                }
                return $eventTimeObj;
            }
            // No weekly repeat settings, just create and return as normal
            return self::create($data);
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
            $properties                  = (object) get_object_vars($this);
            $properties->startUTC        = $properties->startUTC->format('c');
            $properties->endUTC          = $properties->endUTC->format('c');
            $properties->repeatEndUTC    = !is_null($properties->repeatEndUTC) ? $properties->repeatEndUTC->format('c') : null;
            $properties->weeklyDays      = is_null($properties->weeklyDays) ? array() :
                // All this does is cast the exploded values from strings to integers
                array_map(function($day){ return (int)$day; }, explode(',',$properties->weeklyDays));
            return $properties;
        }


        /****************************************************************
         * Fetch Methods
         ***************************************************************/

        /**
         * Delete all by eventID
         * @param $eventID
         */
        public static function purgeAllByEventID( $eventID ){
            self::adhocQuery(function(\PDO $connection, $tableName) use ($eventID){
                $statement = $connection->prepare("DELETE FROM {$tableName} WHERE eventID=:eventID");
                $statement->bindValue(':eventID', $eventID);
                return $statement;
            });
        }

        /**
         * Get an instance by ID, AND join weeklyDays as concat'd column (2,5,7)
         * @param $id
         * @return $this|void
         */
        public static function getByID( $id ){
            return self::fetchOneBy(function(\PDO $connection, $tableName) use ($id){
                $statement = $connection->prepare("SELECT tblEt.*, tblEtw.weeklyDays FROM {$tableName} tblEt
                LEFT JOIN (
                  SELECT _wd.eventTimeID, GROUP_CONCAT(repeatWeeklyDay SEPARATOR ',') AS weeklyDays FROM
                  SchedulizerEventTimeWeekdays _wd GROUP BY _wd.eventTimeID
                ) AS tblEtw ON tblEtw.eventTimeID = tblEt.id
                WHERE tblEt.id=:id");
                $statement->bindValue(':id', $id);
                return $statement;
            });
        }

        /**
         * @param $eventID
         * @return array|null [$this, $this]
         */
        public static function fetchAllByEventID( $eventID ){
            return self::fetchMultipleBy(function( \PDO $connection, $tableName ) use ($eventID){
                $statement = $connection->prepare("SELECT tblEt.*, tblEtw.weeklyDays FROM {$tableName} tblEt
                LEFT JOIN (
                  SELECT _wd.eventTimeID, GROUP_CONCAT(repeatWeeklyDay SEPARATOR ',') AS weeklyDays FROM
                  SchedulizerEventTimeWeekdays _wd GROUP BY _wd.eventTimeID
                ) AS tblEtw ON tblEtw.eventTimeID = tblEt.id
                WHERE tblEt.eventID=:eventID");
                $statement->bindValue(':eventID', $eventID);
                return $statement;
            });
        }
    }

}