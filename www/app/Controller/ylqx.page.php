<?php
/**
* @name: 药品招商
* @author: baoxianjian
* @date: 10:14 2015/4/24
*/
define('SYS_NAME','');

class Controller_ylqx extends Controller_basepage {
		
	/**
	 * 药品招商首页
	 * @param unknown $inPath
	 * @return Ambigous <string, void, string>
	 */
	function pageIndex($inPath){
        define('AD_PAGE_ID',152);
          
        
      //  print_r($this->request);exit;
       // print_r($ad_list);
       
        $title='医疗器械招商_医疗器械公司_医疗器械代理加盟_全国药品网-3156医药网';
        $keywords='医疗器械招商网,医疗器械代理加盟,医疗器械产品,医疗器械公司,医疗器械招商代理信息,国产医疗器械,进口医疗器械,治疗型医疗器械,全国医疗器械招商网';
        $description='3156医疗器械招商网提供最新的进口国产医疗器械招商代理信息,其中包含最新品牌医疗器械、医用医疗器械、治疗型医疗器械、国产医疗器械、进口医疗器械、医疗器械销售、医疗器械招信息。';
        $this->setSEO($title,$keywords,$description);

		return $this->template();
		
	}   
           
           
  
    
    
}


