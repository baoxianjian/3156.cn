<?php
/**
* @name: 药品招商
* @author: baoxianjian
* @date: 10:14 2015/4/24
*/
define('SYS_NAME','');

class Controller_yyzs extends Controller_basepage {
		
	/**
	 * 药品招商首页
	 * @param unknown $inPath
	 * @return Ambigous <string, void, string>
	 */
	function pageIndex($inPath){
        define('AD_PAGE_ID',54);  
        
      //  print_r($this->request);exit;
       // print_r($ad_list);
       
        $title='火爆医药招商_全国药品网-3156医药网';
        $keywords='医药招商,医药招商网,中国医药招商网,医药厂家招商';
        $description='3156医药网，医药行业第一门户。3156医药网是一家提供精准精确医药招商医药代理信息的医药行业网站！3156医药网从药品分类、药品剂型、科室、症状等多方面进行类别细化,为医药公司提供至尊医药招商服务！';
        $this->setSEO($title,$keywords,$description);
        
		return $this->template();
		
	}   
           
           
  
    
    
}


