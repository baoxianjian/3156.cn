<?php
/**
* @name: 区块数据控制器 
* @author: baoxianjian
* @date: 22:07 2015/4/27
*/        
                 
class Controller_blockdata extends Controller_basepage {
    
	//列表页
	function pageList($inPath){   
        #得到当前页码
        $page=$this->getPageNumber();
        #实例化 搜索广告 数据模型
        $mdl_bd = new Model_block_data();
        
        #收索
        $s_id=$this->request['id'];
        $s_title=$this->request['title'];
        $s_bbid=$this->request['bbid'];
        $srow=array('id'=>$s_id,'title'=>$s_title,'bbid'=>$s_bbid);
        
        $type_selected=array($s_type=>'selected="selected"');
                        
        #得到搜索广告列表
        $data = $mdl_bd->getListAll($page,$srow);


        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($list);$i++){
            $list[$i]['dateline']=SUtil::formatTime($list[$i]['dateline']);
           // $list[$i]['bt_type']=$list_types[$list[$i]['bt_type']];

        }

        
        //unset($this->request['id']);
        
       // print_r($this->request);

        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath,20);

        $param['srow']=$srow;
        $param['type_selected']=$type_selected;
        //$param['list_types']=$list_types;
        
        
        //默认ad/ad_list.html
        #调用相应模版
        return $this->template($param);
	}
    
    // 广告的编辑 增加/删除
	function pageEdit(){
       
        $id=intval($this->request['id']);
        $mdl_bd = new Model_block_data();
        $mdl_bg = new Model_block_groups();
        
        $mdl_dd=new Model_block_blocks();  

        $group_list = $mdl_bg->getRowsetAll(); 
        $mt_list = $mdl_bd->getModTypes();
        unset($mt_list[1]); //不要代理商 

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
            $row['title']=trim($row['title']);
            $row['link_url']=trim($row['link_url']);
            $row['abstract']=trim($row['abstract']);
            $row['order']=intval($row['order']);
            

            $passed=true;           
            //一系列检查
           if(!$row['bg_id'] || !$row['bb_id']) //bg_id
            {
                $this->showMessage("提交失败，请选择区块！",3);
                $passed=false;
            }
            if($passed && !$row['mod_type'])
            {
           	    $this->showMessage("提交失败，请选择所属模块！",3); 
           	    $passed=false;
            }
            if($passed && !$row['title'])
            {
                   $this->showMessage("提交失败，请输入标题！",3); 
                   $passed=false;
            }
            if($passed && !$row['link_url'])
            {
                   $this->showMessage("提交失败，请输入链接地址！",3); 
                   $passed=false;
            }
            
            
            if($id && $passed)
            {
                //修改
                $this->ass('TIP_NAME','修改'); 
                $result=$mdl_bd->updateRowById($row,$id);
                                
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
                $result=$mdl_bd->addRow($row); 
                
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
                $row=$mdl_bd->getRowById($id);

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
            //$type_checked=array($row['bt_type']=>"checked='checked' ");
            $group_selected[$row['bg_id']]="selected='selected'";

            //有组ID即列出改组下的区块
            if($row['bg_id'])
            {
                $block_list=$mdl_dd->getRowsetAll(array('bgid'=>$row['bg_id']));
                $block_selected[$row['bb_id']]="selected='selected'";
                
                //把区块里选中的bb_type复制给当前row 合成
                foreach ($block_list as $v)
                {
                    if($v['bb_id']==$row['bb_id'])
                    {
                        $row['bb_type']=$v['bb_type'];
                        break;
                    }
                }
                
            }
            $mt_selected[$row['mod_type']]="selected='selected'";
            
            $img_url_show[1]='class="hide"';
            $img_url_show[2]='class="show"';
            
            
            $this->ass('group_selected',$group_selected);
            $this->ass('block_selected',$block_selected);
            $this->ass('mt_selected',$mt_selected);
            $this->ass('img_url_show',$img_url_show);
          
            $this->ass('block_list',$block_list);
            
            $this->ass('row',$row);
        }
        

        
        $this->ass('group_list',$group_list);
        $this->ass('mt_list',$mt_list); 
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
        $mdl_bd = new Model_block_data();     
        $id=$this->request['id'];

        $result=$mdl_bd->deleteRowById($id);
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
   
    function pageBlocks()
    {
        $gid=intval($this->request['gid']);
        
        $mdl_dd=new Model_block_blocks();  
        $list=$mdl_dd->getRowsetAll(array('bgid'=>$gid));
        
        exit(json_encode($list));   
    }

   
}
