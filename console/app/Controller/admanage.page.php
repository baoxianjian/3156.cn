<?php
	
	/**
	 * 广告管理控制器
	 * @author zhangqijun
	 *
	 */

 	Class Controller_admanage extends Controller_basepage {
 		
 		/**
 		 * 分组管理首页视图
 		 * @return Ambigous <mixed, string, void, string>
 		 */
 		public function pageIndex(){
 			//die("222");
 			return $this->template();
 		}
 		
 		/**
 		 * 添加（编辑）分组视图
 		 * @param unknown $inPath
 		 * @return Ambigous <mixed, string, void, string>
 		 */
 		public function pageaddgroup($inPath){
 			return $this->template();
 		}
 		
 	}