<?php namespace Concrete\Package\Schedulizer\Src {

    use Loader;

    /**
     * Class SchedulizerEventRepeat
     * @package Concrete\Package\Schedulizer\Src
     * @Entity
     * @Table(name="SchedulizerEventRepeat")
     */
    class EventRepeat extends Bin\Persistable {

        use Bin\Traits\Persistable;

        /**
         * @Id
         * @Column(type="integer")
         * @GeneratedValue(strategy="NONE")
         */
        protected $eventID;

        /**
         * @Column(type="integer")
         */
        protected $repeatWeek;

        /**
         * @Column(type="integer")
         */
        protected $repeatDay;

        /**
         * @Column(type="integer")
         */
        protected $repeatWeekday;


        public static function purgeByID( $id ){
            Loader::db()->Execute("DELETE FROM SchedulizerEventRepeat WHERE eventID = ?", array($id));
        }

    }

}