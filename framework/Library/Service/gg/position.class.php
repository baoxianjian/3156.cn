<?php
 /**
* @name: 广告位的服务层 
* @author: baoxianjian
* @date: 22:04 2015/4/16
*/

define('DEFAULT_LINK_URL','http://www.3156.test');
define('DEFAULT_IMG_SRC','http://www.3156.test/images/xin_top/logo.gif');


class Service_gg_position extends Base_service{
	//判断参数是否为1
	function getRowsetAdsAll($srow)
    {
        $mdl_ggp = new Model_ggposition_ggposition(); 
        
        if(defined('DEBUG'))
        {
            $mdl_ggp->setCache(true);                
        }
         
        $ad_list=$mdl_ggp->getRowsetAdsAll($srow); //首页
        
        #找出空的数据
        foreach ($ad_list as $v)
        {
            if(!$v['ggq_id'])
            {
                $ad_list_null[$v['ggp_id']]=$v;
            }
        }
        
        $ad_null_ids=array();
        #将空广告位取出，反查数据库，找到最近的广告单
        foreach ($ad_list_null as $v)
        {
            array_push($ad_null_ids,$v['ggp_id']);
        }
        $ad_null_ids_str=implode(',',$ad_null_ids);
        
        #得到这些空广告位的最近广告信息
        $ad_list2=$mdl_ggp->getRowsetAdsLast($ad_null_ids_str);
        
        #数据重组/分类
        foreach ($ad_list as $v) {
            $v['html']=$this->_templateConvert($v);
            $ad_list_temp[$v['ggpg_id2']][$v['ggp_id']]=$v;
        }
                                                  
      // print_r($ad_list_temp);     
        
        #数据重组/分类
        foreach ($ad_list2 as $v) {
            $v['html']=$this->_templateConvert($v);
            $ad_list_temp[$v['ggpg_id2']][$v['ggp_id']]=$v;
        }
      // print_r($ad_list_temp);

        return $ad_list_temp;
	}
    
    
    private function _templateConvert($row)
    {
        if(!$v=$row){return '';} 
         //------------------->这个应该放在数据层    

        if(!$v['ggq_id'])
        {
            $v['slogan']='slogan';
            $v['link_url']=DEFAULT_LINK_URL;
            $v['src']=DEFAULT_IMG_SRC;
            //$v['slogan']
           // $v['width']=150;
           // $v['height']=60;
            //$v['ggp_id']=0;    
            
        }

        $v['code']=html_entity_decode($v['code']);
        $v['code']=html_entity_decode($v['code']);
        
        $a=array('{$title}','{$linkurl}','{$imgurl}','{$alt}','{$width}','{$height}','{$placeid}');
        
        $linkurl=GOTO_URL.
        '-url-'.$v['link_url'].
        '-id-'.intval($v['ggp_id']).
        '-pid-'.intval($v['ggpg_id']).
        '-gid-'.intval($v['ggpg_id2']).
        '-oid-'.intval($v['ggq_id']);
                     
        $b=array($v['title'],$linkurl,$v['src'],$v['slogan'],$v['width'],$v['height'],$v['ggp_id']);   
        
        if($v['add_red']==1) //套红
        {
            $v['code']=str_replace('"{$title}"','"{|$title|}"',$v['code']);
            $v['code']=str_replace('{$title}','<font class="taohong">{$title}</font>',$v['code']);
            $v['code']=str_replace('"{|$title|}"','"{$title}"',$v['code']);
            
            $v['code']=str_replace('"{$alt}"','"{|$alt|}"',$v['code']);
            $v['code']=str_replace('{$alt}','<font class="taohong">{$alt}</font>',$v['code']);
            $v['code']=str_replace('"{|$alt|}"','"{$alt}"',$v['code']);
        }
        
        $html=str_replace($a,$b,$v['code']);

        return $html; 
    }
} 
