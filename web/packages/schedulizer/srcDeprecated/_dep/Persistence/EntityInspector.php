<?php namespace Concrete\Package\Schedulizer\Src\Persistence {

    class EntityInspector {

        protected $entity;
        protected $reflection;

        public function __construct( $entity ){
            $this->entity = $entity;
            $this->reflection = new \ReflectionObject($entity);
        }


        public function insert( \PDO $pdo ){
            $columns        = join(',', array_keys($this->definitions()));
            $placeholders   = join(',', array_map(function($item){ return ":{$item}"; }, array_keys($this->definitions())));
            $statement      = $pdo->prepare("INSERT INTO {$this->entityDefinition()->table} ({$columns}) VALUES({$placeholders})");
            foreach($this->definitions() AS $column => $settings){
                $property = $this->reflection->getProperty($column);
                $property->setAccessible(true);
                $statement->bindValue(":{$column}", $property->getValue($this->entity));
            }
            $rows = $statement->execute();
        }


        protected function entityDefinition(){
            if( $this->_entityDefinition === null ){
                if( preg_match('/@definition\((.*)\)/', $this->reflection->getDocComment(), $def) ){
                    $this->_entityDefinition = json_decode($def[1]);
                }
            }
            return $this->_entityDefinition;
        }


        protected function definitions(){
            if( $this->_definitions === null ){
                $this->_definitions = array();
                foreach( $this->reflection->getProperties() AS $reflProp ){
                    if( $reflProp->isDefault() ){
                        if( preg_match('/@definition\((.*)\)/', $reflProp->getDocComment(), $def) ){
                            $declaration = json_decode($def[1]);
                            if( $declaration ){
                                $this->_definitions[$reflProp->getName()] = $declaration;
                            }
                        }
                    }
                }
            }
            return $this->_definitions;
        }
    }

}