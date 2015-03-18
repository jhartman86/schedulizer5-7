<?php namespace Concrete\Package\Schedulizer\Src {

    use Loader;
    use DateTime;
    use DateTimeZone;
    use \Exception;

    /**
     * Class EventList. This goes completely around Doctrine and composes the database
     * query directly; no idea how to even begin building a query like this in an ORM.
     * @package Concrete\Package\Schedulizer\Src
     */
    class EventList {

        const DATE_FORMAT       = 'Y-m-d',
              DAYS_IN_FUTURE    = 45; // span 6 weeks for some calendar views

        protected $calendarIDs;
        protected $startDTO;
        protected $endDTO;
        protected $queryDaySpan = self::DAYS_IN_FUTURE;

        public function __construct( array $calendarID = array() ){
            $this->calendarIDs = $calendarID;
        }

        public function setStartDate( \DateTime $start ){
            $this->startDTO = $start;
            return $this;
        }

        public function setEndDate( \DateTime $end ){
            $this->endDTO = $end;
            return $this;
        }

        public function setCalendarIDs( $calendarIDs ){
            if( is_array($calendarIDs) ){
                $this->calendarIDs = $calendarIDs;
                return;
            }
            $this->calendarIDs = array($calendarIDs);
            return $this;
        }

        public function setDaysIntoFuture( $number = self::DAYS_IN_FUTURE ){
            if( (int)$number > self::DAYS_IN_FUTURE ){
                $number = self::DAYS_IN_FUTURE;
            }
            $this->queryDaySpan = $number;
            return $this;
        }

        public function get(){
            return Loader::db()->GetAll($this->assembledQuery());
        }

        protected function assembledQuery(){
            if( ! $this->_assembledQuery ){
                // Throw exception if no calendarIDs specified
                if( empty($this->calendarIDs) ){
                    throw new Exception("No calendar IDs specified.");
                }

                // If _queryStartDTO hasn't been defined, set it to Now()
                if( !($this->startDTO instanceof DateTime) ){
                    $this->startDTO = new DateTime('now', new DateTimeZone('UTC'));
                }

                // Conversely, if the endDTO *HAS* been set, automatically adjust
                // the queryDaySpan property to be the difference between start and end
                if( $this->endDTO instanceof DateTime ){
                    $this->queryDaySpan = $this->endDTO->diff($this->startDTO, true)->days + 1;
                }

                $this->_assembledQuery = $this->queryString();
            }
            return $this->_assembledQuery;
        }


        /**
         * Setup the base query string. This is the stupidest/beastliest SQL query ever.
         * @todo: repeat yearly (just once per year)
         * @todo: last (> 4th) "Tuesday" or whatever day of the month.
         * @todo: PERFORMANCE - include date restrictions on the JOIN of the events table,
         * so that it only joins events where the repeatEndUTC < $endDate AND startUTC < $endDate
         * and excludes *single day, historical events*
         * @return string
         */
        protected function queryString(){
            $startDate      = $this->startDTO->format(self::DATE_FORMAT);
            $inCalendarIDs  = join(',', $this->calendarIDs);

            // For singular day events, make sure to restrict by number of days (daySpan) being queried
            $endDate = clone $this->startDTO;
            $endDate->modify("+{$this->queryDaySpan} days");
            $endDate = $endDate->format(self::DATE_FORMAT);

            $queryDaySpan = $this->queryDaySpan;

            return "SELECT _eventList.*, TIMESTAMP(_eventList.eventDate, TIME(CONVERT_TZ(_eventList.startUTC, 'UTC', _eventList.timezoneName))) AS startLocalized, TIMESTAMPADD(MINUTE,TIMESTAMPDIFF(MINUTE, _eventList.startUTC, _eventList.endUTC),TIMESTAMP(_eventList.eventDate, TIME(CONVERT_TZ(_eventList.startUTC, 'UTC', _eventList.timezoneName)))) AS endLocalized FROM (
                select _unionized.eventDate, _events.*, (CASE WHEN (_unionized.eventDate != DATE(_events.startUTC)) IS TRUE THEN 1 ELSE 0 END) AS isAlias FROM (
                    select '{$startDate}' + INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY as eventDate
                      from (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as a
                      cross join (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as b
                      cross join (select 0 as a union all select 1 union all select 2 union all select 3 union all select 4 union all select 5 union all select 6 union all select 7 union all select 8 union all select 9) as c
                      LIMIT {$queryDaySpan}
                ) AS _unionized
                JOIN (
                    SELECT ev.*, evr.repeatWeek, evr.repeatDay, evr.repeatWeekday FROM SchedulizerEvent ev
                    LEFT JOIN SchedulizerEventRepeat evr ON evr.eventID = ev.id
                    WHERE ev.calendarID IN ({$inCalendarIDs})
                    AND ev.isRepeating = 1
                ) AS _events
                WHERE (_events.repeatIndefinite = 1 OR (_unionized.eventDate <= _events.repeatEndUTC AND _events.repeatIndefinite = 0))
                AND DATE(_events.startUTC) <= DATE(_unionized.eventDate)
                AND _events.id NOT IN (SELECT evnullify.eventID FROM SchedulizerEventRepeatNullify evnullify WHERE DATE(_unionized.eventDate) = DATE(evnullify.hideOnDate))
                AND (
                    ((_events.repeatTypeHandle = 'daily') AND ( DATEDIFF(_unionized.eventDate, CONVERT_TZ(_events.startUTC, 'UTC', _events.timezoneName)) % _events.repeatEvery = 0 ))
                    OR
                    ( (_events.repeatTypeHandle = 'weekly') AND (_events.repeatWeek IS NULL) AND (_events.repeatWeekday = DAYOFWEEK(_unionized.eventDate)) AND (CEIL(DATEDIFF(CONVERT_TZ(_events.startUTC, 'UTC', _events.timezoneName), _unionized.eventDate)/7) % _events.repeatEvery = 0) )
                    OR
                    ( (_events.repeatTypeHandle = 'monthly') AND (_events.repeatDay = DAYOFMONTH(_unionized.eventDate)) AND ((MONTH(_unionized.eventDate) - MONTH(CONVERT_TZ(_events.startUTC, 'UTC', _events.timezoneName))) % _events.repeatEvery = 0) )
                    OR
                    ( (_events.repeatTypeHandle = 'monthly') AND ((DATE_ADD(DATE_SUB(LAST_DAY(_unionized.eventDate), INTERVAL DAY(LAST_DAY(_unionized.eventDate)) -1 DAY), INTERVAL (((_events.repeatWeekday + 7) - DAYOFWEEK(DATE_SUB(LAST_DAY(_unionized.eventDate), INTERVAL DAY(LAST_DAY(_unionized.eventDate)) -1 DAY))) % 7) + ((_events.repeatWeek * 7) -7) DAY)) = _unionized.eventDate) AND ((MONTH(_unionized.eventDate) - MONTH(CONVERT_TZ(_events.startUTC, 'UTC', _events.timezoneName))) % _events.repeatEvery = 0))
                    OR
                    ( (_events.repeatTypeHandle = 'yearly') AND ((YEAR(_unionized.eventDate) - YEAR(CONVERT_TZ(_events.startUTC, 'UTC', _events.timezoneName))) % _events.repeatEvery = 0) )
                )
                UNION (SELECT DATE(CONVERT_TZ(ev2.startUTC, 'UTC', ev2.timezoneName)) AS eventDate, ev2.*, NULL as repeatWeek, NULL AS repeatDay, NULL AS repeatWeekday, 0 AS isAlias
                FROM SchedulizerEvent ev2 WHERE (ev2.isRepeating = 0) AND ev2.calendarID IN ({$inCalendarIDs})
                AND CONVERT_TZ(ev2.startUTC, 'UTC', ev2.timezoneName) >= '{$startDate}' AND CONVERT_TZ(ev2.startUTC, 'UTC', ev2.timezoneName) < '{$endDate}')
                ) AS _eventList";
        }

    }

}