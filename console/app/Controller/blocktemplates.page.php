<?php
/**
* @name: 区块模版控制器 
* @author: baoxianjian
* @date: 16:25 2015/4/25
*/        
                 
class Controller_blocktemplates extends Controller_basepage {
    
	//列表页
	function pageList($inPath){   
        #得到当前页码
        $page=$this->getPageNumber();
        #实例化 搜索广告 数据模型
        $mdl_bt = new Model_block_templates();

        $list_types=$mdl_bt->getTypes();
        
        $ac=$this->request['ac'];

        #收索
        $s_id=$this->request['id'];
        $s_btn=$this->request['btn'];
        $s_type=$this->request['type'];
        $srow=array('id'=>$s_id,'btn'=>$s_btn,'type'=>$s_type);
        
        $type_selected=array($s_type=>'selected="selected"');
                        
        #得到搜索广告列表
        $data = $mdl_bt->getListAll($page,$srow);


        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($list);$i++){
            $list[$i]['dateline']=SUtil::formatTime($list[$i]['dateline']);
            $list[$i]['bt_type']=$list_types[$list[$i]['bt_type']];

        }

        //unset($this->request['id']);
        
       // print_r($this->request);

        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);

        $param['srow']=$srow;
        $param['type_selected']=$type_selected;
        $param['list_types']=$list_types;
        
        
        //默认ad/ad_list.html
        #调用相应模版
        return $this->template($param);
	}
    
    // 广告的编辑 增加/删除
	function pageEdit(){
       
        $id=intval($this->request['id']);
        $mdl_bt = new Model_block_templates();  

        $list_types=$mdl_bt->getTypes();
        

        //如果有保存请求
        if($this->request['_ac']=='save')
        {
            $row=$this->request['row'];
            //$row=SUtil::html_arr($row);
            
            //与数据库类型一致
            //$row['mod_id']=intval($row['mod_id']);
            


            $passed=true;           
            //一系列检查
           if(!$row['bt_type'])
            {
                $this->showMessage("提交失败，请选择模版类型！",3);
                $passed=false;
            }
            if(!$row['name'])
            {
           	    $this->showMessage("提交失败，模版名称不能为空！",3); 
           	    $passed=false;
            }
     
            if(!$row['code'])
            {
                $this->showMessage("提交失败，模版代码不能为空！",3); 
            	$passed=false;
            }
               
            if($id && $passed)
            {
                //修改
                $this->ass('TIP_NAME','修改'); 
                $result=$mdl_bt->updateRowById($row,$id);
                                
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
                $result=$mdl_bt->addRow($row); 
                
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
                $row=$mdl_bt->getRowById($id);

                $this->ass('TIP_NAME','修改');
                $this->ass('AC_NAME','modify');
                
            }
            else
            {
                //添加初始化
                $this->ass('TIP_NAME','添加');
                $this->ass('AC_NAME','add');
                $row['bt_type']=2;
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
            $type_checked=array($row['bt_type']=>"checked='checked' ");

            $this->ass('type_checked',$type_checked);
          
            $this->ass('row',$row);
        }
        
        $this->ass('list_types',$list_types); 
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
        $mdl_bt = new Model_block_templates();     
        $id=$this->request['id'];

        $result=$mdl_bt->deleteRowById($id);
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
