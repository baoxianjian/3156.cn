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
require_once(SLIGHTPHP_PLUGINS_DIR.DIRECTORY_SEPARATOR."smarty3/Smarty.class.php");
/**
 * @package SlightPHP
 */
class STpl{
	static $engine;
	/**
	 * render a .tpl
	 */
	public function render($tpl,$parames=array()){
		if(!self::$engine){
			self::$engine = new Smarty;
			self::$engine->plugins_dir = array(SMARTY_DIR."/plugins_slightphp/",SMARTY_DIR."/plugins/");
		}
		self::$engine->compile_dir = SlightPHP::getAppDir().DIRECTORY_SEPARATOR."templates_c";
		self::$engine->template_dir= SlightPHP::getAppDir().DIRECTORY_SEPARATOR."templates";
		foreach($parames as $key=>$value){
			self::$engine->assign($key,$value);
		}
		return self::$engine->fetch($tpl);
		
	}

	/**
	
	13883981903
	
	
	 * 加载框架下的tpl文件夹内的公共模板
	 */
	public function renderCommon($tpl,$parames=array()){
		if(!self::$engine){
			self::$engine = new Smarty;
			self::$engine->plugins_dir = array(SMARTY_DIR."/plugins_slightphp/",SMARTY_DIR."/plugins/");
		}
		self::$engine->compile_dir = COMTPL_DIR.DIRECTORY_SEPARATOR."templates_c";
		self::$engine->template_dir= COMTPL_DIR.DIRECTORY_SEPARATOR."templates";
		foreach($parames as $key=>$value){
			self::$engine->assign($key,$value);
		} 
		return self::$engine->fetch($tpl);
	}
	/**
	 * 302 redirect
	 */
	public function redirect($url) {
		header('Location:'.$url);
		exit;
	}
	
	public function ass($key,$value,$explode=true){
		if(!self::$engine){
			self::$engine = new Smarty;
			self::$engine->plugins_dir = array(SMARTY_DIR."/plugins_slightphp/",SMARTY_DIR."/plugins/");		
		}
		self::$engine->compile_dir = SlightPHP::getAppDir().DIRECTORY_SEPARATOR."templates_c";
		self::$engine->template_dir= SlightPHP::getAppDir().DIRECTORY_SEPARATOR."templates";
		self::$engine->assign($key,$value);
	}
}
?>
