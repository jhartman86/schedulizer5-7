<?php namespace Concrete\Package\Schedulizer\Src {

    use DateTime;
    use DateTimeZone;
    use Concrete\Package\Schedulizer\Src\EventRepeat;

    /**
     * Class Calendar
     * @package Concrete\Package\Schedulizer\Src
     * @Entity
     * @Table(name="SchedulizerEvent")
     * @HasLifecycleCallbacks
     */
    class Event extends Bin\Persistable {

        use Bin\Traits\Persistable, Bin\Traits\Unique;

                // timezone overrides
        const   USE_CALENDAR_TIMEZONE_TRUE      = 1,
                USE_CALENDAR_TIMEZONE_FALSE     = 0,
                // all day booleans
                ALL_DAY_TRUE                    = 1,
                ALL_DAY_FALSE                   = 0,
                // is recurring booleans
                IS_REPEATING_TRUE               = 1,
                IS_REPEATING_FALSE              = 0,
                // indefinite?
                REPEAT_INDEFINITE_TRUE          = 1,
                REPEAT_INDEFINITE_FALSE         = 0,
                // repeat monthly (specific day or "3rd {monday}"
                REPEAT_MONTHLY_SPECIFIC_DATE    = 1,
                REPEAT_MONTHLY_WEEK_AND_DAY     = 0,
                // frequency handle
                REPEAT_TYPE_HANDLE_DAILY        = 'daily',
                REPEAT_TYPE_HANDLE_WEEKLY       = 'weekly',
                REPEAT_TYPE_HANDLE_MONTHLY      = 'monthly',
                REPEAT_TYPE_HANDLE_YEARLY       = 'yearly',
                // alias? (only used when editing recurring events that are not the original)
                IS_ALIAS_TRUE                   = 1,
                IS_ALIAS_FALSE                  = 0;

        /**
         * @Column(type="integer")
         */
        protected $calendarID;

        /**
         * @Column(type="string", length=255)
         */
        protected $title;

        /**
         * @Column(type="text")
         */
        protected $description;

        /**
         * @Column(type="datetime")
         */
        protected $startUTC;

        /**
         * @Column(type="datetime")
         */
        protected $endUTC;

        /**
         * @Column(type="boolean")
         */
        protected $isAllDay;

        /**
         * @Column(type="boolean")
         */
        protected $useCalendarTimezone;

        /**
         * @Column(type="string", length=255)
         */
        protected $timezoneName;

        /**
         * @Column(type="string", length=10)
         */
        protected $eventColor;

        /**
         * @Column(type="boolean")
         */
        protected $isRepeating;

        /**
         * @Column(type="string", length=255)
         */
        protected $repeatTypeHandle;

        /**
         * @Column(type="integer")
         */
        protected $repeatEvery;

        /**
         * @Column(type="boolean")
         */
        protected $repeatIndefinite;

        /**
         * @Column(type="datetime")
         */
        protected $repeatEndUTC;

        /**
         * @Column(type="boolean")
         */
        protected $repeatMonthlyMethod;

        /**
         * @Column(type="integer")
         */
        protected $ownerID;

        /**
         * @PrePersist
         * @PreUpdate
         */
        public function setStartUTC(){
            if( !($this->startUTC instanceof DateTime) ){
                $this->startUTC = new DateTime($this->startUTC, new DateTimeZone('UTC'));
            }
        }

        /**
         * @PrePersist
         * @PreUpdate
         */
        public function setEndUTC(){
            if( !($this->endUTC instanceof DateTime) ){
                $this->endUTC = new DateTime($this->endUTC, new DateTimeZone('UTC'));
            }
        }

        /**
         * @PrePersist
         * @PreUpdate
         */
        public function setRepeatEndUTC(){
            if( !($this->repeatEndUTC instanceof DateTime) ){
                $this->repeatEndUTC = new DateTime($this->repeatEndUTC, new DateTimeZone('UTC'));
            }
        }

        /**
         * @PostPersist
         * @PostUpdate
         */
        public function postPersistEvent(){
            EventRepeat::purgeByID($this->id);

            if( $this->isRepeating ){
                switch( $this->repeatTypeHandle ){
                    case self::REPEAT_TYPE_HANDLE_DAILY:
                    case self::REPEAT_TYPE_HANDLE_YEARLY:
                        EventRepeat::create(array('eventID' => $this->id));
                        break;

                    case self::REPEAT_TYPE_HANDLE_WEEKLY:
                        if( is_object($this->repeatSettings) && !empty($this->repeatSettings->weekdayIndices) ){
                            foreach($this->repeatSettings->weekdayIndices AS $weekdayIndex){
                                EventRepeat::create(array('eventID' => $this->id, 'repeatWeekday' => $weekdayIndex));
                            }
                        }
                        break;

                    case self::REPEAT_TYPE_HANDLE_MONTHLY:
                        // If its repeating only on a specific date(eg. "21st" of every month)
                        if( $this->repeatMonthlyMethod === self::REPEAT_MONTHLY_SPECIFIC_DATE ){
                            EventRepeat::create(array('eventID' => $this->id, 'repeatDay' => $this->repeatSettings->monthlySpecificDay));
                            // Its repeating on an abstract (eg. "Second Thursday" of every month)
                        }else{
                            EventRepeat::create(array('eventID' => $this->id, 'repeatWeek' => $this->repeatSettings->monthlyDynamicWeek, 'repeatWeekday' => $this->repeatSettings->monthlyDynamicWeekday));
                        }
                        break;
                }
            }
        }

        /**
         * Constructor
         */
        public function __construct(){

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
         * @param $id Int
         * @return mixed SchedulizerCalendar|null
         */
        public static function getByID( $id ){
            return self::entityManager()->find(__CLASS__, $id);
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
         * List queries
         ***************************************************************/

        /**
         * @return array
         */
        public static function findAll(){
            return self::entityManager()->getRepository(__CLASS__)->findAll();
        }

        /**
         * @param $title string
         * @return array
         */
        public static function findByTitle( $title ){
            return self::entityManager()->getRepository(__CLASS__)->createQueryBuilder('cal')
                ->where('cal.title LIKE :title')
                ->setParameter('title', "%{$title}%")
                ->getQuery()
                ->getResult();
        }

        /**
         * @param $ownerID int
         * @return mixed
         */
        public static function findByOwnerID( $ownerID ){
            return self::entityManager()->getRepository(__CLASS__)->findByOwnerID( $ownerID );
        }
    }

}