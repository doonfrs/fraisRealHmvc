<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class fraisController extends CController
{

	/**
	 * Creates the action instance based on the action name.
	 * The action can be either an inline action or an object.
	 * The latter is created by looking up the action map specified in {@link actions}.
	 * @param string $actionID ID of the action. If empty, the {@link defaultAction default action} will be used.
	 * @return CAction the action instance, null if the action does not exist.
	 * @see actions
	 */
	public function createAction($actionID)
	{
		$action = null;
		if($actionID==='')
			$actionID=$this->defaultAction;
		if(method_exists($this,'action'.$actionID) && strcasecmp($actionID,'s')) // we have actions method
		{	
			return new CInlineAction($this,$actionID);
		}
		elseif(strpos($actionID,'.')!==false)
		{
			$action = $this->createActionFromWidgetCache($actionID);
			if(!$action)
			{
				$action=$this->createActionFromMap($this->actions(),$actionID,$actionID);
			}
			
			if($action!==null && !method_exists($action,'run'))
				throw new CException(Yii::t('yii', 'Action class {class} must implement the "run" method.', array('{class}'=>get_class($action))));
			
		}
		
		return $action;
	}
	
	
	
	public function createActionFromWidgetCache($actionID)
	{
		//important to set actionid manually , this will be used in many functions like controller->createurl ...
		$requestActionID = $actionID;
		

		$ary = explode(".",$actionID);
		
		$widgetActionPrefix = array_pop($ary);
		$actionID = array_pop($ary);
		
		$compName = implode(".", $ary);
		Yii::import($compName,true);
		
		$widgetName = array_pop($ary);
		
		Yii::import(implode(".", $ary) . '.actions.*');
		$comp= $this->getWidgetFromCache($widgetActionPrefix);
		
		
		if(method_exists($comp,'Actions'))
		{
			$action = $this->createActionFromMap($comp->Actions(), $actionID, $requestActionID);
			
			$action->setOwner($comp);
			return $action;
		}else
		{
			return null;
		}
	}

	
	public function getWidgetFromCache($actionPrefix)
	{
		$cacheFile = $this->getWidgetCacheDir() . DIRECTORY_SEPARATOR . $actionPrefix;
		
		if(is_file($cacheFile))
		{
			$w = file_get_contents($cacheFile);
			$w =  (unserialize($w));
			$w->actionPrefix = $actionPrefix;
			
			if(false) $w = new fraisWidget();
			$w->setOwner($this);
			
			
			
			return $w;
		}
		
	}
	
	
	
	public function saveWidgetToCache($widget)
	{
		
		if(false) $widget = new fraisWidget;
		
		//clone the object , set owner to null to avoid serializaition of owner and its properties ...
		//this will make widget key shared between controllers if widget has the same properties (sameproperties = same action prefix = same cache key)
		$cw = clone $widget; if(false) $cw = new fraisWidget;
		$cw->cleanUpForSerialization();
		
		$s = serialize($cw);
		$cw->actionPrefix =sprintf("%u", crc32($s)); //crc32 is faster from md5
		
		
		
		$widget->actionPrefix = $cw->actionPrefix; 
		
		$cacheFile = $this->getWidgetCacheDir() . DIRECTORY_SEPARATOR . $widget->actionPrefix;
		
		//only create the file if not exists
		if(!is_file($cacheFile)) 	
		{
			file_put_contents($cacheFile , $s);
		}
	}
	
	private function getWidgetCacheDir()
	{
		$dir = Yii::app()->runtimePath . DIRECTORY_SEPARATOR . 'widgetscache';
		@mkdir($dir);
		if(!is_dir($dir))
		{
			throw new CHttpException(500, "unable to create widget cache dir");
		}
		
		return $dir ;
	}
	
	


	
	/**
	 * Creates a widget and initializes it.
	 * This method first creates the specified widget instance.
	 * It then configures the widget's properties with the given initial values.
	 * At the end it calls {@link CWidget::init} to initialize the widget.
	 * Starting from version 1.1, if a {@link CWidgetFactory widget factory} is enabled,
	 * this method will use the factory to create the widget, instead.
	 * @param string $className class name (can be in path alias format)
	 * @param array $properties initial property values
	 * @return CWidget the fully initialized widget instance.
	 */
	public function createWidget($className,$properties=array())
	{
		$w = parent::createWidget($className , $properties);
		
		
		/**
		 * only save classes that extends fraisWidget , other classes no need to save in cache 
		 */
		if(get_parent_class($w) == 'fraisWidget')
		{
			$this->saveWidgetToCache($w);			
		}
		return $w;
	}
	
}