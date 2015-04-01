<?php namespace Schedulizer\Tests {

    use \Doctrine\ORM\Tools\SchemaTool;

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
         * @todo: should this be returning a ->clear'd instance of the entity manager? probably...
         * @return \Doctrine\ORM\EntityManager|null
         */
        protected function packageEntityManager(){
            if( $this->_packageEntityManager === null ){
                $this->_packageEntityManager = \Core::make('SchedulizerEntityManager'); //$this->packageStructManager()->getEntityManager();
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

        /**
         * Destroy schema will nuke the schema for Schedulizer package. By default,
         * if no argument is passed in, this will destroy ALL entities related to
         * the package. Alternatively, you can pass in metadata for a specific entity
         * and kill just that one (eg. kill only the calendar table).
         * @param array $metaDatas
         * @return $this
         */
        protected function destroySchema( array $metaDatas = null ){
            $schemaTool = new SchemaTool($this->packageEntityManager());
            if( $metaDatas !== null ){
                $schemaTool->dropSchema($metaDatas);
                return $this;
            }
            $schemaTool->dropSchema($this->packageMetadatas());
            return $this;
        }

        /**
         * Same options as described in destroySchema method, but this time
         * takes care of creating.
         * @return $this
         * @throws \Doctrine\ORM\Tools\ToolsException
         */
        protected function createSchema( array $metaDatas = null ){
            $schemaTool = new SchemaTool($this->packageEntityManager());
            if( $metaDatas !== null ){
                $schemaTool->createSchema($metaDatas);
                return $this;
            }
            $schemaTool->createSchema($this->packageMetadatas());
            return $this;
        }

    }

}