<?php
/**
* @name: 广告位的服务层 
* @author: baoxianjian
* @date: 11:31 2015/4/20
*/
//命令解析器
function cmd_parser($cmd)
{
    if(!$cmd=trim($cmd)){return array();}
    $ps=explode(',',$cmd); //params
    if(!$ps){return array();}
    
    foreach ($ps as $v)
    {
        $v=explode(':',$v);   
        if(strpos($v[1],'-'))
        {
            $v[1]=explode('-',$v[1]);
        }
        $params[$v[0]]=$v[1];
    }
    return $params;   
}



   
function gg($cmd)
{
    global $srv_ggp,$ads;   
//    $cmd='pid:1,gid:2,id:3-5,not:1-2-3';

    /*
    [pid] => 1
    [gid] => Array
        (
            [0] => 2
            [1] => 3
        )

    [id] => Array
        (
            [0] => 3
            [1] => 5
        )

    [not] => Array
        (
            [0] => 1
            [1] => 2
            [2] => 3
        )
    */

   // print_r($params);exit;
    

    if(!$cmd || $cmd=='help')
    {
        echo <<<EOT
调用方法：gg('pid:1,gid:2,id:3-5,not:1-2-3');
pid:页面ID(如果不指明，则默认当前页面ID)；gid:分组ID；id:广告位ID，not:要排除的id
示例，假如首页ID为1，分组ID为2，广告位为1,2,3,4,5
1.调用首页下分组ID为2下的所有广告:{|gg('pid:1,gid:2')|},简写：{|gg('gid:2')|}
2.调用首页下分组ID为2下的所有广告，但要排除广告位为3的所有广告:{|gg('pid:1,gid:2,not:3')|},简写：{|gg('gid:2,not:3')|}
3.调用首页下分组ID为2下的所有广告，但要排除广告位为3,5的所有广告:{|gg('pid:1,gid:2,not:3-5')|},简写：{|gg('gid:2,not:3-5')|}
4.调用广告位ID为3广告:{|gg('id:3')|}
5.同时调用广告位ID为1,2,3的广告:{|gg('id:1-2-3')|}  
;
EOT;
        exit;
    }

    if(!$srv_ggp)
    {
        $srv_ggp = new SService('Service_gg_position');    
    }
    
    $params=cmd_parser($cmd);
    $id=$params['id'];
    $pid=$params['pid'] ? $params['pid'] : AD_PAGE_ID;
    $gid=$params['gid'];
    $not=$params['not'];

    if($id)
    {    
        if(is_array($id)){$is_list=1;}
        $ads_temp=$srv_ggp->getRowsetAdsAll(array('id'=>$id));
    }
    else
    {
        if($pid)
        {
            if(!$ads[$pid])
            {
                //进入查询缓存
                $ads[$pid]=$srv_ggp->getRowsetAdsAll(array('pid'=>$pid));
    
                //只有首页才有:精品刷新招商区
                if($pid==1)
                {
                      //$ref_group_ids = array(44,45,46,47);
                      $ref_group_ids = explode(',',GG_REFRESH_GROUP_IDS);
                      
                      $ref_group_list = array();
                      
                      foreach ($ref_group_ids as $v_gid)
                      {
                         // $ref_ads_count_temp=count($ads[$pid][$v_gid]);
                          
                          //分离出索引和记录
                          foreach ($ads[$pid][$v_gid] as $v)
                          {
                              $ref_group_list[$v['ggp_id']]=$v;
                              //索引按素材类型分组
                              $ad_group_idx[$v['ggm_type']][$v['ggp_id']]=$v['refresh_time'];  
                              //$ad_group_info[$v['ggm_type']][$v_gid]=$ref_ads_count_temp;
                          }  
                      }
                      
                      //个数定死  
                      $ad_group_info[2][44]=14;
                      $ad_group_info[2][45]=7;
                      $ad_group_info[1][46]=24;
                      $ad_group_info[1][47]=24;
                             //        2          2         1         1
                       //Array ( [44] => 14 [45] => 7 [46] => 24 [47] => 24 ) 
		       
		            
                       #将索引排序
                       arsort($ad_group_idx[1]); //文字分组排序
                       arsort($ad_group_idx[2]); //图片分组排序
                      
                       foreach ($ad_group_idx as $k_type=>$idx)
                       {
                           foreach ($idx as $k=>$v)  //k为ggp_id v为时间
                           {
                               $row = $ref_group_list[$k];   //按索引找到对应记录

                               //将该条记录分发到同种类型的组里
                               foreach ($ad_group_info[$k_type] as $k_gid=>$v_count) 
                               {

                                   //大于改组的容量时，就赋给下一个同种类型的组里
                                   if($counter[$k_gid] >= $v_count)
                                   {
                                       //unset掉，下次就不会再次做分发判断
                                       unset($ad_group_info[$k_type][$k_gid]);
                                       continue;
                                   }
                                   
                                   $ref_group_temp[$k_gid][$k]= $row;
                                   $counter[$k_gid]++;
                                   break;
                               }
                           }
                       }
                       
                       //将排序后的广告放回原处
                       foreach ($ref_group_temp as $k=>$v)
                       {
                           $ads[$pid][$k]=$v;
                       }
                }
            }   
        }
        if($gid)
        {
            //缓存没得，单独查询
            $is_list=1;   
            if($ads[$pid][$gid])
            {
                $ads_temp=$ads[$pid][$gid];
            }
            else
            {
                 $ads_temp=$ads[$pid][$gid]= $srv_ggp->getRowsetAdsAll(array('gid'=>$gid)); 
            }
        }
    }



    $html=''; 
    if(!is_array($not)){$not=array($not);}    
    if($is_list) //调组
    {
         foreach ($ads_temp as $v)
        {
            
            if(!in_array($v['ggp_id'],$not))
            {
                $html.=$v['html'];
            }
        }
    }
    else  //调id
    {
        foreach ($ads_temp as $v)
        {   
            foreach ($v as $v2) 
            {
               $html.=$v2['html'];  
            } 
        }

    }

    return $html;
//  return $ads[$page][$group][$place]['html'];
}

function block($cmd,$update=true,$fs=false)
{   
    if($cmd=='help')
    {
    echo <<<EOT
调用方法：block('id:1,did:110,count:2');
示例1.调区块ID为7里的信息：block('id:7')
示例2.调区块ID为7里的信息，且需要8条：block('id:7,count:8')
示例3.直接调数据ID为4的信息：block('did:110');
EOT;
        exit;
    }

    $params=cmd_parser($cmd); 
    
    if(!$params)
    {
    return <<<EOT
<!--block call error,cmd:{$cmd} -->
EOT;
    }
    
    
    $id=$params['id'];
    $did=$params['did'];
    $count=$params['count'];
    
    
    if(!$srv_bb)
    {
        $srv_bb = new SService('Service_block_blocks');    
    }

    $need_save = false;

    //只有在静态化模式下，才判断是否需要保存
    if(WEB_STATIC==1)
    {
        //应该判断缓存时间 这些
        $fn=md5($cmd);
        $vn='/blocks/'.$fn.'.shtml';  //virtual name 
        //$fn1='C:/apache2.4/htdocs/3156.test/'.APP_NAME.'/blocks/'.$fn.'.shtml';
        //file_put_contents($fn1,$html);
        
        $fn2=BLOCK_ORIGIN_DIR.'/'.$fn.'.shtml';
        
        
        if($fs) //强制更新
        {
            $need_save = true;
        }
        else
        {
            if(file_exists($fn2))
            {
                $up_time = filemtime($fn2);
                if(NOW - $up_time > BLOCK_MAX_EXPIRY_TIME) //过期时间超过一天
                {
                     $need_save = true;
                }
            }
            else
            {
                $need_save = true;
            }
        }   
    }
    
    //在动态浏览或需要保存时，才需要区块的html
    if(!WEB_STATIC || $need_save)
    {
        $list = $srv_bb->getRowsetDataAll($params);
        $html='';  
        
        //如果不是数组（代码块）
        if(is_array($list))
        {
            foreach ($list as $v)
            {
                $html.=$v['html'];
            }
        }
        else
        {
            $html=$list;
        }
    }
   
    //更新调用状态
    if($update)
    {
        $srv_bb->updateToSB($id,$cmd);  
    }
    
    //数据已经准备好，需要保存就保存
    if($need_save)
    {   
        file_put_contents($fn2,$html);
        if(defined('BLOCK_UPDATE_OUTPUT'))
        {
            echo 'cmd:'.$cmd,'<br/>';
            echo 'block file has saved to:'.$fn2,'<br/>';
        }
    }
   
    //静态时，$fn2肯定有值，就返回shtml的include
    if(WEB_STATIC==1)
    {
        return '<!--x#include virtual="'.$vn.'"-->';
    }

    //普通html输出
    return $html; 
}

