<?php
/**
* @name: 药品招商
* @author: baoxianjian
* @date: 10:14 2015/4/24
*/
define('SYS_NAME','');

class Controller_otc extends Controller_basepage {
		
	/**
	 * 药品招商首页
	 * @param unknown $inPath
	 * @return Ambigous <string, void, string>
	 */
	function pageIndex($inPath){
        define('AD_PAGE_ID',104);  
       
        
      //  print_r($this->request);exit;
       // print_r($ad_list);
        $title="OTC招商_非处方药招商_全国药品网-3156医药网";
        $keywords="OTC药品招商,OTC医药招商,OTC交易,OTC市场,OTC招商,OTC代理,非处方药招商,季节性热门OTC药品,西药OTC,中药OTC,OTC招商网";
        $description="3156医药网提供最新中药OTC、西药OTC、药品招商代理信息、季节性热门OTC、非处方OTC药品招商代理信息,甲类和乙类OTC医药招商信息。";
        $this->setSEO($title,$keywords,$description);


        
        
		return $this->template();
		
	}   
           
           
  
    
    
}


