<?php namespace Concrete\Package\Schedulizer\Src {

    use Concrete\Package\Schedulizer\Src\Persistable\Contracts\Persistant;
    use Concrete\Package\Schedulizer\Src\Persistable\Mixins\Crud;

    /**
     * Class SchedulizerEventRepeat
     * @package Concrete\Package\Schedulizer\Src
     * @definition({"table":"SchedulizerEventRepeat"})
     */
    class EventRepeat extends Persistant {

        use Crud;

        /** @definition({"cast":"int", "declarable":false}) */
        protected $id;

        /** @definition({"cast":"int"}) */
        protected $eventID;

        /** @definition({"cast":"int","nullable":true}) */
        protected $repeatWeek;

        /** @definition({"cast":"int","nullable":true}) */
        protected $repeatDay;

        /** @definition({"cast":"int","nullable":true}) */
        protected $repeatWeekday;

        /**
         * Allow passing in an array to set properties on instantiation
         * @param array $setters
         */
        public function __construct( $setters = array() ){
            $this->mergePropertiesFrom( $setters );
        }

        /**
         * Get all EventRepeat records associated with an event.
         * @param $eventID
         * @return $this|void
         */
        public static function getAllByEventID( $eventID ){
            return self::fetchMultipleBy(function(\PDO $connection, $tableName) use ($eventID){
                $statement = $connection->prepare("SELECT * FROM {$tableName} WHERE eventID=:eventID");
                $statement->bindValue(':eventID', $eventID);
                return $statement;
            });
        }

        /**
         * Purge repeat settings associated with a given event.
         * @param $eventID
         * @throws \Exception
         */
        public static function purgeAllByEventID( $eventID ){
            self::adhocQuery(function( \PDO $connection, $tableName ) use ($eventID){
                $statement = $connection->prepare("DELETE FROM {$tableName} WHERE eventID=:eventID");
                $statement->bindValue(':eventID', $eventID);
                return $statement;
            });
        }

        /**
         * Return properties for JSON serialization
         * @return array|mixed
         */
        public function jsonSerialize(){
            $properties = (object) get_object_vars($this);
            unset($properties->id);
            unset($properties->eventID);
            return $properties;
        }

    }

}