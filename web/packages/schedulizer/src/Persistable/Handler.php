<?php namespace Concrete\Package\Schedulizer\Src\Persistable {

    use \DateTime;
    use \DateTimeZone;
    use \PDO;
    use \ReflectionObject;

    class Handler {

        protected $entity;
        protected $entityReflection;
        protected $definition;
        protected $pdoConnection;

        /**
         * @return \PDO
         */
        private function connection(){
            if( $this->pdoConnection === null ){
                $this->pdoConnection = \Core::make('SchedulizerDB');
            }
            return $this->pdoConnection;
        }

        /**
         * @param DefinitionInspector $definition
         * @param $entity
         */
        public function __construct( DefinitionInspector $definition, $entity ){
            $this->definition = $definition;
            $this->entity = $entity;
            $this->entityReflection = new ReflectionObject($entity);
        }

        /**
         * Execute the transaction.
         */
        public function commit(){
            if( $this->entity->isPersisted() ){
                $this->updateStatement()->execute();
                return;
            }
            // With create statements we have to reflect the ID back
            $this->createStatement()->execute();
            $reflPropID = $this->entityReflection->getProperty('id');
            $reflPropID->setAccessible(true);
            $reflPropID->setValue($this->entity, (int)$this->connection()->lastInsertId());
        }

        /**
         * @return \PDOStatement
         */
        protected function updateStatement(){
            $persistable = array_filter($this->definition->persistablePropertyDefinitions(), function($definition){
                if( is_array($definition->autoSet) && !in_array('onUpdate', $definition->autoSet) ){
                    return false;
                }
                return true;
            });

            $tableName      = $this->definition->classDefinition()->table;
            $columnNames    = array_keys($persistable);
            $placeholders   = join(',', array_map(function($item){return "{$item}=:{$item}";}, $columnNames));
            $statement      = $this->connection()->prepare("UPDATE {$tableName} SET {$placeholders} WHERE id=:id");
            // Get the ID from the object
            $reflProp = $this->entityReflection->getProperty('id');
            $reflProp->setAccessible(true);
            $statement->bindValue(':id', $reflProp->getValue($this->entity));
            // Bind values
            foreach($persistable AS $propName => $propDefinition){
                $this->castAndBindToStatement($statement, $propName, $propDefinition);
            }
            return $statement;
        }

        /**
         * @return \PDOStatement
         */
        protected function createStatement(){
            $persistable = array_filter($this->definition->persistablePropertyDefinitions(), function($definition){
                if( is_array($definition->autoSet) && !in_array('onCreate', $definition->autoSet) ){
                    return false;
                }
                return true;
            });

            $tableName      = $this->definition->classDefinition()->table;
            $columnNames    = array_keys($persistable);
            $columnsJoined  = join(',', $columnNames);
            $placeholders   = join(',', array_map(function($col){return ":{$col}";}, $columnNames));
            $statement      = $this->connection()->prepare("INSERT INTO {$tableName} ({$columnsJoined}) VALUES({$placeholders})");
            // Bind values
            foreach($persistable AS $propName => $propDefinition){
                $this->castAndBindToStatement($statement, $propName, $propDefinition);
            }
            return $statement;
        }

        /**
         * @param \PDOStatement $statement
         * @param $propName
         * @param $propDefinition
         */
        private function castAndBindToStatement( \PDOStatement $statement, $propName, $propDefinition ){
            $reflProp = $this->entityReflection->getProperty($propName);
            $reflProp->setAccessible(true);
            $value = $reflProp->getValue($this->entity);

            // Can the property be null and IS IT null?
            if( $propDefinition->nullable === true && is_null($value) ){
                $statement->bindValue(":{$propName}", null);
                return;
            }

            $commitValue = $value;

            switch( $propDefinition->cast ){
                case 'int':
                    $commitValue = (int)$value;
                    break;

                case 'bool':
                    $commitValue = ((bool)(int)$value) === true ? 1 : 0;
                    break;

                // datetime fields are the only ones that honor autoSet,
                // so to keep the entity state in sync we have to set the
                // value back on the entity
                case 'datetime':
                    if( $propDefinition->autoSet ){
                        $nowUTC = new DateTime('now', new DateTimeZone('UTC'));
                        $commitValue = $nowUTC->format('Y-m-d H:i:s');
                        $reflProp->setValue($this->entity, $nowUTC);
                        break;
                    }
                    if( $value instanceof DateTime ){
                        $commitValue = $value->format('Y-m-d H:i:s');
                    }
                    break;

                case 'string':
                    $commitValue = (string)$value;
                    break;
            }

            $statement->bindValue(":{$propName}", $commitValue);
        }

    }

}