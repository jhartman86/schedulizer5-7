<?php namespace Concrete\Package\Schedulizer\Controller\SinglePage\Dashboard\Schedulizer {

    use PermissionAccess;
    use \Concrete\Package\Schedulizer\Src\Permission\Key\SchedulizerKey;
    use \Concrete\Package\Schedulizer\Controller\DashboardController;

    class Permissions extends DashboardController {

        public function view(){

            $this->set('permissionKeyList', SchedulizerKey::getList());
        }

    }

}