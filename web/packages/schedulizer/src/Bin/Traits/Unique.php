<?php namespace Concrete\Package\Schedulizer\Src\Bin\Traits {

    date_default_timezone_set('UTC');

    use DateTime;
    use DateTimeZone;

    trait Unique {

        /**
         * @Id @Column(type="integer") @GeneratedValue
         * @var int
         */
        protected $id;

        /**
         * @Column(type="datetime")
         * @var DateTime
         */
        protected $createdUTC;

        /**
         * @Column(type="datetime")
         * @var DateTime
         */
        protected $modifiedUTC;

        /**
         * @PrePersist
         */
        public function setCreatedUTC(){
            if( !($this->createdUTC instanceof DateTime) ){
                $this->createdUTC = new DateTime('now', new DateTimeZone('UTC'));
            }
        }

        /**
         * @PrePersist
         * @PreUpdate
         */
        public function setModifiedUTC(){
            $this->modifiedUTC = new DateTime('now', new DateTimeZone('UTC'));
        }

        /**
         * @return int|null
         */
        public function getID(){
            return $this->id;
        }

        /**
         * @return DateTime
         */
        public function getModifiedUTC(){
            return $this->modifiedUTC;
        }

        /**
         * @return DateTime
         */
        public function getCreatedUTC(){
            return $this->createdUTC;
        }

    }

}