<?php
/**
* @name: 搜索广告 
* @author: baoxianjian
* @date: 10:32 2015/3/20
*/        

class Controller_adposition extends Controller_basepage {
    
	//列表页
	function pageList($inPath){
		
		

        #得到当前页码
        $page=$this->getPageNumber();
        #实例化 搜索广告 数据模型
        $mdl_adp = new Model_search_adposition();
        //$testModel->setReaddb('count');
        $mdl_adp->setCache(false);
        
        $sgp_area=$this->request['sgp_area'];
        $title=$this->request['title'];
        $sgp_page=$this->request['sgp_page'];
        $sg_id=$this->request['sg_id'];
        $srow=array('sgp_area'=>$sgp_area,'title'=>$title,'sgp_page'=>$sgp_page,'sg_id'=>$sg_id,);
        
        $sgp_page_selected=array($sgp_page=>'selected="selected"');
                        
        #得到搜索关键字列表
        $data = $mdl_adp->getListAll($page,$srow);
        
        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($list);$i++){
            $list[$i]['sgp_page']=$mdl_adp->getPageName($list[$i]['sgp_page']);
            $list[$i]['sgp_area']=$mdl_adp->getAreaName($list[$i]['sgp_area']);
        }

        
        foreach ($list as $v) {
            $list_temp[$v['sgp_id']]=$list_temp[$v['sgp_id']].','.$v['sg_id1'];
        }
        
        foreach ($list as $v) {
            $v['sg_ids']=$list_temp[$v['sgp_id']];
           $list_temp2[$v['sgp_id']]=$v;
        }
        
        $list=array();
        foreach ($list_temp2 as $v) {
            $v['sg_ids']{0}='';
            array_push($list,$v);
        }
        

        
        for($i=0;$i<count($list);$i++){
            $list[$i]['start_time']=SUtil::formatTime($list[$i]['start_time']);
            $list[$i]['end_time']=SUtil::formatTime($list[$i]['end_time']);
            
            $this->request['id']=$list[$i]['sgp_id'];
            $list[$i]['del_url']=$url=SRoute::createUrl('adposition/del', array('page' => $page,'id'=>$list[$i]['sgp_id']), $this->request); 
        }
        //unset($this->request['id']);
        
       // print_r($this->request);

        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        //$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);
        
        $param['all_count']=count($list);
        
        $param['srow']=$srow;
        $param['sgp_page_selected']=$sgp_page_selected;
        
        //默认ad/ad_list.html
        #调用相应模版
        return $this->template($param);
	}
    
    // 广告的编辑 增加/删除
	function pageEdit(){
       
        $id=intval($this->request['id']);
        $mdl_adp = new Model_search_adposition();  

        //如果有保存请求
        if($this->request['_ac']=='save')
        {
            $row=$this->request['row'];
            $row=SUtil::html_arr($row);
            
            //与数据库类型一致

            $row['sgp_page']=intval($row['sgp_page']);
            $row['sgp_area']=intval($row['sgp_area']);
            
            $row['width']=intval($row['width']);
            $row['height']=intval($row['height']);
            $row['order']=intval($row['order']);
            

            //一系列检查
           if(!$row['title'])
            {
                SUtil::error("提交失败，标题不能为空！");
                exit;
            }
          
            if($row['order']<0 ||  $row['sgpg_page']<0 ||  $row['sgpg_area']<0 || $row['width']<0 || $row['height']<0)
            {
                SUtil::error("提交失败，数据格式不对!");
                exit;
            }      

            
            if($id)
            {
                //修改
                $this->ass('TIP_NAME','修改'); 
                $result=$mdl_adp->updateRowById($row,$id);
                if($result)
                {      
                    $this->showMessage('修改成功');
                    
                }
                else
                {
                     $this->showMessage('修改失败'); 
                }

            }
            else
            {
                //添加
                $this->ass('TIP_NAME','添加');
                $row['dateline']=NOW;
                $result=$mdl_adp->addRow($row); 
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

                $row=$mdl_adp->getRowById($id);
                //添加初始化
                $this->ass('TIP_NAME','修改');
                $this->ass('AC_NAME','modify');
                
            }
            else
            {
                //添加初始化
                $this->ass('TIP_NAME','添加');
                $this->ass('AC_NAME','add');
                $row['sg_type']=1;
                $row['recommend']=0;
                $row['sg_state']=1;
                
            }  
        }

        if($row)
        {
            //选中设置
            $sgp_page_checked=array($row['sgp_page']=>"checked='checked' ");
            
            $sgp_area_checked=array($row['sgp_area']=>"checked='checked' ");
            
            $this->ass('sgp_page_checked',$sgp_page_checked);
            $this->ass('sgp_area_checked',$sgp_area_checked); 
            
            $this->ass('row',$row);
        }
        
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
        $mdl_adp = new Model_search_adposition(); 
        $id=$this->request['id'];
        $result=$mdl_adp->deleteRowById($id);
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