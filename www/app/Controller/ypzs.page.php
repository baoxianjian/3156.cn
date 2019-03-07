<?php
/**
* @name: 药品招商
* @author: baoxianjian
* @date: 10:14 2015/4/24
*/
define('SYS_NAME','');

class Controller_ypzs extends Controller_basepage {
		
	/**
	 * 药品招商首页
	 * @param unknown $inPath
	 * @return Ambigous <string, void, string>
	 */
	function pageIndex($inPath){
        define('AD_PAGE_ID',83);  
        
      //  print_r($this->request);exit;
       // print_r($ad_list);
        
        $ru=$this->getCompanyFromSession(0);
        $cid=$ru['cid'];
        $uid=$ru['uid'];
        $this->ass('cid',$cid);
        $this->ass('uid',$uid);
        
        
        $title="独家药品招商_全国药品网-3156医药网";
        $keywords="独家药品招商,药品招商,药品招商代理,中药,OTC,保健品,医疗器械,炒作药品招商网,中国药品招商网";
        $description="3156医药网是一家专业的药品招商平台,网站提供综合的药品招商、药品代理和药品生产企业信息,为药品厂家和药品代理商搭建了一个沟通和交流的平台。";
        $this->setSEO($title,$keywords,$description);
        
        
        //通过pdt_type表得到产品类型。
        $mdl_pdtype=new Model_pdt_pdtTypes();
        $list_pdtype=$mdl_pdtype->getListAlltp();
        
        foreach ($list_pdtype['list'] as $v)
        {
        	$list_pdtype_temp[$v['parent_id']][]=$v;
        }        	        
        $this->ass('list_pdtype',$list_pdtype_temp);
        
        //得到药剂类型数组
        $mdl_products = new Model_pdt_mainProducts();
        $medicamentType = $mdl_products->getMedicamentTypes();
        $this->ass('medicamenttype',$medicamentType);
        
        
        
		return $this->template();
		
	}   
           
           
  
    
    
}


