<?php namespace Concrete\Package\Schedulizer\Src\Persistable {

    class Handler {

        protected $entity;
        protected $definition;

        public function __construct( DefinitionInspector $definition, $entity ){
            $this->definition = $definition;
            $this->entity = $entity;
        }

    }

}