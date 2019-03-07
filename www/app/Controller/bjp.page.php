<?php
/**
* @name: 药品招商
* @author: baoxianjian
* @date: 10:14 2015/4/24
*/
define('SYS_NAME','');

class Controller_bjp extends Controller_basepage {
		
	/**
	 * 药品招商首页
	 * @param unknown $inPath
	 * @return Ambigous <string, void, string>
	 */
	function pageIndex($inPath){
        define('AD_PAGE_ID',122);  
        
      //  print_r($this->request);exit;
       // print_r($ad_list);
       
        $title='保健品招商_保健品招商产品大全_全国药品网-3156医药网';
        $keywords='保健品招商,保健品招商代理信息,保健品招商网,中国保健品招商网,国药食字保健品,卫食健字保健品,保健品招商网';
        $description='3156医药网提供最新的国药食字保健品卫食健字保健品招商代理信息,保健品招网频道发布最新国药食字保健品、卫食健字保健品、保健医疗器械、炒作保健品、会销保健品、保健品招商代理信息。';
        $this->setSEO($title,$keywords,$description);
        
		return $this->template();
		
	}   
           
           
  
    
    
}


