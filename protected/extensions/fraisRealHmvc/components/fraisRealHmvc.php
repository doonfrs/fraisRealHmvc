<?php
/**
 * Frais class file.
 * @author Firas Abd Alrahman <doonfrs@gmail.com>
 * @copyright Copyright &copy; 
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version 1.0
 */

/**
 * Frais application component.
 */
class fraisRealHmvc extends CApplicationComponent
{
	public function init()
	{
		parent::init();
		$this->setPathAlias();
		$this->Import();
	}
	
	
	private function Import()
	{
		Yii::import('fraisRealHmvc.components.*');
	}
	
	private function setPathAlias()
	{
		if(!Yii::getPathOfAlias('fraisRealHmvc')) Yii::setPathOfAlias("fraisRealHmvc",realpath(dirname(__FILE__) . '/..'));
	}
}
