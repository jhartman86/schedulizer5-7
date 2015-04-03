<?php namespace Concrete\Package\Schedulizer\Src\Persistable {

    use \DateTime;
    use \DateTimeZone;
    use \ReflectionClass;
    use \ReflectionObject;
    use \ReflectionProperty;

    class DefinitionInspector {

        protected static $parsedCache = array();

        protected $reflectionObject;
        protected $_propertyDefinitions;
        protected $_classDefinition;
        protected $_declarablePropertyDefinitions;

        protected function __construct( $object ){
            $this->reflectionObject = new ReflectionClass($object);
        }

        /**
         * @param $object
         * @return DefinitionInspector
         */
        public static function parse( $object ){
            if( ! self::$parsedCache[get_class($object)] ){
                self::$parsedCache[get_class($object)] = new self($object);
            }
            return self::$parsedCache[get_class($object)];
        }

        /**
         * Analyze comments marked as @definition({ ..VALID_JSON.. }) on class
         * @return mixed|null
         */
        public function classDefinition(){
            if( $this->_classDefinition === null ){
                if( preg_match('/@definition\((.*)\)/', $this->reflectionObject->getDocComment(), $def) ){
                    $this->_classDefinition = json_decode($def[1]);
                }
            }
            return $this->_classDefinition;
        }

        /**
         * Analyze comments marked as @definition({ ..VALID_JSON.. }) on properties
         * @return array
         */
        public function propertyDefinitions(){
            if( $this->_propertyDefinitions === null ){
                $this->_propertyDefinitions = array();
                foreach( $this->reflectionObject->getProperties() AS $reflProp ){
                    if( $reflProp->isDefault() ){
                        if( preg_match('/@definition\((.*)\)/', $reflProp->getDocComment(), $def) ){
                            $declaration = json_decode($def[1]);
                            if( $declaration ){
                                $this->_propertyDefinitions[$reflProp->getName()] = $declaration;
                            }
                        }
                    }
                }
            }
            return $this->_propertyDefinitions;
        }

        /**
         * Using the already parsed property definitions, filter out properties
         * that should not have their values passed to the database on persisting
         * (eg. id, modifiedUTC)
         * @return array
         */
        public function declarablePropertyDefinitions(){
            if( $this->_declarablePropertyDefinitions === null ){
                $this->_declarablePropertyDefinitions = array_filter($this->propertyDefinitions(),
                    function( $definition ){
                        if( !($definition->declarable === false) ){
                            return true;
                        }
                    }
                );
            }
            return $this->_declarablePropertyDefinitions;
        }

//        public function declarableOnUpdateOnlyProperties(){
//            if( $this->_declarableOnUpdateOnlyProperties === null ){
//                $this->_declarableOnUpdateOnlyProperties = array_filter($this->declarableOnPersistProperties(),
//                    function( $definition ){
//                        if( !($definition->declarableOnPersist === "createOnly") ){
//                            return true;
//                        }
//                    }
//                );
//            }
//            return $this->_declarableOnUpdateOnlyProperties;
//        }

        public function reflectSettablesOntoInstance( $object, $data = array() ){
            $this->reflectWith($this->declarablePropertyDefinitions(), $object, $data);
        }

        public function reflectAllOntoInstance( $object, $data = array() ){
            $this->reflectWith($this->propertyDefinitions(), $object, $data);
        }

        protected function reflectWith( $properties, $object, $data ){
            $reflection = new ReflectionObject($object);
            $data       = (is_object($data)) ? $data : (object) $data;

            foreach($properties AS $prop => $definition){
                if( $data->{$prop} ){
                    $property = $reflection->getProperty($prop);
                    $property->setAccessible(true);
                    $this->setReflectedPropertyOnObject($property, $definition, $object, $data->{$prop});
                }
            }
        }

        /**
         * Looks at the property $definition->cast value, and determines how to
         * handle setting the value on an object.
         * @param ReflectionProperty $property
         * @param $definition
         * @param $object
         * @param $value
         */
        protected function setReflectedPropertyOnObject( ReflectionProperty $property, $definition, $object, $value ){
            $dtzUTC = new DateTimeZone('UTC');
            switch( $definition->cast ){
                case 'integer':
                    $property->setValue($object, (int)$value); break;

                case 'boolean':
                    $property->setValue($object, (bool)(int)$value); break;

                case 'datetime':
                    if( $value instanceof DateTime ){
                        $value->setTimezone($dtzUTC);
                        $property->setValue($object, $value);
                        break;
                    }
                    $property->setValue($object, new DateTime($value, $dtzUTC));
                    break;
                case 'string':
                    $property->setValue($object, $value);
                    break;
            }
        }

    }

}