<?php
/**
* @name: 药品招商
* @author: baoxianjian
* @date: 10:14 2015/4/24
*/
define('SYS_NAME','');

class Controller_yydatabase extends Controller_basepage {
		
	/**
	 * 药品招商首页
	 * @param unknown $inPath
	 * @return Ambigous <string, void, string>
	 */
	function pageIndex($inPath){
        define('AD_PAGE_ID',104);  
        
      //  print_r($this->request);exit;
       // print_r($ad_list);
       

        
		return $this->template();
		
	}   
           
           
  
    
    
}


