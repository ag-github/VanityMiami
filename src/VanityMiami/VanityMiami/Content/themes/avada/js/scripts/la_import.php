<?php
require_once 'bootstrap.php';

class La_Scripts_OldLAImport extends Gpf_Module implements Gpf_Controller{

    public function __construct() {
        parent::__construct('agent', 'A');
        $this->addController($this);
        Gpf_Event_Bus::init(new La_Event_Factory());
    }

    public function execute() {
        Gpf_NewSession::init();
        Gpf_NewSession::start();
        $importLaTask = new La_Import_ImportOldLiveAgentData();
		$importLaTask->insertTask();
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

Gpf_Module::run(new La_Scripts_OldLAImport());
?>
