<?php
require_once 'bootstrap.php';

class La_Scripts_VbImport extends Gpf_Module implements Gpf_Controller{

    public function __construct() {
        parent::__construct('agent', 'A');
        $this->addController($this);
        Gpf_Event_Bus::init(new La_Event_Factory());
    }

    public function execute() {
        $src = @$_GET['src'];
        if ($src == '') {
            die('You need to specify vBulletin forum id in src parameter');
        }
        
        $dst = @$_GET['dst'];
        if ($dst == '') {
            die('You need to specify LA forum id in dst parameter');
        }
        
        Gpf_NewSession::init();
        Gpf_NewSession::start();
        $importTask = new La_Import_ImportvBulletin();
        $importTask->setParams($src."|".$dst);
		$importTask->insertTask();
    }

    protected function initProperties() {
        $this->properties = new Gpf_Module_Properties();
    }

    protected function getTheme() {
        return null;
    }

    protected function getSessionHandler() {
        return new Gpf_Session_Handler_Db();
    }
}

Gpf_Module::run(new La_Scripts_VbImport());
?>
