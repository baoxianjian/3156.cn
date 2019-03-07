<?php
/**
* @name: 搜索广告 
* @author: baoxianjian
* @date: 10:32 2015/3/20
*/        
                 
class Controller_blockgroups extends Controller_basepage {
    
	//列表页
	function pageList($inPath){   
        #得到当前页码
        $page=$this->getPageNumber();
        #实例化 搜索广告 数据模型
        $mdl_bg = new Model_block_groups();
        //$testModel->setReaddb('count');
        //$mdl_bkw->setCache(false);
 
        $list_pc=$mdl_bg->getRowsetAll(0); 
        
        
        $ac=$this->request['ac'];

        #收索
        $s_id=$this->request['id'];
        $s_gn=$this->request['gn'];
        $s_gc=$this->request['gc'];
        $s_pc=$this->request['pc']; 
        
        $srow=array('id'=>$s_id,'gn'=>$s_gn,'gc'=>$s_gc,'pc'=>$s_pc);
        $pc_selected=array($s_pc=>"selected='selected'");
        
                        
        #得到搜索广告列表
        $data = $mdl_bg->getListAll($page,$srow);


        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($list);$i++){
            $list[$i]['dateline']=SUtil::formatTime($list[$i]['dateline']);
        }
        //unset($this->request['id']);
        
       // print_r($this->request);

        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);

        $param['srow']=$srow;
        $param['pc_selected']=$pc_selected;
        $param['list_pc']=$list_pc; 
        
        //默认ad/ad_list.html
        #调用相应模版
        return $this->template($param);
	}
    
    // 广告的编辑 增加/删除
	function pageEdit(){
       
        $id=intval($this->request['id']);
        $mdl_bg = new Model_block_groups();  

        $list_pc=$mdl_bg->getRowsetAll(0);
        

        //如果有保存请求
        if($this->request['_ac']=='save')
        {
            $row=$this->request['row'];

            
            //$row=SUtil::html_arr($row);
            
            //与数据库类型一致
            $row['order']=intval($row['order']);
            
            
            
            $passed=true;           
            //一系列检查
            if($row['bg_pc']=='' || $row['bg_pc']===0)
            {
                $this->showMessage("提交失败，没有选择父级分组!",3);
                $passed=false;
            }
            if($passed && !$row['bg_name'])
            {
           	    $this->showMessage("提交失败，分组名不能为空！",3); 
           	    $passed=false;
            }
                       
            if($id && $passed)
            {
                //修改
                $this->ass('TIP_NAME','修改'); 
                $result=$mdl_bg->updateRowById($row,$id);
                                
                if($result[0])
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
                $result=$mdl_bg->addRow($row); 
                
                if($result[0])
                {
                    $this->showMessage('添加成功'); 
                }
                else
                {
                    $this->showMessage('添加失败'); 
                }
            }
            $row['bg_code']=$result[1];
            
        }
        else
        {
            if($id)
            {
                //修改初始化
                $row=$mdl_bg->getRowById($id);

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

            $pc_cur=$mdl_bg->getParentCodeId($row['bg_code']);
            //$pc_cur=$mdl_bg->getParentCodeId('0101');                           
            
            //选中设置
            $pc_selected=array($pc_cur=>"selected='selected'");
            

            $this->ass('pc_selected',$pc_selected);
          
            $this->ass('row',$row);
        }
        
        $this->ass('list_pc',$list_pc);
        
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
        $mdl_bg = new Model_block_groups();     
        $id=$this->request['id'];

        $result=$mdl_bg->deleteRowById($id);
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
