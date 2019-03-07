<?php
/**
* @name: 药品招商
* @author: baoxianjian
* @date: 10:14 2015/4/24
*/
define('SYS_NAME','');

class Controller_yz extends Controller_basepage {
		
	/**
	 * 药品招商首页
	 * @param unknown $inPath
	 * @return Ambigous <string, void, string>
	 */
	function pageIndex($inPath){
        define('AD_PAGE_ID',141);  
        
      //  print_r($this->request);exit;
       // print_r($ad_list);
       
        $title='药妆招商_药妆招商产品大全_全国药品网-3156医药网';
        $keywords='药妆招商,药妆代理,药妆品牌,药妆加盟,药妆店加盟,药妆药品信息,日本药妆,韩国药妆,3156药妆招商网';
        $description='3156药妆招商网是一家专业的药妆药品招商平台,提供全面的药妆招商信息和药妆生产企业信息、药妆化妆品品牌,欢迎医药代理商前来咨询和洽谈。';
        $this->setSEO($title,$keywords,$description);
        
		return $this->template();
		
	}   
           
           
  
    
    
}


