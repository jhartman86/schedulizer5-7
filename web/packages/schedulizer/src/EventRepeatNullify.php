<?php namespace Concrete\Package\Schedulizer\Src {

    use \DateTime;
    use Concrete\Package\Schedulizer\Src\Persistable\Contracts\Persistant;
    use Concrete\Package\Schedulizer\Src\Persistable\Mixins\Crud;

    /**
     * Class SchedulizerEventRepeatNullify
     * @package Concrete\Package\Schedulizer\Src
     * @definition({"table":"SchedulizerEventRepeatNullify"})
     */
    class EventRepeatNullify extends Persistant {

        use Crud;

        /** @definition({"cast":"int", "declarable":false}) */
        protected $id;

        /** @definition({"cast":"int"}) */
        protected $eventID;

        /** @definition({"cast":"datetime"}) */
        protected $hideOnDate;

        public function jsonSerialize(){
            $properties = (object) get_object_vars($this);
            if( $this->hideOnDate instanceof DateTime ){
                $properties->hideOnDate = $this->hideOnDate->format('c');
            }
            return $properties;
        }

        /****************************************************************
         * Fetch Methods
         ***************************************************************/

        public static function fetchAllByEventID( $eventID ){
            return self::fetchMultipleBy(function( \PDO $connection, $tableName ) use ($eventID){
                $statement = $connection->prepare("SELECT * FROM {$tableName} WHERE eventID=:eventID ORDER BY hideOnDate asc");
                $statement->bindValue(':eventID', $eventID);
                return $statement;
            });
        }

    }

}