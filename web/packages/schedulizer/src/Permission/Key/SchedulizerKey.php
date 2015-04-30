<?php namespace Concrete\Package\Schedulizer\Src\Permission\Key {

    use \Concrete\Package\Schedulizer\Src\Permission\Assignment\SchedulizerAssignment;
    use PermissionKey; /** @see \Concrete\Core\Permission\Key\Key */

    class SchedulizerKey extends PermissionKey {

        public static function getList(){
            return parent::getList('schedulizer');
        }

        public function getPermissionAssignmentObject(){
            if (is_object($this->permissionObject)) {
                $className = $this->permissionObject->getPermissionAssignmentClassName();
                $targ = Core::make($className);
                $targ->setPermissionObject($this->permissionObject);
            } else {
                $targ = new SchedulizerAssignment();
            }
            $targ->setPermissionKeyObject($this);
            return $targ;
        }

    }

}