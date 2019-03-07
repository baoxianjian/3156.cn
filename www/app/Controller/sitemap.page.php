<?php
/**
* @name: 网站地图控制器 
* @author: baoxianjian
* @date: 16:36 2015/4/6
*/        
define('SYS_NAME','sitemap');


class Controller_sitemap extends Controller_basepage {
        
      //公司xml   
        function pageCompanyXML($inPath){
    	  
        $page=$this->getPageNumber();
        $limit=intval($this->request['limit']);
        //$this->
    	//公司model
        $mdl_company = new Model_cmp_company();            
	   	//获取列表数据	           		   	   
             			   		  	   	
       	$data = $mdl_company->getSitemapAll($page,$limit);
        
        #列表页的412检测
        $this->pageBarS($data['count'], $page, '/list-{$page}.xml','list-1.xml',$limit);
        
       	$this->ass('list',$data['list']);
                 	       	
        return $this->template();
    }
    
    
    //产品xml
      function pageProductXML($inPath){
          
        $page=$this->getPageNumber();
        $limit=intval($this->request['limit']);
        //$this->
        //公司model
        $mdl_pdt = new Model_pdt_products();          
           //获取列表数据                                 
                                                 
        $data = $mdl_pdt->getSitemapAll($page,$limit); 
        
        #列表页的412检测
        $this->pageBarS($data['count'], $page, '/list-{$page}.xml','list-1.xml',$limit);
        
        $this->ass('list',$data['list']);                        
        return $this->template();
    }

	//资讯xml
      function pageZixunXML($inPath){
          
        $page=$this->getPageNumber();
        $limit=intval($this->request['limit']);
        //$this->
        //公司model
        $mdl_zx = new Model_news_news();          
           //获取列表数据                                 
                                                 
        $data = $mdl_zx->getSitemapAll($page,$limit);
                         
        #列表页的412检测
        $this->pageBarS($data['count'], $page, '/list-{$page}.xml','list-1.xml',$limit);
   
        $this->ass('list',$data['list']);                        
        return $this->template();
    }
	
    //招商xml
      function pageZhaoshangXML($inPath){
          
        $page=$this->getPageNumber();
        $limit=intval($this->request['limit']);
        //$this->
        //公司model
        $mdl_zs = new Model_cmp_company();        
           //获取列表数据                                 
                                                 
        $data = $mdl_zs->getzhaoshangAll($page,$limit);
        
        #列表页的412检测
        $this->pageBarS($data['count'], $page, '/list-{$page}.xml','list-1.xml',$limit);
        
        $this->ass('list',$data['list']);                        
        return $this->template();
    }
    
}