<?php namespace Concrete\Package\Schedulizer\Src\Bin\Traits {

    use Database;

    /**
     * Class Persistable
     * @package Concrete\Package\Schedulizer\Src\Traits
     * @todo: Installation test to ensure support for prepersist callbacks!
     */
    trait Persistable {

        use SettersSerializers;

        /**
         * @param array $data
         * @return mixed
         */
        public static function create( $data ){
            $instance = new self();
            $instance->mergePropertiesFrom($data)->save();
            return $instance;
        }

        /**
         * Update the instance with the given properties
         * @param array $data
         * @return mixed
         */
        public function update( $data ){
            $this->mergePropertiesFrom($data)->save();
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
        public static function entityManager(){
            return \Core::make('SchedulizerEntityManager');
            //return Database::get()->getEntityManager();
        }


        /***************************************************
         * Fucking Doctrine pollutes your objects to a positively
         * disgusting level with recursion, so print_r'ing an object will cause the
         * system to run out of memory unless you use this convenience
         * method for viewing object state.
         **************************************************/
        public function _dump(){
            \Doctrine\Common\Util\Debug::dump($this);
        }

    }

}