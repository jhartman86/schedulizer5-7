<?php namespace Concrete\Package\Schedulizer\Src\Persistable\Mixins {

    use \PDO;
    use Concrete\Package\Schedulizer\Src\Persistable\DefinitionInspector;
    use Concrete\Package\Schedulizer\Src\Persistable\Handler;

    trait Persistable {

        use Hooks, SettersSerializers;

        private static $_pdoConnection;

        /**
         * @return \PDO
         */
        private static function pdo(){
            if( self::$_pdoConnection === null ){
                self::$_pdoConnection = \Core::make('SchedulizerDB');
            }
            return self::$_pdoConnection;
        }

        /**
         * Is the entity persisted?
         * @return bool
         */
        public function isPersisted(){
            return (bool)((int)$this->id >= 1);
        }

        /**
         * Get an instance by ID
         * @param $id
         * @return $this|void
         */
        final public static function getByID( $id ){
            $instance   = new self();
            $definition = DefinitionInspector::parse($instance);
            $statement  = self::pdo()->prepare("SELECT * FROM {$definition->classDefinition()->table} WHERE id=:id");
            $statement->execute(array(':id' => $id));
            $result = $statement->fetch(\PDO::FETCH_OBJ);
            if( ! $result ){
                return;
            }
            $definition->reflectAllOntoInstance($instance, $result);
            $instance->onAfterFetch($result);
            return $instance;
        }

        /**
         * Create a new instance
         * @param $data
         */
        final public static function create( $data ){
            $instance   = new self();
            $instance->mergePropertiesFrom($data);
            return $instance->save();
        }

        /**
         * Update an instance
         * @param $data
         */
        final public function update( $data ){
            $this->mergePropertiesFrom($data);
            return $this->save();
        }

        /**
         * Both create and update proxy to this method, or it can be called directly
         * after doing some work on an entity. Lots of flexibility.
         */
        final public function save(){
            $handler = new Handler(DefinitionInspector::parse($this), $this);

//            if( $this->_needsInspecting ){
//                $definition = DefinitionInspector::parse($this);
//                $data = array();
//                foreach($definition->declarableOnPersistProperties() AS $prop => $def){
//                    $data[$prop] = $this->{$prop};
//                }
//                $definition->reflectOntoInstance($this, $data);
//            }else{
//                $definition = DefinitionInspector::parse($this);
//            }

            // Query setup stuff
            if( $this->isPersisted() ){
                $persistable    = array_keys($definition->declarableOnUpdateOnlyProperties());
                $placeholders   = join(',', array_map(function($item){return "{$item}=:{$item}";}, $persistable));
                $statement      = self::pdo()->prepare("UPDATE {$definition->classDefinition()->table} SET {$placeholders} WHERE id=:id");
                $statement->bindValue(':id', $this->id);
                foreach($definition->declarableOnUpdateOnlyProperties() AS $prop => $propertyDefinition){
                    $this->castAndBindToPersistStatement($propertyDefinition, $statement, $prop);
                }
            }else{
                $persistable    = array_keys($definition->declarableOnPersistProperties());
                $columns        = join(',', $persistable);
                $placeholders   = join(',', array_map(function($item){return ":{$item}";}, $persistable));
                $statement      = self::pdo()->prepare("INSERT INTO {$definition->classDefinition()->table} ({$columns}) VALUES({$placeholders})");
                foreach($definition->declarableOnPersistProperties() AS $prop => $propertyDefinition){
                    $this->castAndBindToPersistStatement($propertyDefinition, $statement, $prop);
                }
            }

            $statement->execute();
        }

        final public function delete(){

        }

        private function castAndBindToPersistStatement( $propertyDefinition, $statement, $prop ){
            echo $prop;
            switch( $propertyDefinition->cast ){
                case 'integer':
                    $statement->bindValue(":{$prop}", (int) $this->{$prop});
                    break;
                case 'boolean':
                    $statement->bindValue(":{$prop}", ((bool)(int)$this->{$prop}) === true ? 1 : 0);
                    break;
                case 'datetime':
                    $statement->bindValue(":{$prop}", '2015-01-02 02:00:00');// $this->{$prop}->format('Y-m-d H:i:s'));
                    break;
                case 'string':
                    $statement->bindValue(":{$prop}", (string) $this->{$prop});
                    break;
            }
        }

    }

}