<?php namespace Concrete\Package\Schedulizer\Src {

    use Loader;
    use Concrete\Package\Schedulizer\Src\Event;
    use Concrete\Package\Schedulizer\Src\Persistable\Contracts\Persistant;
    use Concrete\Package\Schedulizer\Src\Persistable\Mixins\Crud;

    /**
     * Class EventTag
     * @package Concrete\Package\Schedulizer\Src
     * @definition({"table":"SchedulizerEventTag"})
     */
    class EventTag extends Persistant {

        use Crud;

        /** @definition({"cast":"string","nullable":false}) */
        protected $displayText;

        /** @definition({"cast":"string","nullable":false}) */
        protected $handle;

        /**
         * @param null $string
         */
        public function __construct( $string = null ){
            if( $string !== null ){
                $this->displayText = $string;
            }
        }

        public function __toString(){
            return $this->displayText;
        }

        protected function onBeforePersist(){
            if( $this->handle === null ){
                $this->handle = Loader::helper('text')->handle($this->displayText);
            }
        }

        public static function createOrGetExisting( $mixed ){
            if( is_object($mixed) ){
                $entity = self::fetchOneBy(function(\PDO $connection, $tableName) use ($mixed){
                    $statement = $connection->prepare("SELECT * FROM {$tableName} WHERE id=:id OR displayText=:displayText OR handle=:handle");
                    $statement->bindValue(':id', (int)$mixed->id);
                    $statement->bindValue(':displayText', $mixed->displayText);
                    $statement->bindValue(':handle', $mixed->handle);
                    return $statement;
                });
                if( empty($entity)){
                    $entity = self::create($mixed);
                }
                return $entity;
            }
        }

        public static function purgeAllEventTags( Event $eventObj ){
            $eventID = $eventObj->getID();
            self::adhocQuery(function(\PDO $connection) use ($eventObj, $eventID){
                $statement = $connection->prepare("DELETE FROM SchedulizerTaggedEvents WHERE eventID=:eventID");
                $statement->bindValue(':eventID', $eventObj->getID());
                return $statement;
            });
        }

        public function tagEvent( Event $eventObj ){
            $tagID = $this->id;
            self::adhocQuery(function(\PDO $connection) use ($eventObj, $tagID){
                $statement = $connection->prepare("INSERT INTO SchedulizerTaggedEvents (eventID, eventTagID) VALUES(:eventID,:eventTagID)");
                $statement->bindValue(':eventID', $eventObj->getID());
                $statement->bindValue(':eventTagID', $tagID);
                return $statement;
            });
        }

        /****************************************************************
         * Fetch Methods
         ***************************************************************/

        public static function fetchAll(){
            return (array) self::fetchMultipleBy(function( \PDO $connection, $tableName ){
                $statement = $connection->prepare("SELECT * FROM {$tableName}");
                return $statement;
            });
        }

        public static function fetchTagsByEventID( $eventID ){
            return (array) self::fetchMultipleBy(function(\PDO $connection, $tableName) use ($eventID){
                $statement = $connection->prepare("SELECT sevt.* FROM SchedulizerEventTag sevt
                    JOIN SchedulizerTaggedEvents sete ON sevt.id = sete.eventTagID
                    WHERE sete.eventID = :eventID");
                $statement->bindValue('eventID', $eventID);
                return $statement;
            });
        }
    }

}