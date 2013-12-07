<?php
class fraisModel extends CActiveRecord
{
	
	public function getDbConnection()
    {
		if(Yii::app()->controller->module==null)
		{
			return parent::getDbConnection();
		}else
		{
			$dbsetname = Yii::app()->controller->module->database;
			$dbobject = Yii::app()->$dbsetname;
	        return $dbobject;
		}
    }
}