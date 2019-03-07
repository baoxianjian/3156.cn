<?php
/**
* @name: 3156首页
* @author: baoxianjian
* @date: 20:44 2015/3/28
*/

define('SYS_NAME','');
  header('Content-Type: text/html; charset=utf-8');
class Controller_main extends Controller_basepage {
	/**
	 * 后台首页
	 * @param unknown $inPath
	 * @return Ambigous <string, void, string>
	 */
	function pageIndex($inPath){
        define('AD_PAGE_ID',1); 
                  
            $mdl_area = new Model_com_area();
            //一级
            $list = $mdl_area->getAllRowset(0,0); 
            $mdl_area->buildAreaSql($list,1);
             echo "\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n";
             //二级
            $list = $mdl_area->getAllRowset(0,1); 
            $mdl_area->buildAreaSql($list,2);

            //print_r($list);
                       
            //$mdl_area->buildAreaSql($list);
            exit;
                  
   

      //  print_r($this->request);exit;
       // print_r($ad_list);
       
        //$a= block('id:7,index:0,row:name');    
        //$a= block('id:7,index:0,row:linkurl');    
       // $a= block('id:47');    
       // print_r($a);
       
        //exit;
        $title='医药招商_药品招商_药品代理_医药行业第一门户_全国药品网-3156医药网';
        $keywords='3156医药网,医药招商,药品招商,医药代理,OTC,保健品,医疗器械,药妆';
        $description='3156医药网,医药行业第一门户。3156医药网是一家提供精准精确医药招商、药品招商、医药代理信息的医药行业网站！3156提供包括医药招商、保健品招商、医疗器械招商、OTC招商、药品招商、医药代理、药品代理、药交会、药展会、医药资讯等最新信息,为医药公司和代理商提供至尊药品招商和药名代理服务!';
        $this->setSEO($title,$keywords,$description);
       
       block('id:33');

        //得到一级药品类型
        $mdl_pdtype=new Model_pdt_pdtTypes();
        $data=$mdl_pdtype->getListAlltypeone($page);
        $list=$data['list'];
        $this->ass('list', $list);
 
        
        
		return $this->template();
		
	}
	
	//发布成功
     function pageSuccess($inPath){
         
         
     	$srv_bkw = new SService('Service_block_keywords');
     	$mdl_bkw=new Model_block_keywords();
     	$mdl_bkd=new Model_block_keydata();
     	$mdl_pdt=new Model_pdt_products();
     	
     	$list_bkw = $srv_bkw->getRowsetAll();
     	$list_types=$mdl_bkw->getTypes();
     	
     	$bkw_name=$mdl_bkw->getNameByMark($s_kwm);
     	
     	$srow=array('kwm'=>$s_kwm);
     	
     	$list_bkd=$mdl_bkd->getRowsetAll($srow);
     	
     	foreach ($list_bkd as $k=>$v)
     	{
     		$list_bkd[$k]['cmp_static_url']=$this->buildGotoUrl($v);
     		$rowset = $mdl_pdt->getRowsetByCmpId($v['cmp_id']);
     		 
     		foreach ($rowset as $k2=>$v2) {
     			$rowset[$k2]['static_url']=$this->buildGotoUrl($v2);
     		}
     		 
     		$list_bkd[$k]['recruit']=$rowset;
     	}
     	
     	
     	
     	$mark_selected[$s_kwm]='class="choose"';
     	
     	$params['bkw_name']=$bkw_name;
     	$params['list_bkw']=$list_bkw;
     	$params['list_types']=$list_types;
     	$params['list_bkd']=$list_bkd;
     	$params['mark_selected']=$mark_selected;
     	
     	
     	$title="{$bkw_name}药品招商代理信息_{$bkw_name}医药招商企业信息_全国药品网-3156医药网";
     	$keywords="{$bkw_name}药品招商,{$bkw_name}医药招商,{$bkw_name}药品代理,{$bkw_name}企业信息,3156药品招商网";
     	$description="3156医药网是一家专业的{$bkw_name}药品招商信息发布平台,提供全面的{$bkw_name}药品招商代理信息和{$bkw_name}医药招商企业药品信息,为药品生产企业和药品代理商搭建一座沟通的桥梁,欢迎医药代理商前来咨询和洽谈。";
     	$this->setSEO($title,$keywords,$description);     
       return $this->template($params);
     }
	 
	 //组合goto路径
     private function buildGotoUrl($row)
     {  
     	return GOTO_URL.
     	'-url-http://www.3156.cn/'.$row['cmp_static_url'].
     	'-mid-2'.
     	'-ktid-'.$row['bkw_type'].
     	'-kwid-'.$row['bkw_id'].
     	'-kdid-'.$row['bkd_id'];  
     }
             
}



