<?php namespace Concrete\Package\Schedulizer\Src {

    use DateTimeZone;
    use \Doctrine\Common\Collections\ArrayCollection;

    /**
     * Class Calendar
     * @package Concrete\Package\Schedulizer\Src
     * @Entity
     * @Table(name="SchedulizerCalendar",indexes={
     *  @Index(name="createdUTC",columns="createdUTC"),
     *  @Index(name="modifiedUTC",columns="modifiedUTC"),
     *  @Index(name="title",columns="title"),
     *  @Index(name="ownerID",columns="ownerID")
     * })
     * @HasLifecycleCallbacks
     */
    class Calendar extends Bin\Persistable {

        use Bin\Traits\Persistable, Bin\Traits\Unique;

        /**
         * @Column(type="string", length=255, nullable=true)
         */
        protected $title;

        /**
         * @Column(type="integer", nullable=false, options={"unsigned":true,"default":1})
         */
        protected $ownerID;

        /**
         * @Column(type="string", length=255, nullable=false)
         */
        protected $defaultTimezone;

        /**
         * @OneToMany(targetEntity="Concrete\Package\Schedulizer\Src\Event", mappedBy="calendarInstance", cascade={"all"})
         */
        protected $associatedEvents;

        /**
         * Constructor
         */
        public function __construct(){
            $this->associatedEvents = new ArrayCollection();
            $this->defaultTimezone  = self::DEFAULT_TIMEZONE;
        }

        /**
         * @return string
         */
        public function __toString(){
            return ucwords( $this->title );
        }

        /**
         * Add an event.
         * @param Event $event
         * @return Calendar
         */
        public function addEvent( Event $event ){
            // Bi-directional!
            if( ! $this->associatedEvents->contains($event) ){
                $this->associatedEvents->add($event);
                $event->setCalendarInstance($this);
            }
            return $this;
        }

        public function removeEvent( Event $event ){
            if( $this->associatedEvents->contains($event) ){
                $this->associatedEvents->removeElement($event);
            }
            return $this;
        }

        /**
         * @return ArrayCollection
         */
        public function getEvents(){
            return $this->associatedEvents->toArray();
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
        public static function getByID( $id ){
            return self::entityManager()->find(__CLASS__, $id);
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