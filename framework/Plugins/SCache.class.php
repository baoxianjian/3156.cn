<?php
/*{{{LICENSE
+-----------------------------------------------------------------------+
| SlightPHP Framework                                                   |
+-----------------------------------------------------------------------+
| This program is free software; you can redistribute it and/or modify  |
| it under the terms of the GNU General Public License as published by  |
| the Free Software Foundation. You should have received a copy of the  |
| GNU General Public License along with this program.  If not, see      |
| http://www.gnu.org/licenses/.                                         |
| Copyright (C) 2008-2009. All Rights Reserved.                         |
+-----------------------------------------------------------------------+
| Supports: http://www.slightphp.com                                    |
+-----------------------------------------------------------------------+
}}}*/

/**
 * @package SlightPHP
 */
if(!defined("SLIGHTPHP_PLUGINS_DIR"))define("SLIGHTPHP_PLUGINS_DIR",dirname(__FILE__));
require_once(SLIGHTPHP_PLUGINS_DIR."/SConfig.class.php");
class SCache{
	private static $_caches;
	private $_engine;
	private static $_config;
	/**
	 * other cache engine
	 */
	private static $engines=array("file","apc","memcache");
	
	public function __construct($engine='memcache',$zone='default'){
		$engine = strtolower($engine);
		if(in_array($engine,self::$engines)){
			$key = $engine.'_'.$zone;
			if (!self::$_caches[$key]) {
				if($engine=="apc" && extension_loaded("apc")){
					require_once(SLIGHTPHP_PLUGINS_DIR."/cache/CacheObject.php");
					require_once(SLIGHTPHP_PLUGINS_DIR."/cache/Cache_APC.php");
					self::$_caches[$key] = new Cache_APC;
				}elseif($engine=="file"){
					require_once(SLIGHTPHP_PLUGINS_DIR."/cache/CacheObject.php");
					require_once(SLIGHTPHP_PLUGINS_DIR."/cache/Cache_File.php");
					self::$_caches[$key] = new Cache_File;
				} elseif ($engine=="memcache"){
					require_once(SLIGHTPHP_PLUGINS_DIR."/cache/Cache_MemCache.php");
					self::$_caches[$key] = new Cache_MemCache;
					self::$_caches[$key]->addServers(self::getConfig($zone));
				}	
			}
			$this->_engine = self::$_caches[$key];
		}
	}
	
	static function setConfigFile($file){
		self::$_config = $file;
	}
	static function getConfig($zone=null){
		$config = SConfig::getConfig(self::$_config,$zone);
		return $config;
	}
	
	
	public function __call($name,$args){
		if ($this->_engine){
			if(!method_exists($this->_engine,$name)){
				return false;
			}
			return call_user_func_array(array(&$this->_engine,$name),$args);
		} else {
			return false;
		}
	}
}
