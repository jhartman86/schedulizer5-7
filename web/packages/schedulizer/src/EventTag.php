<?php namespace Concrete\Package\Schedulizer\Src {

    /**
     * Class EventTag
     * @package Concrete\Package\Schedulizer\Src
     * @Entity
     * @Table(name="SchedulizerEventTag")
     */
    class EventTag extends Bin\Persistable {

        use Bin\Traits\Persistable;

        /**
         * @Id @Column(type="integer") @GeneratedValue(strategy="IDENTITY")
         * @var int
         */
        protected $id;

        /**
         * @Column(type="string", length=255, nullable=false)
         */
        protected $tagName;

        /**
         * @ManyToMany(targetEntity="\Concrete\Package\Schedulizer\Src\Event", mappedBy="eventTags", cascade={"persist"})
         */
        protected $events;

        /**
         * @param null $string
         */
        public function __construct( $string = null ){
            $this->events = new \Doctrine\Common\Collections\ArrayCollection();

            if( $string !== null ){
                $this->tagName = $string;
            }
        }

        public function __toString(){
            return $this->tagName;
        }

        /**
         * @param Event $event
         */
        public function addEvent( Event $event ){
            $this->events[] = $event;
        }

        /**
         * @return int|null
         */
        public function getID(){
            return $this->id;
        }

        /**
         * @param $id
         * @return null|object
         * @throws \Doctrine\ORM\ORMException
         * @throws \Doctrine\ORM\OptimisticLockException
         * @throws \Doctrine\ORM\TransactionRequiredException
         */
        public static function getByID( $id ){
            return self::entityManager()->find(__CLASS__, $id);
        }

        public static function findAll(){
            return self::entityManager()->getRepository(__CLASS__)->findAll();
        }
    }

}