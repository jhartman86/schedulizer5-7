<?php namespace Concrete\Package\Schedulizer\Src {

    use Concrete\Package\Schedulizer\Src\Persistable\Contracts\Persistant;
    use Concrete\Package\Schedulizer\Src\Persistable\Mixins\Crud;

    /**
     * Class EventTag
     * @package Concrete\Package\Schedulizer\Src
     */
    class EventTag extends Persistant {

        use Crud;

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
         * @ManyToMany(targetEntity="Concrete\Package\Schedulizer\Src\Event", mappedBy="eventTags", cascade={"persist"})
         */
        protected $taggedEvents;

        /**
         * @param null $string
         */
        public function __construct( $string = null ){
            $this->events = new ArrayCollection();

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
    }

}