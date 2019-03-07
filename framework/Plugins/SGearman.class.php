<?php
/**
 * 此是gearman客户端文件
 * 此文件的使用必须建立在PHP支持gearman.os的情况下
 * @author tong
 */
if(!defined("SLIGHTPHP_PLUGINS_DIR"))define("SLIGHTPHP_PLUGINS_DIR",dirname(__FILE__));
require_once(SLIGHTPHP_PLUGINS_DIR."/SConfig.class.php");
class SGearman extends GearmanClient{
	protected $_config;
	function __construct($zone='default'){
		parent::__construct();
		$this->useConfig($zone);
	}
	static function setConfigFile($file){
		self::$_config = $file;
	}
	function getConfig($zone=null){
		return SConfig::getConfig(self::$_config,$zone);
	}
	function useConfig($zone){
		parent::addServers(self::getConfig($zone));
	}
}