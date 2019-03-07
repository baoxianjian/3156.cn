<?php
/**
 * 加载solr
 * @author tong
 * @date 2014/6/10
 */

require_once(dirname(__FILE__) . '/solr/Solr.php');

class SSolr{
	private static $_config;
	private static $_rc;
	public function __construct($zone='default'){
		//修改默认配置
		$this->useConfig($zone);
	}
	static function setConfigFile($file){
		self::$_config = $file;
	}
	static function getConfig($zone=null){
		return SConfig::getConfig(self::$_config,$zone);
	}
	public function useConfig($zone){
		$hosts=array();
		$cfg = self::getConfig($zone);
		if(empty($cfg))$cfg=self::getConfig("default");
		if(!is_array($cfg)){
			$tmp = $cfg;
			unset($cfg);
			$cfg[] = $tmp;
		}
		$hosts = $cfg[0];
		self::$_rc = new solr($hosts->host,$hosts->port,$hosts->path);
	}
	public function __call($name,$args){
		return call_user_func_array(array(self::$_rc,$name),$args);
	}
	
}