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
if(!defined("SLIGHTPHP_PLUGINS_DIR"))define("SLIGHTPHP_PLUGINS_DIR",dirname(__FILE__));
require_once(SLIGHTPHP_PLUGINS_DIR."/SConfig.class.php");
require_once(SLIGHTPHP_PLUGINS_DIR."/db/DbData.php");
require_once(SLIGHTPHP_PLUGINS_DIR."/db/DbObject.php");
/**
 * @package SlightPHP
 */
class SDb{
private static $_config;//数据库配置文件路径,从单入口文件获取
	static $engines=array("pdo_mysql");
	/**
	 * @param string $engine enum("mysql");
	 * @return DbObject
	 */
	static function getDbEngine($engine="pdo_mysql"){
		$engine = strtolower($engine);
		if(!in_array($engine,SDb::$engines)){
			return false;
		}
		if($engine=="pdo_mysql"){
			require_once(SLIGHTPHP_PLUGINS_DIR."/db/Db_PDO.php");
			return new Db_PDO("mysql");
		} else {
			return false;
		}
	}
	
	static function setConfigFile($file){
		self::$_config = $file;
	}
	/**
	 * @param string $zone 表前缀
	 * @param string $type	main|query 
	 * @return array
	 */
	static function getConfig($zone=null,$type="main"){
		$config = SConfig::getConfig(self::$_config,$zone);
		if(isset($config->$type)){
			return $config->$type;
		}elseif(isset($config->main)){
			return $config->main;
		}
		return;
	}
}
?>
