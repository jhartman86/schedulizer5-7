<?php namespace Concrete\Package\Schedulizer\Controller {

    use View;
    use User;
    use Loader;

    class DashboardController extends \Concrete\Core\Page\Controller\DashboardPageController {

        protected $_userObj;

        public function on_start(){
            $this->requireAsset('redactor');
            $this->requireAsset('core/file-manager');
            $this->addHeaderItem( \Core::make('helper/html')->css('app.css', 'schedulizer') );
            $this->addHeaderItem('<script type="text/javascript">var _Schedulizer = {dashboard:"'.View::url('/dashboard/schedulizer').'",api:"'.View::url('/_schedulizer').'"};</script>');
            $this->addFooterItem('<script src="//cdnjs.cloudflare.com/ajax/libs/Sortable/1.1.1/Sortable.min.js"></script>');
            $this->addFooterItem( \Core::make('helper/html')->javascript('core.js', 'schedulizer') );
            $this->addFooterItem( \Core::make('helper/html')->javascript('app.js', 'schedulizer') );
            $this->addFooterItem('<script type="text/javascript">var CCM_EDITOR_SECURITY_TOKEN = \''.Loader::helper('validation/token')->generate('editor').'\'</script>');
        }

        /**
         * @return User
         */
        protected function currentUser(){
            if( $this->_userObj === null ){
                $this->_userObj = new User;
            }
            return $this->_userObj;
        }

    }

}