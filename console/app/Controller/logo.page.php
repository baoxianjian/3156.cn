<?php
/**
 * 用户登录控制器
 * @author tong
 * @time 2014-2-11 11:28:13 
 */

header("Content-type:text/html;charset=utf8");
class Controller_logo extends Controller_basepage {
		
	/**
	 * 前端首页视图方法
	 * @param unknown $inPath
	 * @return Ambigous <string, void, string>
	 */
	function pageIndex($inPath){
		
		return $this->render("main/index.html");
		
	}
	
} 