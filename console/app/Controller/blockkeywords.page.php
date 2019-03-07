<?php
/**
* @name: 搜索广告 
* @author: baoxianjian
* @date: 10:32 2015/3/20
*/        
                 
class Controller_blockkeywords extends Controller_basepage {
    
	//列表页
	function pageList($inPath){   
        #得到当前页码
        $page=$this->getPageNumber();
        #实例化 搜索广告 数据模型
        $mdl_bkw = new Model_block_keywords();
        //$testModel->setReaddb('count');
        //$mdl_bkw->setCache(false);
        
        $types=$mdl_bkw->getTypes();
        $mod_types=$mdl_bkw->getModTypes();
        
        
        $ac=$this->request['ac'];


        #收索
        $s_id=$this->request['id'];
        $s_bkwn=$this->request['bkwn'];
        $s_type=$this->request['type'];
        $srow=array('id'=>$s_id,'bkwn'=>$s_bkwn,'type'=>$s_type);
        
        $type_selected=array($s_type=>'selected="selected"');
                        
        #得到搜索广告列表
        $data = $mdl_bkw->getListAll($page,$srow);


        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($list);$i++){
            $list[$i]['dateline']=SUtil::formatTime($list[$i]['dateline']);
            $list[$i]['bkw_type']=$types[$list[$i]['bkw_type']];
            $list[$i]['mod_type']=$mod_types[$list[$i]['mod_type']]; 
        }
        //unset($this->request['id']);
        
       // print_r($this->request);

        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);

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

        $type_list=$mdl_bkw->getTypes();
        

        //如果有保存请求
        if($this->request['_ac']=='save')
        {
            $row=$this->request['row'];
            //$row=SUtil::html_arr($row);
            
            //与数据库类型一致
            //$row['mod_id']=intval($row['mod_id']);
            


            $passed=true;           
            //一系列检查
           if(!$row['bkw_name'])
            {
                $this->showMessage("提交失败，关键字不能为空！",3);
                $passed=false;
            }
            if(!$row['bkw_mark'])
            {
           	    $this->showMessage("提交失败，系统标识不能为空！",3); 
           	    $passed=false;
            }
     
            if(!$row['mod_type']=intval($row['mod_type']))
            {
                $this->showMessage("提交失败，所属模块不能为空！",3); 
            	$passed=false;
            }
            /*
            if(!$row['mod_id']=intval($row['mod_id']))
            {
                $this->showMessage("提交失败，模块ID不能为空！",3); 
                $passed=false;
            }
            */

            if(!$row['bkw_type']=intval($row['bkw_type']))
            {            	
            	$this->showMessage("提交失败，类型不能为空！",3);
            	$passed=false;
            }

            /*
            if(!$row['tpl_id']=intval($row['tpl_id']))
            {                
                $this->showMessage("提交失败，模版ID不能为空！",3);
                $passed=false;
            }
            */
                                   
            if($id && $passed)
            {
                //修改
                $this->ass('TIP_NAME','修改'); 
                $result=$mdl_bkw->updateRowById($row,$id);
                                
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
                $result=$mdl_bkw->addRow($row); 
                
                if($result)
                {
                    $this->showMessage('添加成功'); 
                }
                else
                {
                    $this->showMessage('添加失败'); 
                }
            }
            
        }
        else
        {
            if($id)
            {
                //修改初始化
                $row=$mdl_bkw->getRowById($id);

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

            
            //选中设置
            $type_selected=array($row['bkw_type']=>"selected='selected' ");

            $this->ass('type_selected',$type_selected);
          
            $this->ass('row',$row);
        }
        
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
        $mdl_bkw = new Model_block_keywords();     
        $id=$this->request['id'];

        $result=$mdl_bkw->deleteRowById($id);
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
   
    
    function pageCheckBKWId()
    {
        $id=intval($this->request['id']);

        $mod_bkw=new Model_block_keywords();
        $bkw_name=$mod_bkw->getNameById($id);
        
        if($bkw_name)
        {
            $this->showMessage("区块关键字ID可以用，其名称为：{$bkw_name}",1);
        }
        else
        {
            $this->showMessage("区块关键字ID不存在！",3);
        }
    }
    
    function pageCheckModId()
    {
        $id=intval($this->request['id']);

        $mod_cmp=new Model_cmp_company();
        $cname=$mod_cmp->getNameById($id);
        
        if($cname)
        {
            $this->showMessage("企业ID可以用，其名称为：{$cname}",1);
        }
        else
        {
            $this->showMessage("企业ID不存在！",3);
        }
    }

}
