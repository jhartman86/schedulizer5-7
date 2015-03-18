<?php namespace Concrete\Package\Schedulizer\Src {

    use Loader;

    /**
     * Class SchedulizerEventRepeatNullify
     * @package Concrete\Package\Schedulizer\Src
     * @Entity
     * @Table(name="SchedulizerEventRepeatNullify",indexes={
     *  @Index(name="eventID",columns="eventID"),
     *  @Index(name="hideOnDate",columns="hideOnDate")
     * })
     */
    class EventRepeatNullify extends Bin\Persistable {

        use Bin\Traits\Persistable;

        /**
         * @Id
         * @Column(type="integer")
         * @GeneratedValue(strategy="NONE")
         */
        protected $eventID;

        /**
         * @Column(type="datetime", nullable=false)
         */
        protected $hideOnDate;

        public static function purgeByID( $id ){
            Loader::db()->Execute("DELETE FROM SchedulizerEventRepeatNullify WHERE eventID = ?", array($id));
        }

    }

}