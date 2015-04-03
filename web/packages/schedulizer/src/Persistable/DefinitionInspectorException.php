<?php namespace Concrete\Package\Schedulizer\Src\Persistable {

    class DefinitionInspectorException extends \Exception {

        /**
         * A class trying to be parsed doesn't declare a table name, or is not annotated at all
         * at the class level.
         * @param \ReflectionClass $reflected
         * @return DefinitionInspectorException
         */
        public static function classNotAnnotated( \ReflectionClass $reflected ){
            return new self(sprintf("Class %s does not contain a table annotation.", $reflected->getName()));
        }

        /**
         * A property marked as annotated (contains @definition) has invalid JSON syntax.
         * @param \ReflectionProperty $reflected
         * @return DefinitionInspectorException
         */
        public static function invalidPropertyAnnotation( \ReflectionProperty $reflected ){
            return new self(sprintf("Invalid annotation syntax on property %s in class %s", $reflected->name, $reflected->class));
        }

    }

}