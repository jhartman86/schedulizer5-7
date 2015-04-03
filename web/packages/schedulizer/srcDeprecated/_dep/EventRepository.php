<?php namespace Concrete\Package\Schedulizer\Src {

    use Database;
    use \Doctrine\ORM\EntityRepository;

    class EventRepository extends EntityRepository {

        public static function entityManager(){
            return Database::get()->getEntityManager();
        }

        public static function getByID( $id ){
            return self::entityManager()->find($id);
        }

    }

}