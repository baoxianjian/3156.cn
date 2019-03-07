<?php
/**
* @name: 站内留言信息管理
* @author: zhanghao
* @date: 10:30 2015/4/9
*/        

class Controller_stationinfo extends Controller_basepage {
	//首页
	function pageList($inPath){
        #得到当前页码
        $page=$this->getPageNumber($inPath);
        #实例化 关键字管理 数据模型
        $mdl_stationinfo = new Model_stationinfo_stationinfo();
        //$testModel->setReaddb('count');
        $mdl_stationinfo->setCache(false);
        
        #得到搜索 
        $dateline=$this->request['dateline'];
        $contact=$this->request['contact'];        
        $srow=array('dateline'=>$dateline,'contact'=>$contact);
		
		print_r($srow);
        
        #得到搜索关键字列表
        $data = $mdl_stationinfo->getListAll($page,$srow);
        
        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($data['list']);$i++){
            $this->request['id']=$list[$i]['ae_id'];
            $list[$i]['del_url']=$url=SRoute::createUrl('stationinfo/list', array('page' => $page,'id'=>$list[$i]['ae_id']), $this->request);
        }

        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath,20);
        $param['srow']= $srow;


        //默认ad/ad_list.html
        #调用相应模版  
        return $this->render($this->tplFilePath,$param);
	}
	
	//编辑
	
    function pageEdit(){
	    //SUtil::error('提交成功');
            $id=intval($this->request['id']);
            $mdl_stationinfo = new Model_stationinfo_stationinfo(); 
                                
            //如果有保存请求
            if($this->request['_ac']=='save')
            {
                $row=$this->request['row'];
                $row=SUtil::html_arr($row);
                
                //与数据库类型一致
                $row['contact']=trim($row['contact']);
                $row['dateline']=strtotime($row['dateline']);
                
                   //一系列检查
                if(!$row['pdt_name']) {              
                //$this->showMessage("提交失败，关键字不能为空！");
                SUtil::error("提交失败，产品名称不能为空！");
                exit;
                }
                if(!$row['areas']) {
                	SUtil::error("提交失败，意向区域不能为空！");
                	exit;
                }             
                if(!$row['dateline']) {
                	SUtil::error("提交失败，添加日期不能为空！");
                	exit;
                }
                
                                                                            
                if($id) {
                    //修改
            	    $this->ass('TIP_NAME','修改');
                    $result=$mdl_stationinfo->updateRowById($row,$id); 
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
                //	$row['dateline']=SUtil::formatTime(NOW,3);
                    $result=$mdl_stationinfo->addRow($row); 
                    if($result)
                    {
                         $this->showMessage('添加成功');
                    }
                }
                
            }
            else
            {
                if($id)
                {
                    //修改初始化
                    $row=$mdl_stationinfo->getRowById($id);
                    //添加初始化
                    $this->ass('TIP_NAME','修改');
                    $this->ass('AC_NAME','modify');
                    
                }
                else
                {
                    //添加初始化
                    $this->ass('TIP_NAME','添加');
                    $this->ass('AC_NAME','add');                                      
                }  
            }
	       // $this->preparRow($row);
            $this->ass('id',$id); 
            $this->ass('row',$row);
            return $this->template();
	    }
        
	    
	    
    private function preparRow($row)	
    {	
	    if($row)
	    {
		    //选中设置

		    $type_checked=array($row['type']=>"checked='checked' ");
		    $is_del_checked=array($row['is_del']=>"checked='checked' ");
		    $ss_state_checked=array($row['ss_state']=>"checked='checked' ");
	    
		    $this->ass('type_checked',$type_checked);
		    $this->ass('is_del_checked',$is_del_checked);
		    $this->ass('ss_state_checked',$ss_state_checked);
	    
		    $this->ass('row',$row);
	    }
    }
    
    
    /**
    * 删除
    * add by baoxianjian 19:00 2015/3/28 删除单独了
    * 
    */
    function pageDel()
    {    
        //如果在列表页删除s数据
        $mdl_stationinfo = new Model_stationinfo_stationinfo();    
        $id=$this->request['id'];
        $result=$mdl_stationinfo->deleteRowById($id);
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
    /**
     * 
     * 删除多条数据
     *
     */
    function pageDels()
    {
    	//如果在列表页删除s数据
    	$mdl_stationinfo = new Model_stationinfo_stationinfo();
    	$ids=$this->request['check'];
    	$result=$mdl_stationinfo->deleteRowByIds($ids);
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
