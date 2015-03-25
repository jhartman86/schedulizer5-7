<?php namespace Concrete\Package\Schedulizer\Src {

    use Loader;

    /**
     * Class SchedulizerEventRepeat
     * @package Concrete\Package\Schedulizer\Src
     * @Entity
     * @Table(name="SchedulizerEventRepeat",indexes={
     *  @Index(name="eventID",columns="eventID"),
     *  @Index(name="repeatWeek",columns="repeatWeek"),
     *  @Index(name="repeatDay",columns="repeatDay"),
     *  @Index(name="repeatWeekday",columns="repeatWeekday")
     * })
     */
    class EventRepeat extends Bin\Persistable {

        use Bin\Traits\Persistable;

        /**
         * @Id @Column(type="integer") @GeneratedValue(strategy="IDENTITY")
         * @var int
         */
        protected $id;

        /**
         * @Column(type="integer", nullable=false, options={"unsigned":true})
         */
        protected $eventID;
        public function setEventID( $id ){ $this->eventID = $id; }

        /**
         * @Column(type="integer", nullable=true, options={"unsigned":true})
         */
        protected $repeatWeek;

        /**
         * @Column(type="integer", nullable=true, options={"unsigned":true})
         */
        protected $repeatDay;

        /**
         * @Column(type="integer", nullable=true, options={"unsigned":true})
         */
        protected $repeatWeekday;

        /**
         * @ManyToOne(targetEntity="\Concrete\Package\Schedulizer\Src\Event", inversedBy="eventRepeatSettings")
         * @JoinColumn(name="eventID", referencedColumnName="id")
         */
        protected $event;

        public function setEventObject( Event $event ){
            $this->event = $event;
        }


        public function __construct( $setters = array() ){
            $this->setPropertiesFromArray($setters);
        }

        public static function purgeByID( $id ){
            Loader::db()->Execute("DELETE FROM SchedulizerEventRepeat WHERE eventID = ?", array($id));
        }

    }

}