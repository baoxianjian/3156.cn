<?php
/**
* @name: 区块服务层 
* @author: baoxianjian
* @date: 17:15 2015/4/28
*/
class Service_block_blocks extends Base_service{
   
    private $mdl_bb=null; //block_blocks
    private $mdl_sb=null; //static_blocks
    private $mdl_bd=null; //block_data
    private $mdl_cmp=null; //cmp_company
    private $mdl_pdt=null; //pdt_products
    private $mdl_news=null; //news_news 
    
    private $tpl_vars=array('{$title}','{$linkurl}','{$imgurl}','{$alt}','{$width}','{$height}','{$type}','{$count}','{$time}','{$abstract}','{$slogan}','{$slogan}');  

    
    public function getRowsetDataAll($params)
    {
        $id=intval($params['id']);
        $did=intval($params['did']);
        $count=intval($params['count']);
        
        //did 数据id优先
        if($did)
        {
            $this->getModBD();
            $row_data = $this->mdl_bd->getRowById($did);
        }
        else if($id) //区块id
        {
            $this->getModBB();
            $row_block = $this->mdl_bb->getRowDataById($id);
        
            #模版相关预处理
            if($row_block['bb_type']!=1)//非文字时，把宽高解析出来
            {
                preg_match('/^([0-9]+)[\*xX]{1}([0-9]+)$/',$row_block['bb_standard'],$temp);
                $row_block['img_width']=intval($temp[1]);
                $row_block['img_height']=intval($temp[2]);
            }

            $row_block['code']=html_entity_decode($row_block['code']);
            
            if(!$count)
            {
                $count = $row_block['ds_count'];
            }
            
            switch(intval($row_block['ds_way']))
            {
                case 1: //手动
                {
                    $this->getModBD();
                    $list = $this->mdl_bd->getRowsetAll(array('bbid'=>$id),$count);
                    break;
                }
                case 2: //自动
                {
                    $srow = $this->buildSearchRow($row_block);
                    $list = $this->getRowsetByMT($row_block['ds_mod_type'],$srow,$count);
                    break;
                }
                case 3: //代码块
                {
                    return html_entity_decode($row_block['ds_html']); //直接返回
                }
                default:  //出错
                {
                    break;
                }
            }
        }
        else
        {
            //数据id 和 区块id不能同时为空
        }
        
        //模版转换
        if($row_block['bt_type']==6) //非主流（非循环）转换
        {     
            $tpl_vars_bak = $this->tpl_vars; //先备份模版变量
            
            $i=0;
            foreach ($list as $v)
            {
                foreach ($this->tpl_vars as $k2=>$v2)
                {
                    if($i==0)
                    {
                        $this->tpl_vars[$k2]=str_replace('$','$'.$i.':',$v2);
                    }
                    else
                    {
                        $this->tpl_vars[$k2]=str_replace('$'.$last_i,'$'.$i,$v2);
                    }
                }
                $row_block['code']=$this->_templateConvert($v,$row_block);
                $last_i=$i;
                $i++;
            }  
            
            return $row_block['code'];
        }
        else  //循环模版
        {
            $i=0;
            foreach ($list as $k=>$v)
            {
                $i++;
                $list[$k]['html']=$this->_templateConvert($v,$row_block,$i);
            }    
        }
        return $list;
    }
   
    public function updateToSB($id,$cmd)
    {
        $id=intval($id);
        $cmd=trim($cmd);
        if(!$id || !$cmd)
        {
            if(defined('DEBUG'))
            {
                exit('block id or cmd is null');
            }
            return false;
        }

        $app_page=CUR_PAGE.'/'.CUR_ACTION;
       // echo $cmd.APP_NAME.$app_page,'<br/>';
        $key = md5($cmd.APP_NAME.$app_page);
       // echo $key,'<br/>'; 

        $row['last_call_time']=NOW;       
        
        $this->getModSB();
        $sb_id = $this->mdl_sb->getIdBySBKey($key);
        
        
        
        if($sb_id) //存在即修改
        {                 
            $this->mdl_sb->updateRowById($row,$sb_id);
        }
        else
        {
            $row['bb_id']=$id;
            $row['sb_key']=$key;
            $row['sb_cmd']=$cmd;
            $row['app_name']=APP_NAME; 
            $row['app_page']=$app_page;
            $row['dateline']=NOW;            
            $this->mdl_sb->addRow($row);
        }
        
        
       // $this->mdl_sb->updateRowBySBKey($)
        
    } 
    
    private function buildSearchRow($rb)
    {
        switch(intval($rb['ds_rule']))
        {
            case 1: //指定ids
            {
                $srow=array('ids'=>$rb['ds_mod_ids']);
                break;
            }
            case 2: //最新
            {
                $srow=array('new'=>1,'tid1'=>$rb['ds_mod_tid1'],'tid2'=>$rb['ds_mod_tid2'],'tid3'=>$rb['ds_mod_tid3']);
                break;
            }
            case 3: //最热
            {
                $srow=array('hot'=>1,'tid1'=>$rb['ds_mod_tid1'],'tid2'=>$rb['ds_mod_tid2'],'tid3'=>$rb['ds_mod_tid3']);
                break;
            }
            case 4: //收费
            {
                $srow=array('fee'=>1,'tid1'=>$rb['ds_mod_tid1'],'tid2'=>$rb['ds_mod_tid2'],'tid3'=>$rb['ds_mod_tid3']);
                break;
            }
            default:
            {
                exit('data source rule error!');
                break;
            }
        }
        return $srow;
    }
    
    private function getModSB()
    {
        if(!$this->mdl_sb)
        {
            $this->mdl_sb=new Model_static_blocks();
        }
        return $this->mdl_sb;   
    }
    
    private function getModBB()
    {
        if(!$this->mdl_bb)
        {
            $this->mdl_bb=new Model_block_blocks();
        }
        return $this->mdl_bb;   
    }
    
    private function getModBD()
    {
        if(!$this->mdl_bd)
        {
            $this->mdl_bd=new Model_block_data();
        }
        return $this->mdl_bd;   
    }
    
    private function getModCMP()
    {
        if(!$this->mdl_cmp)
        {
            $this->mdl_cmp=new Model_cmp_company();
        }
        return $this->mdl_cmp;   
    }
    
    private function getModPDT()
    {
        if(!$this->mdl_pdt)
        {
            $this->mdl_pdt=new Model_pdt_products();
        }
        return $this->mdl_pdt;   
    }
    
    private function getModNEWS()
    {
        if(!$this->mdl_news)
        {
            $this->mdl_news=new Model_news_news();
        }
        return $this->mdl_news;   
    }
    
    public function getRowsetByMT($mt,$srow,$count)
    {
        switch(intval($mt))
        {
            case 1: //user
            {
                break;
            }
            case 2://企业
            {
                $this->getModCMP();
                $list = $this->mdl_cmp->getRowsetAll($srow,$count);
                break;
            }
            case 3: //产品
            {
                $this->getModPDT();
                $list = $this->mdl_pdt->getRowsetAll($srow,$count);
                break;
            }
            case 4: //资讯
            {
                $this->getModNEWS();
                $list = $this->mdl_news->getRowsetAll($srow,$count);
                break;
            }
            default:
            {
                exit('mod type is error!');
                break;
            }
        }
        return $list;
    }
    
    //得到模版变量值 根据模块类型 对应ds_way的自动
    private function _getTplValuesByMT($mt,$r,$rb)
    {
        switch(intval($mt))
        {
            case 1: //user
            {
                break;
            }
            case 2://企业
            {
                //$a=array('{$title}','{$linkurl}','{$imgurl}','{$alt}','{$width}','{$height}','{$type}','{$count}','{$time}','{$abstract}');
                $title=SString::dcutstr($r['cmp_name'],$rb['bb_title_length']);
                if(WEB_STATIC || true)
                {
                    //$link_url=NEWS_URL.'/'.$r['en_name'].'/u'.$r['admin_id'].'a'.$r['news_id'].'.shtml';  //http://zixun.3156.test/{$en}/u{$uid}a{$id}.shtml
                    //$link_url=NEWS_URL.'/'.$r['en_name2'].'/u'.$r['admin_id'].'a'.$r['news_id'].'.shtml';
                    $link_url=WWW_URL."/company/{$r['cmp_id']}/";
                }
                else
                {
                    // http://zixun.3156.test/main/show-id-{$id}-static-1
                    $link_url=NEWS_URL.SRoute::createUrl('company/about',array('id'=>$r['cmp_id']));
                }
                
                return array($title,$link_url,'',$r['cmp_name'],0,0,'',$r['click_count'],$r['build_date'],'',$r['slogan']);
            }
            case 3: //产品
            {
                //$a=array('{$title}','{$linkurl}','{$imgurl}','{$alt}','{$width}','{$height}','{$type}','{$count}','{$time}','{$abstract}');   
                
                $type=$r['type_name1'].'-'.$r['type_name2'];
                $type=trim($type,'-');
                //应当有长度限制 str_length
                $title=SString::dcutstr($r['name'],$rb['bb_title_length']);
                
                
                                
                if(WEB_STATIC || true)
                {
                    //$link_url=NEWS_URL.'/'.$r['en_name'].'/u'.$r['admin_id'].'a'.$r['news_id'].'.shtml';  //http://zixun.3156.test/{$en}/u{$uid}a{$id}.shtml
                    //$link_url=NEWS_URL.'/'.$r['en_name2'].'/u'.$r['admin_id'].'a'.$r['news_id'].'.shtml';
                    $link_url=WWW_URL."/product/{$r['pdt_id']}.shtml";
                }
                else
                {
                    // http://zixun.3156.test/main/show-id-{$id}-static-1
                    $link_url=NEWS_URL.SRoute::createUrl('product/info',array('id'=>$r['pdt_id']));
                }
                
                
                
               // $link_url=$this->_buildUrl($r['static_url'],$rb['ds_mod_type'],$r['pdt_id'],0,$rb['bb_id'],$rb['bg_id']);
                $time=SUtil::formatTime($r['dateline']);
                return array($title,$link_url,$r['img'],$r['name'],$rb['img_width'],$rb['img_height'],$type,$r['click_count'],$time,'');
            }
            case 4: //资讯
            {
                $a=array('{$title}','{$linkurl}','{$imgurl}','{$alt}','{$width}','{$height}','{$type}','{$count}','{$time}','{$abstract}');   
                $type=$r['type_name1'].'-'.$r['type_name2'].'-'.$r['type_name3'];
                $type=trim($type,'-');
                //应当有长度限制 str_length
                $title=SString::dcutstr($r['title'],$rb['bb_title_length']);
                
                $abstract=SString::dcutstr($r['description'],$rb['bb_abs_length']);

                //$link_url=$this->_buildUrl($r['static_url'],$rb['ds_mod_type'],$r['news_id'],0,$rb['bb_id'],$rb['bg_id']);
                if(WEB_STATIC || true)
                {
                    //$link_url=NEWS_URL.'/'.$r['en_name'].'/u'.$r['admin_id'].'a'.$r['news_id'].'.shtml';  //http://zixun.3156.test/{$en}/u{$uid}a{$id}.shtml
                    //$link_url=NEWS_URL.'/'.$r['en_name2'].'/u'.$r['admin_id'].'a'.$r['news_id'].'.shtml';
                    $link_url=NEWS_URL.'/u'.$r['admin_id'].'a'.$r['news_id'].'.shtml';
                }
                else
                {
                    // http://zixun.3156.test/main/show-id-{$id}-static-1
                    $link_url=NEWS_URL.SRoute::createUrl('main/show',array('id'=>$r['news_id']));
                }
            
                $time=SUtil::formatTime($r['dateline']);
                //n.news_id,n.title,n.static_url,n.type_id1,n.type_id2,n.type_id3,
                return array($title,$link_url,$r['pic'],$r['title'],$rb['img_width'],$rb['img_height'],$type,$r['click_count'],$time,$abstract);
                break;
            }
            default:
            {
                exit('mod type is error!');
                break;
            }
        }
        return false;   
    }
    
    //得到模版变量值 根据区块数据  对应ds_way的手动
    private function _getTplValuesByData($r,$rb)
    {
        /*
        [bd_id] => 3
        [bg_id] => 14
        [bb_id] => 4
        [title] => 123123123123
        [link_url] => 123
        [img_url] => 
        [abstract] => 123123
        */
        $url = $this->_buildUrl($r['link_url'],0,0,$r['bd_id'],$r['bb_id'],$r['bg_id']);
        $time=SUtil::formatTime($r['dateline']);
        return array($r['title'],$url,$r['img_url'],$r['title'],$rb['img_width'],$rb['img_height'],'',$r['click_count'],$time,$r['abstract']);    
    }
    
    private function _buildUrl($url,$mt,$mid,$did,$bid,$gid)
    {
        /*
        $linkurl=GOTO_URL.
        '-url-'.$v['link_url'].
        '-id-'.intval($v['ggp_id']).
        '-pid-'.intval($v['ggpg_id']).
        '-gid-'.intval($v['ggpg_id2']).
        '-oid-'.intval($v['ggq_id']);
        */
        /*   
        $url=WWW_ONLINE_URL.$url;
        return GOTO_URL.SRoute::createUrl('',array('url'=>$url,'mt'=>$mt,'mid'=>$mid,'did'=>$did,'bid'=>$bid,'gid'=>$gid));
        */ 
        //return $url.SRoute::createUrl('xx/xx',array('mt'=>$mt,'mid'=>$mid,'did'=>$did,'bid'=>$bid,'gid'=>$gid));
        return $url ? $url : "http://www.3156.test";
        
    }
    
    private function _templateConvert($r,$rb,$i=0)
    {
        if(!$r){return '';} 

        //$a=array('{$title}','{$linkurl}','{$imgurl}','{$alt}','{$width}','{$height}','{$type}','{$count}','{$time}','{$abstract}');

        //手动
        if($rb['ds_way']==1)
        {
            $b=$this->_getTplValuesByData($r,$rb);
        }
        else //自动 即 $rb['ds_way']==2)
        {
            $b=$this->_getTplValuesByMT($rb['ds_mod_type'],$r,$rb);
        }
        /*
        if($v['add_red']==1)
        {
            $v['code']=str_replace('{$title}','<font color="red">{$title}</font>',$v['code']);
           // $v['code']=str_replace('{$alt}','<font color="red">{$title}</font>',$v['code']);
        }
        */
        
        $html=str_replace($this->tpl_vars,$b,$rb['code']);
        if($i)
        {
            $html=str_replace('{$i}',$i,$html);
        }
        return $html; 
    }
    
    
    
    
    
    
    
    
}
