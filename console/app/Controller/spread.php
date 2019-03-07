<?php
  /**
* @name: 后台推广控制器
* @author: baoxianjian
* @date: 09:32 2015/3/21
*/

class Controller_main extends Controller_basepage {
		
	/**
	 * 后台首页
	 * @param unknown $inPath
	 * @return Ambigous <string, void, string>
	 */
	function pageIndex($inPath){
		return $this->render("spread/index.html");
	}


    
} 