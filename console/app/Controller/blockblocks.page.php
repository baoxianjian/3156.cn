<?php
/**
* @name: 区块控制器
* @author: baoxianjian
* @date: 22:55 2015/4/26
*/                  
class Controller_blockblocks extends Controller_basepage {
    
	//列表页
	function pageList($inPath){   
        #得到当前页码
        $page=$this->getPageNumber();
        #实例化 搜索广告 数据模型
        $mdl_bb = new Model_block_blocks();
        //$testModel->setReaddb('count');
        //$mdl_bkw->setCache(false);
        
        $types = $mdl_bb->getTypes();
        $mod_types = $mdl_bb->getModTypes();
        $ds_ways = $mdl_bb->getDsWays();

        $ac=$this->request['ac'];


        #收索
        $s_id=$this->request['id'];
        $s_bn=$this->request['bn'];
        $s_bgid=$this->request['bgid'];
        $s_type=$this->request['type'];
        $srow=array('id'=>$s_id,'bn'=>$s_bn,'bgid'=>$s_bgid,'type'=>$s_type);
        
        $type_selected=array($s_type=>'selected="selected"');
                        
        #得到搜索广告列表
        $data = $mdl_bb->getListAll($page,$srow);

        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($list);$i++){
            $list[$i]['dateline']=SUtil::formatTime($list[$i]['dateline']);
            $list[$i]['bb_type']=$types[$list[$i]['bb_type']];
            $list[$i]['ds_way']=$ds_ways[$list[$i]['ds_way']]; 
        }
        //unset($this->request['id']);
        
       // print_r($this->request);

        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath,20);

        $param['srow']=$srow;
        $param['type_selected']=$type_selected;
        
        
        //默认ad/ad_list.html
        #调用相应模版
        return $this->template($param);
	}
    
    //区块的添加/修改
	function pageEdit(){
        $id=intval($this->request['id']);
        $mdl_bb = new Model_block_blocks();
        $mdl_bg = new Model_block_groups();
        $mdl_tpl = new Model_block_templates();

        $type_list = $mdl_bb->getTypes();
        $ds_way_list = $mdl_bb->getDsWays();
        $group_list = $mdl_bg->getRowsetAll();
        //$tpl_list = $mdl_tpl->getRowsetAll();
        $mt_list = $mdl_bb->getModTypes();
        unset($mt_list[1]); //不要代理商

        $rule_list = $mdl_bb->getDsRules();
        
        

        //如果有保存请求
        if($this->request['_ac']=='save')
        {
            $row=$this->request['row'];
            //$row=SUtil::html_arr($row);
            
            //与数据库类型一致
            //$row['mod_id']=intval($row['mod_id']);
            $row['cache_time']=intval($row['cache_time']);
            $row['bb_name']=trim($row['bb_name']);
            $row['bb_title_length']=intval($row['bb_title_length']);
            $row['bb_abs_length']=intval($row['bb_abs_length']);
            $row['ds_count']=intval($row['ds_count']);
            $row['ds_mod_type']=intval($row['ds_mod_type']);
            $row['ds_mod_tid1']=intval($row['ds_mod_tid1']);
            $row['ds_mod_tid2']=intval($row['ds_mod_tid2']);
            $row['ds_mod_tid3']=intval($row['ds_mod_tid3']);
            $row['ds_rule']=intval($row['ds_rule']);
            $row['ds_html']=trim($row['ds_html']);
            $row['ds_mod_ids']=trim($row['ds_mod_ids'],',');
            
            if($row['ds_rule']==1)
            {
                $id_list=explode(',', $row['ds_mod_ids']);
            
                foreach ($id_list as $k=>$v)
                {
                    if(!intval($v))
                    {
                        unset($id_list[$k]);
                    }
                }
                $row['ds_mod_ids']=implode(',', $id_list);
            }
            
            
            $passed=true;           
            //一系列检查
           if(!$row['bb_name'])
            {
                $this->showMessage("提交失败，区块名称不能为空！",3);
                $passed=false;
            }
            if($passed && !$row['bb_type'])
            {
           	    $this->showMessage("提交失败，请选择区块类型!",3); 
           	    $passed=false;
            }
            if($passed && !$row['bg_id'])
            {
                   $this->showMessage("提交失败，请选择区块分组!",3); 
                   $passed=false;
            }
            if($passed && !$row['bt_id'])
            {
                   $this->showMessage("提交失败，请选择模版!",3); 
                   $passed=false;
            }
            if($passed)
            {

                if($row['bb_type']==1 || $row['bb_type']==6)
                {       
                    if(!$row['bb_title_length'])
                    {
                        $this->showMessage("提交失败，请在文字长度中输入：大于0的数字!",3); 
                        $passed=false;
                    }
                }
                if($row['bb_type']==2 || $row['bb_type']==6)
                {
                    $row['bb_standard']=trim($row['bb_standard']);
                    if(!preg_match('/^([0-9]+)[\*xX]{1}([0-9]+)$/',$row['bb_standard']))
                    {
                        $this->showMessage("提交失败，请输入图片宽高，格式为：宽度*高度",3); 
                        $passed=false;
                    }
                }
                if($row['bb_type']==6)
                {
                    if(!$row['bb_abs_length'])
                    {
                        $this->showMessage("提交失败，请在摘要长度中输入：大于0的数字!",3); 
                        $passed=false;
                    } 
                }
            }
            
            if($passed)
            {
                switch(intval($row['ds_way']))
                {
                    case 1: //手动,无需验证
                    {
                        
                        break;
                    }
                    case 2: //验证自动
                    {
                        if($passed && !$row['ds_mod_type'])
                        {
                            $this->showMessage("提交失败，请选择数据来源!",3); 
                            $passed=false;   
                        }
                        
                        if($passed)
                        {
                            if($row['ds_rule']==1)
                            {
                                //检查ds_mod_ids
                                if(!preg_match('/^[0-9,]+$/',$row['ds_mod_ids']))
                                {
                                    $this->showMessage("提交失败，数据ID格式不对!",3); 
                                    $passed=false;  
                                }
                            }
                            else
                            {
                                if(!SUtil::numberInRange($row['ds_count'],1,100)) 
                                {
                                    $this->showMessage("提交失败，数据提取数量应为1~100之间的数字!",3); 
                                    $passed=false;   
                                }
                            }
                        }
                        
                        break;
                    }
                    case 3: //验证html代码
                    {
                        if(!$row['ds_html'])
                        {
                           $this->showMessage("提交失败，请贴上HTML代码!",3); 
                           $passed=false; 
                        }
                        break; 
                    }
                    default:
                    {
                       $this->showMessage("提交失败，请选择数据获取方式！",3); 
                       $passed=false;
                    } 
                }
            }
            

     
     
                          

                                    
            if($id && $passed)
            {
                //修改
                $this->ass('TIP_NAME','修改'); 
                $result=$mdl_bb->updateRowById($row,$id);
                                
                if($result)
                {      
                    $this->showMessage('修改成功');
                    
                }
                else
                {
                     $this->showMessage('修改失败',3); 
                }
            }
            else if($passed)
            {
                //添加
                $this->ass('TIP_NAME','添加');
                $row['dateline']=NOW;
                $result=$mdl_bb->addRow($row); 
                
                if($result)
                {
                    $this->showMessage('添加成功'); 
                }
                else
                {
                    $this->showMessage('添加失败'); 
                }
            }

            //html解过滤，还原成编辑时的样子
            $row['ds_html']=SUtil::deHttpFilter($row['ds_html']);

        }
        else
        {
            if($id)
            {
                //修改初始化
                $row=$mdl_bb->getRowById($id);

                $this->ass('TIP_NAME','修改');
                $this->ass('AC_NAME','modify');
                
            }
            else
            {
                //添加初始化
                $this->ass('TIP_NAME','添加');
                $this->ass('AC_NAME','add');
              
                $row['bb_type']=1;
                $row['ds_way']=2;
                $row['cache_time']=300;
                $row['ds_rule']=2;
                $row['ds_count']=10; 
              
            }  
        }

        if($row)
        {
           
            //时间格式化输出
            /*
            if($row['start_time']){
            	
               $row['start_time']=SUtil::formatTime($row['start_time'],3);
            }
            */

            
            $tpl_list = $this->_getTemplates($row['bb_type']);
            
            //选中设置
            $type_checked[$row['bb_type']]="checked='checked'";
            $ds_way_checked[$row['ds_way']]="checked='checked'";
            $group_selected[$row['bg_id']]="selected='selected'";
            $tpl_selected[$row['bt_id']]="selected='selected'";  
            $mt_selected[$row['ds_mod_type']]="selected='selected'";
            $rule_checked[$row['ds_rule']]="checked='checked'"; 
            
          //  var_dump($rule_checked);exit;
          
            //非主流 全显
            if($row['bb_type']==6)
            {
                foreach ($type_list as $k=>$v)
                {
                    $type_list_show[$k]='class="show"';
                }
            }
            else if($row['bb_type']==2) //图片 
            {
                $type_list_show[1]='class="show"';    //标题
                $type_list_show[2]='class="show"';    //图片
                $type_list_show[6]='class="hide"';    //摘要
            }
            else //文字
            {
                /*
                foreach ($type_list as $k=>$v)
                {
                    $type_list_show[$k]='class="hide"';
                }
                */
                $type_list_show[1]='class="show"';
                $type_list_show[2]='class="hide"'; 
                $type_list_show[6]='class="show"';
            }

            
            //print_r($type_list_show);exit;
            
            foreach ($ds_way_list as $k=>$v)
            {
                $ds_way_show[$k]='class="hide"';
            }
            $ds_way_show[$row['ds_way']]='class="show"';
            
            #有父级即显示出子级 模块下的分类1
            if($row['ds_mod_type'])
            {
                $mt_sub_1_list=$this->_getModSubTypes($row['ds_mod_type'],0);
                $mt_sub_1_selected[$row['ds_mod_tid1']]="selected='selected'"; 
            }
            #有父级即显示出子级 模块下的分类2  
            if($row['ds_mod_tid1'])
            {
                $mt_sub_2_list=$this->_getModSubTypes($row['ds_mod_type'],$row['ds_mod_tid1']);
                $mt_sub_2_selected[$row['ds_mod_tid2']]="selected='selected'";
            }
            #有父级即显示出子级 模块下的分类3  
            if($row['ds_mod_tid2'])
            {
                $mt_sub_3_list=$this->_getModSubTypes($row['ds_mod_type'],$row['ds_mod_tid2']);
                $mt_sub_3_selected[$row['ds_mod_tid3']]="selected='selected'";
            }
                           
            #企业和产品 有收费规则
            foreach ($rule_list as $k=>$v)
            {
                $ds_rule_show[$k]='class="show"';
            }
            if(!in_array($row['ds_mod_type'],array(2,3)))                
            {
                $ds_rule_show[4]='class="hide"';
            }
            
            
            
            #模块里分类
            $mt_sub_1_show=true;
            $mt_sub_2_show=true;   
            $mt_sub_3_show=true;
            if($row['ds_rule']==1)
            {
                $mt_sub_1_show=false;
                $mt_sub_2_show=false;     
                $mt_sub_3_show=false;
            }
            else
            {
                if(!$mt_sub_1_list)
                {
                     $mt_sub_1_show=false;
                }
                if(!$mt_sub_2_list)
                {
                     $mt_sub_2_show=false;
                }
                if(!$mt_sub_3_list)
                {
                     $mt_sub_3_show=false;
                }
            }
            
            #提取规则为ID时，出现
            if($row['ds_rule']==1)
            {
                $ds_mod_ids_show='class="show"';
                $ds_count_show='class="hide"';
            }
            else
            {
                $ds_mod_ids_show='class="hide"';
                $ds_count_show='class="show"';
            }
            

            $this->ass('type_checked',$type_checked);
            $this->ass('type_list_show',$type_list_show);
            $this->ass('ds_way_checked',$ds_way_checked);
            $this->ass('group_selected',$group_selected);
            $this->ass('tpl_selected',$tpl_selected);
            $this->ass('mt_selected',$mt_selected);
            $this->ass('rule_checked',$rule_checked);
            $this->ass('ds_way_show',$ds_way_show);
            $this->ass('ds_rule_show',$ds_rule_show);
            
            $this->ass('mt_sub_1_list',$mt_sub_1_list);
            $this->ass('mt_sub_1_selected',$mt_sub_1_selected);
            $this->ass('mt_sub_2_list',$mt_sub_2_list);
            $this->ass('mt_sub_2_selected',$mt_sub_2_selected); 
            $this->ass('mt_sub_3_list',$mt_sub_3_list);
            $this->ass('mt_sub_3_selected',$mt_sub_3_selected); 
            $this->ass('mt_sub_1_show',$mt_sub_1_show);
            $this->ass('mt_sub_2_show',$mt_sub_2_show);
            $this->ass('mt_sub_3_show',$mt_sub_3_show);
            $this->ass('ds_mod_ids_show',$ds_mod_ids_show);
            $this->ass('ds_count_show',$ds_count_show);
            
            $this->ass('row',$row);
        }
        
        $this->ass('type_list',$type_list);
        $this->ass('ds_way_list',$ds_way_list);
        $this->ass('group_list',$group_list);
        $this->ass('tpl_list',$tpl_list);
        $this->ass('mt_list',$mt_list);
        $this->ass('rule_list',$rule_list);
        
        $this->ass('id',$id); 
        
        return $this->template();
	}
	
    
    /**
    * 删除
    * 
    */
	function pageDel()
    {    
        //如果在列表页删除s数据
        $mdl_bb = new Model_block_blocks();     
        $id=$this->request['id'];

        $result=$mdl_bb->deleteRowById($id);
        if($result)
        {
            $this->showMessage('删除成功',1,'_reload');
        }
        else
        {
            $this->showMessage('删除失败',3);
        }
        return $this->template();
    }
    
    function pageModSubTypes()
    {
        $mt=intval($this->request['mt']);
        $pid=intval($this->request['pid']);

        $list=$this->_getModSubTypes($mt,$pid); 

        if($this->request['ajax'])
        {
            exit(json_encode($list));
        }
        print_r($list);
        exit;
    }
    
    
    function pageCheckModIds()
    {
        $mt=intval($this->request['mt']);
        $ids=trim($this->request['ids']);
        
        $mt_name='';
        switch($mt)
        {
            case 2: //厂商
            {
                $mdl_cmp = new Model_cmp_company();
                $list = $mdl_cmp->getNamesByIds($ids);

                $mt_name = '企业';
                foreach ($list as $v)
                {
                   $row=array('id'=>$v['cmp_id'],'name'=>$v['cmp_name']);
                   $list_temp[$row['id']]=$row;
                }
                $list=array();
                break;
            }
            case 3: //产品
            {
                $mdl_pdt = new Model_pdt_products();
                $list = $mdl_pdt->getNamesByIds($ids);

                $mt_name = '产品';
                foreach ($list as $v)
                {
                   $row=array('id'=>$v['pdt_id'],'name'=>$v['name']);
                   $list_temp[$row['id']]=$row;
                }
                
                break;
            }
            case 4: //资讯
            {     
                $mdl_news = new Model_news_news();
                $list = $mdl_news->getNamesByIds($ids);
                $mt_name = '资讯';
                foreach ($list as $v)
                {
                   $row=array('id'=>$v['news_id'],'name'=>$v['title']);
                   $list_temp[$row['id']]=$row;
                }
                break;
            }
            case 5:
            {
                /*
                $mdl_ac = new Model_agentcomments_agentcomments(); 
                $list = $mdl_ac->getNamesByIds($ids);
                $mt_name = '产品';
                foreach ($list as $v)
                {
                   $row=array('id'=>$v['news_id'],'name'=>$v['title']);
                   $list_temp[$row['id']]=$row;
                }
                  */
                break;
            }
            default:
            {
                //$list = array();
                //$list_temp=array();
                $str_list='请选择数据来源！';  
                break;  
            }
        }
        /*
        if($this->request['ajax'])
        {
            exit(json_encode($list_temp));
        }
        */
        //$this->showMessage()
        

        $id_list=explode(',',$ids);
        

        $all_count=count($id_list);
        $valid_count=0;
        
        if(!$str_list)
        {
            foreach ($id_list as $v)
            {
                if($list_temp[$v])
                {
                    $valid_count++;
                    $str_list.= "<font color=\"green\">{$mt_name}ID:{$v}存在,其名称为：{$list_temp[$v]['name']}</font><br/>";
                }
                else
                {
                    $str_list.= "<font color=\"red\">{$mt_name}ID:{$v}不存在</font><br/>";      
                }
            }
        }
       
        $msg_type = $valid_count==$all_count ? 1 : 3;
       
        $result=array('vc'=>$valid_count,'ac'=>$all_count,'info'=>$str_list);
        $str_result=json_encode($result);
        $this->showMessage($str_result,$msg_type);
        
        exit;
    }
   
    function pageTemplates()
    {
        $type=intval($this->request['t']);
        
        $list=$this->_getTemplates($type);

        if($this->request['ajax'])
        {
            exit(json_encode($list));
        }
        print_r($list);
        exit; 
    } 

    
    function _getModSubTypes($mt,$pid)
    {
        switch($mt)
        {
            case 2: //厂商
            {
                $list=array();
                break;
            }
            case 3:
            {
                $mdl_pt = new Model_pdt_pdtTypes();
                $srow=array('pid'=>$pid);
                $list = $mdl_pt->getRowsetAll($srow);
                
                foreach ($list as $v)
                {
                   $row=array('id'=>$v['pt_id'],'name'=>$v['pt_name']);
                   $list_temp[]=$row;
                }
                
                break;
            }
            case 4:
            {
                $mdl_nt = new Model_news_type();
                $srow=array('pid'=>$pid);
                $list = $mdl_nt->getRowsetAll($srow);
                
                foreach ($list as $v)
                {
                   $row=array('id'=>$v['nt_id'],'name'=>$v['name']);
                   $list_temp[]=$row;
                }
                break;
            }
            default:
            {
                $list = array();
                break;  
            }
        }
        
        return $list_temp;
    }
    
    function _getTemplates($type)
    {
        $type=intval($type);
        
        $mdl_tpl=new Model_block_templates();
        
        $srow=array('type'=>$type);
        $list=$mdl_tpl->getRowsetAll($srow);
        foreach ($list as $v)
        {
            $row=array('id'=>$v['bt_id'],'name'=>$v['name']);
            $list_temp[]=$row;
        }
        return $list_temp;
           
    }

    
}
