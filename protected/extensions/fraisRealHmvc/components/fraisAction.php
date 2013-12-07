<?php

class fraisAction extends CAction
{
	
	
	/**
	 * router will set this value to the caller component 
	 * when calling the action by url
	 */
	private $_owner;
	
	
	/**
	 * action owner widget / controller 
	 * 
	 * @return CBaseController
	 */
	public function getOwner()
	{
		return $this->_owner;
	}
	
	
	public function setOwner($v)
	{
		$this->_owner = $v;
	}

}
