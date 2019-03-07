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
class SMongodb{
	public function __construct($zone='main'){
		//修改默认配置
		$this->useConfig($zone);
		return self::$_db;
	}
	private static $_config;
	public static $_db;
	
	static function setConfigFile($file){
		self::$_config = $file;
	}
	static function getConfig($zone=null){
		return SConfig::getConfig(self::$_config,$zone);
	}
	//暂不支持账户密码；
	public function useConfig($zone){
		$hosts='';
		$cfg = self::getConfig($zone);
		if(empty($cfg))$cfg=self::getConfig("main");
		if(!is_array($cfg)){
			$tmp = $cfg;
			unset($cfg);
			$cfg[] = $tmp;
		}
		if(!empty($cfg)){
			$i = 0;
			$hosts = 'mongodb://';
			foreach($cfg as $host){
				$hosts .=($i>0?',':'').$host->host.":".$host->port;
				$i++;
			}
		}
		self::$_db = new MongoClient($hosts, array('connect'=>true));
	}
}
