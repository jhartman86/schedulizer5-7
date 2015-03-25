<?php namespace Concrete\Package\Schedulizer\Src\Bin\Traits {

    use Database;

    /**
     * Class Persistable
     * @package Concrete\Package\Schedulizer\Src\Traits
     * @todo: Installation test to ensure support for prepersist callbacks!
     */
    trait Persistable {

        /**
         * @param array $properties
         * @return self
         */
        public function setPropertiesFromArray( array $properties ){
            foreach($properties as $key => $prop) {
                $this->{$key} = $prop;
            }
            return $this;
        }

        /**
         * @param $object
         * @return $this
         */
        public function setPropertiesFromObject( $object ){
            if( is_object($object) ){
                foreach($object AS $prop => $value){
                    $this->{$prop} = $value;
                }
            }
            return $this;
        }


        /**
         * Return properties for JSON serialization
         * @return array|mixed
         */
        public function jsonSerialize(){
            return (object) get_object_vars($this);
        }


        /**
         * @param array $properties
         * @return mixed
         */
        public static function create( $data ){
            $instance = new self();
            if( is_array($data) ){
                $instance->setPropertiesFromArray( $data );
            }
            if( is_object($data) ){
                $instance->setPropertiesFromObject( $data );
            }
            $instance->save();
            return $instance;
        }

        /**
         * Update the instance with the given properties
         * @param array $properties
         */
        public function update( $data ){
            if( is_array($data) ){
                $this->setPropertiesFromArray( $data );
            }
            if( is_object($data) ){
                $this->setPropertiesFromObject( $data );
            }
            $this->save();
            return $this;
        }

        /**
         * Delete a record
         */
        public function delete(){
            $this->entityManager()->remove($this);
            $this->entityManager()->flush();
            $this->entityManager()->clear();
        }

        /**
         * Persist to the database
         * @return void
         */
        public function save(){
            $this->entityManager()->persist( $this );
            $this->entityManager()->flush();
            //$this->entityManager()->clear();
            return $this;
        }

        /**
         * @return \Doctrine\ORM\EntityManager
         */
        protected static function entityManager(){
            return Database::get()->getEntityManager();
        }

    }

}