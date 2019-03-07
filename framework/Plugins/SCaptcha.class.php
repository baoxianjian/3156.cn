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


require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."captcha/captcha.php");
session_start();
/**
 * @package SlightPHP
 */
class SCaptcha extends SimpleCaptcha{
	static $session_prefix="capcha_seed_";
	function __construct(){
		$this->wordsFile = "";
		$this->session_var = SCaptcha::$session_prefix ;
		$this->minWordLength = 4;
		$this->maxWordLength = 5;
		$this->width = 100;
		$this->height = 35;
		$this->Yamplitude = 12;
		$this->Xamplitude = 5;
		$this->scale=2;
		$this->blur = false;
		$this->imageFormat="png";
		$this->transprent=true;
	}
	static function check($captcha_code){
		//还是要修改成memcache的形式
		if(	empty($_SESSION[SCaptcha::$session_prefix . $captcha_code]) ||
			$_SESSION[SCaptcha::$session_prefix . $captcha_code] != $captcha_code
		){
			return false;
		}else{
			return true;
		}
	}
	static function del($captcha_code){
		if(	!empty($_SESSION[SCaptcha::$session_prefix . $captcha_code])){
			unset ($_SESSION[SCaptcha::$session_prefix . $captcha_code]);
		}
	}
		

}
?>