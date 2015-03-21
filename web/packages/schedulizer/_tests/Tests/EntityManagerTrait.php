<?php namespace Schedulizer\Tests {

    /**
     * Use this trait for easy access to specific things from C5. Technically
     * these should be IMMUTABLE.
     * @todo: return cloned instances all the time?
     * Class EntityManagerTrait
     * @package Schedulizer\Tests
     */
    trait EntityManagerTrait {

        use \Schedulizer\Tests\PackageTrait;

        protected $_packageStructManager = null;
        protected $_packageEntityManager = null;
        protected $_packageMetadatas     = null;

        /**
         * @return \Concrete\Core\Database\DatabaseStructureManager|null
         */
        protected function packageStructManager(){
            if( $this->_packageStructManager === null ){
                $this->_packageStructManager = $this->packageClass()->getDatabaseStructureManager();
            }
            return $this->_packageStructManager;
        }

        /**
         * @return \Doctrine\ORM\EntityManager|null
         */
        protected function packageEntityManager(){
            if( $this->_packageEntityManager === null ){
                $this->_packageEntityManager = $this->packageStructManager()->getEntityManager();
            }
            return $this->_packageEntityManager;
        }

        /**
         * Get metadata for the package entities. If a class name is passed in as
         * a parameter, it'll return the entity data for just that class.
         *
         * @param $fullEntityName string 'Concrete\Package\...', NOT '\Concrete\Package...'
         * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata[]|null
         */
        protected function packageMetadatas( $fullEntityName = null ){
            if( $this->_packageMetadatas === null ){
                $this->_packageMetadatas = $this->packageStructManager()->getMetadatas();
            }

            if( is_string($fullEntityName) ){
                return $this->_packageMetadatas[$fullEntityName];
            }

            return $this->_packageMetadatas;
        }

    }

}