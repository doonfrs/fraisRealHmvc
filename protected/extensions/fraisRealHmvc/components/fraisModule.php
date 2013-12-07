<?php
class fraisModule extends CWebModule
{
    public $layout;
    public $layoutPath;
	public $database;
	
	public $caption = "";
	
	
    public function init()
    {
    	parent::init();
		
        $this->setLayoutPath(Yii::getPathOfAlias('application.views.layouts'));
		$this->layout = "//layouts/column1";
        $this->setImport(array(
            $this->getName() . ".models.*",
            $this->getName() . ".components.*"
        ));
		
		$this->defaultController = 'index';
    }
}
