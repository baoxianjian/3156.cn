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
class SRoute{
	private static $_RouteConfigFile;
	private static $_Routes=array();
	static function setConfigFile($file,$zone=''){
		self::$_RouteConfigFile= $file;
		self::$_Routes = SConfig::getConfig(self::$_RouteConfigFile,$zone);
		//var_dump(self::$_Routes);
		self::parse();
	}
	static function getConfigFile(){
		return self::$_RouteConfigFile;
	}
	private static function parse(){
		if (self::$_Routes){
			$splitFlag = SlightPHP::getSplitFlag();
			$splitFlag = $splitFlag{0};
			if(!empty($_GET['PATH_INFO'])){
				$PATH_INFO = $_GET['PATH_INFO'];
			}else{
				$PATH_INFO = !empty($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:$_SERVER['REQUEST_URI'];
			}
			//var_dump($PATH_INFO);
			foreach (self::$_Routes as $route){
				//print_r($route);
				if (!is_array($route)){
					$arr = array();
					$arr[] = $route;
					$route = $arr;
				}
				//var_dump($route);
				foreach($route as $value){
					$pattern = '/'.$value->come_pattern.'/sm';
					$replacement = $value->come_replacement;
					//var_dump($pattern);
					//var_dump($PATH_INFO);
					if (preg_match($pattern, $PATH_INFO)){
						$PATH_INFO = preg_replace($pattern, $replacement, $PATH_INFO);
						SlightPHP::setPathinfo($PATH_INFO);
						break;
					}
				}
			}
		}
	}
	
	/**
	 * 生成URL
	 * @param unknown $route 路由
	 * @param unknown $params 添加参数
	 * @param unknown $pars 原来参数
	 * @param string $domain 域名
	 * @return string|Ambigous <string, mixed>
	 */
	public static function createUrl($route,$params=array(),$pars=array(),$domain=""){
		$splitFlagAll = SlightPHP::getSplitFlag();
		$splitFlag = $splitFlagAll{0};
        if($route){$url = '/'.rtrim(trim($route,'/'),$splitFlagAll);}
		if (!empty($pars)){
			if (!is_array($pars)){
				$pars = self::_parse_str($pars);
			}
			$params = array_merge($params,$pars);
		}
		if(!empty($params)) {
			if (!is_array($params)){
				$params = self::_parse_str($params);
			}
			foreach($params as $key=>$value) {
				$tmp.= $key.$splitFlag.$value.$splitFlag;
			}
			$tmp = rtrim($tmp,$splitFlagAll);
			$fvar = substr($url,strlen($url)-1,1);
			if($fvar == '/') {
				$url=rtrim($url.$tmp,$splitFlagAll);
			} else {
				$url=rtrim($url.$splitFlag.$tmp,$splitFlagAll);
			}
		}

		//根据URL规则重写
		if (!empty($domain)){
			self::$_Routes = SConfig::getConfig(self::$_RouteConfigFile,$domain);
		}
		
        if($routeFormat = trim($route,'/'))
        {
		    $rule = self::$_Routes->$routeFormat;
		    if (!empty($rule)){
			    $pattern = $rule->go_pattern;
			    $replacement = $rule->go_replacement;
			    if (preg_match('/'.$pattern.'/i', $url)){//匹配到就结束
				    if (!empty($replacement)){
					    $url = preg_replace('/'.$pattern.'/i', $replacement, $url);
				    }
			    }
		    }
        }
		
		//$url = preg_replace('/([\/\-\.]{1})[\/\-\.]+/', "$1", $url); //去除多余的分隔符，保留第一个
		if ($domain){
			$const_name = constant(strtoupper($domain).'_URL');
			return $const_name.$url;
		} else {
			return $url;
		}
	}
	
	public static function _parse_str($str){
		$params = explode('&', $str);
		$result = array();
		foreach ($params as $param){
			$single = explode('=', $param);
			if ($single[1] !== null){
				$result[$single[0]] = $single[1];
			}
		}
		
		return $result;
	}
}
