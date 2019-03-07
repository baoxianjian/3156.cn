<?php
/**
* @name: 关键字专区控制器 
* @author: baoxianjian
* @date: 9:34 2015/4/23
*/        
define('SYS_NAME','block');


class Controller_keywords extends Controller_basepage {  
     
    function pageIndex($inPath){
        /*
        $srv_bkw = new SService('Service_block_keywords');
        $list_bkw = $srv_bkw->getRowsetAll();
        
        $mdl_bkw=new Model_block_keywords();
        $list_types=$mdl_bkw->getTypes();

        
        $params['list_bkw']=$list_bkw;
        $params['list_types']=$list_types;
        */

        
        $this->buildTplFilePath('keywords.show');

        return $this->pageShow($inPath);
    }
    
    
    function pageShow($inPath)
    { 
        $s_kwm=trim($this->request['m']);
       
        if(!$s_kwm)
        {
            $this->redirect('http://www.3156.cn/404.shtml');
        }
        
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
        
        foreach ($list_bkd as $v) 
        {
            $list_bkd_temp[$v['area']][]=$v;
        }
        
        
        //$bkw_name
        $relative_pdt_list = $mdl_bkw->searchListAll(1,array('kw'=>$bkw_name),10);
 

        $mark_selected[$s_kwm]='class="choose"';
        
        $params['bkw_name']=$bkw_name; 
        $params['list_bkw']=$list_bkw;
        $params['list_types']=$list_types;
        $params['list_bkd']=$list_bkd_temp;
        $params['relative_pdt_list']=$relative_pdt_list['list'];
        
        
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
        return WWW_URL.'/about-id-'.$row['cmp_id'];
    }


}