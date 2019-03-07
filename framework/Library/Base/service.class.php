<?php
class Base_service extends SAbstract{
	public function __construct(){
		parent::__construct();
		if (SService::getEntrance()==false) {
			die('Service必须使用SService类入口文件实例化');
		}
	}
}