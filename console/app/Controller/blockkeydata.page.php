<?php
/**
* @name: 搜索广告 
* @author: baoxianjian
* @date: 10:32 2015/3/20
*/        
                 
class Controller_blockkeydata extends Controller_basepage {
    
	//列表页
	function pageList($inPath){   
        #得到当前页码
        $page=$this->getPageNumber();
        #实例化 搜索广告 数据模型
        $mdl_bkw = new Model_block_keywords();
        $mdl_bkd = new Model_block_keydata();
        //$testModel->setReaddb('count');
        $mdl_bkw->setCache(false);
        
        $types=$mdl_bkw->getTypes();
        $mod_types=$mdl_bkw->getModTypes();

        /*
        $list_kwd=$mdl_bkd->getRowsetKWDAll();
        print_r($list_kwd);
        exit;
        */
        
        
        $ac=$this->request['ac'];


        #收索
        $s_id=$this->request['id'];
        $s_bkwn=$this->request['bkwn'];
        $s_bkwid=$this->request['bkwid'];
        $s_title=$this->request['title'];
        
        $srow=array('id'=>$s_id,'bkwn'=>$s_bkwn,'bkwid'=>$s_bkwid,'title'=>$s_title);
        
        //$type_selected=array($s_type=>'selected="selected"');
                        
        #得到搜索广告列表
        $data = $mdl_bkd->getListAll($page,$srow);

        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($list);$i++){
            $list[$i]['start_time']=SUtil::formatTime($list[$i]['start_time']);
            $list[$i]['end_time']=SUtil::formatTime($list[$i]['end_time']);
            $list[$i]['dateline']=SUtil::formatTime($list[$i]['dateline']);
            $list[$i]['bkw_type']=$types[$list[$i]['bkw_type']];
            $list[$i]['mod_type']=$mod_types[$list[$i]['mod_type']]; 
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
    
    // 广告的编辑 增加/删除
	function pageEdit(){
       
        $id=intval($this->request['id']);
        $mdl_bkw = new Model_block_keywords();  
        $mdl_bkd = new Model_block_keydata();

        $type_list = $mdl_bkw->getTypes();
        $area_list = $mdl_bkd->getAreas();

        //如果有保存请求
        if($this->request['_ac']=='save')
        {
            $row=$this->request['row'];
            $flashatt=$this->request['flashatt'];
                         
            //单图
            foreach ($flashatt as $k=>$v)
            {
                if($k!=0)
                {
                    $img_url=$v['path'];
                }
            }

            if(!$img_url)
            {
               $img_url= $flashatt[0]['path'];
            }
       
            $row['img_url']=$img_url;
            
            //与数据库类型一致
            $row['bkw_id']=intval($row['bkw_id']);
            $row['tpl_id']=intval($row['tpl_id']);
            $row['area']=intval($row['area']);

            $row['start_time']=strtotime($row['start_time']);
            $row['end_time']=strtotime($row['end_time']);
            
            $passed=true;           
            //一系列检查
           if(!$row['bkw_id'])
            {
                $this->showMessage("提交失败，区块关键字ID不能为空！",3);
                $passed=false;
            }

             /*
            if(!$row['mod_type']=intval($row['mod_type']))
            {
                $this->showMessage("提交失败，所属模块不能为空！",3); 
            	$passed=false;
            }
           
            if(!$row['mod_id']=intval($row['mod_id']))
            {
                $this->showMessage("提交失败，模块ID不能为空！",3); 
                $passed=false;
            }
           

            if(!$row['bk_type']=intval($row['bk_type']))
            {            	
            	$this->showMessage("提交失败，类型不能为空！");
            	$passed=false;
            }
            */
            
            
            /* 
            if(!$row['tpl_id'])
            {                
                $this->showMessage("提交失败，模版ID不能为空！",3);
                $passed=false;
            }
            */
                                    
            if($id && $passed)
            {
                //修改
                $this->ass('TIP_NAME','修改'); 
                $result=$mdl_bkd->updateRowById($row,$id);
                                
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
                $result=$mdl_bkd->addRow($row); 
                
                if($result)
                {
                    $this->showMessage('添加成功'); 
                }
                else
                {
                    $this->showMessage('添加失败',3); 
                }
            }
            
        }
        else
        {
            if($id)
            {
                //修改初始化
                $row=$mdl_bkd->getRowById($id);

                $this->ass('TIP_NAME','修改');
                $this->ass('AC_NAME','modify');
                
            }
            else
            {
                //添加初始化
                $this->ass('TIP_NAME','添加');
                $this->ass('AC_NAME','add');
                /*
                $row['sg_type']=2;
                $row['recommend']=0;
                $row['sg_state']=1;
                */
                $row['area']=1; 
            }  
        }

        if($row)
        {
           
            //时间格式化输出
            if($row['start_time'])
            {
               $row['start_time']=SUtil::formatTime($row['start_time'],3);
            }
            if($row['end_time'])
            {
               $row['end_time']=SUtil::formatTime($row['end_time'],3);
            }

            
            //选中设置
            $type_selected=array($row['bkw_type']=>"selected='selected' ");
            $area_checked=array($row['area']=>"checked='checked' ");
             
            $this->ass('type_selected',$type_selected);
            $this->ass('area_checked',$area_checked); 
          
            $this->ass('row',$row);
        }
        
        
        
        $this->ass('area_list',$area_list);
        $this->ass('type_list',$type_list);
        
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
        $mdl_bkd = new Model_block_keydata();     
        $id=$this->request['id'];

        $result=$mdl_bkd->deleteRowById($id);
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

}
