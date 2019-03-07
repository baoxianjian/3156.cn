<?php
/**
* @name: 留言管理
* @author: zhanghao
* @date: 17:30 2015/4/13
*/        
class Controller_friendlinks extends Controller_basepage {
	//首页
	function pageList($inPath){
		
        #得到当前页码
        $page=$this->getPageNumber($inPath);
        #实例化 关键字管理 数据模型
        $mdl_friendlinks = new Model_sys_friendlinks();
        //$testModel->setReaddb('count');
        $mdl_friendlinks->setCache(false);

         
        #接收需要搜索的类型
        $sitepage=$this->request['sitepage'];
        $show_way=$this->request['show_way'];     
        $srow=array('sitepage'=>$sitepage,'show_way'=>$show_way);  
        
        #得到搜索关键字列表
        $data = $mdl_friendlinks->getListAll($page,$srow);      
           
        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($data['list']);$i++){
            $this->request['id']=$list[$i]['sfl_id'];
            $list[$i]['del_url']=$url=SRoute::createUrl('friendlinks/del', array('page' => $page,'id'=>$list[$i]['sfl_id']), $this->request);
        }
              
        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);

        $param['srow']=$srow;
        
        //默认ad/ad_list.html
        #调用相应模版
       
        return $this->render($this->tplFilePath,$param);
	}
		   		


	/**
	 * 删除
	 * add by baoxianjian 19:00 2015/3/28 删除单独了
	 *
	 */
	function pageDel()
	{
		//如果在列表页删除s数据
		  $mdl_friendlinks = new Model_sys_friendlinks();
		
		   $id=$this->request['id'];			
		
			 	$result=$mdl_friendlinks->deleteRowById($id);
		    
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
	 * 批量删除
	 *
	 */
	function pageDels()
	{
		//如果在列表页删除s数据
	//	$questArr = SUtil::html_arr($this->request);
	    $mdl_friendlinks = new Model_sys_friendlinks();
	//	$mdl_friendlinks->deleteRowByIds($questArr['check']) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'删除成功'))) : SUtil::success('删除成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'删除失败'))) : SUtil::error('删除失败') );
	
		$ids=$this->request['check'];
	
		$result=$mdl_friendlinks->deleteRowByIds($ids);
	
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
	 * 添加/编辑
	 *
	 */
     function pageEdit(){
	    //SUtil::error('提交成功');
            $id=intval($this->request['id']);
            $mdl_friendlinks = new Model_sys_friendlinks();
           
            //如果有保存请求
            if($this->request['_ac']=='save')
            {
                $row=$this->request['row'];
                $row=SUtil::html_arr($row);
                
                //与数据库类型一致           
                $row['order']=intval($row['order']);
                   //一系列检查
                if(!$row['sitepage']) {
                //  SUtil::error('');
                //$this->showMessage("提交失败，关键字不能为空！");
                SUtil::error("提交失败，网站页面不能为空！");
                exit;
                }
                
                if(!$row['title']) {
            	    SUtil::error("提交失败，站点标题不能为空！");
            	    exit;
                }
                if(!$row['infor']) {
                	SUtil::error("提交失败，链接详情不能为空！");
                	exit;
                }

                if(!$row['links']) {
                	SUtil::error("提交失败，链接地址不能为空！");
                	exit;
                }

                if(!$row['order']) {
                	SUtil::error("提交失败，排序不能为空！");
                	exit;
                }             
                                                                                  
                if($id) {
                    //修改
            	    $this->ass('TIP_NAME','修改');
                    $result=$mdl_friendlinks->updateRowById($row,$id); 
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
            	    $this->ass('TIP_NAME','添加');
            	    $row['dateline']=NOW;
                    $result=$mdl_friendlinks->addRow($row); 
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
                    $row=$mdl_friendlinks->getRowById($id);
                    //添加初始化
                    $this->ass('TIP_NAME','修改');
                    $this->ass('AC_NAME','modify');
                    
                }
                else
                {
                    //添加初始化
                    $this->ass('TIP_NAME','添加');
                    $this->ass('AC_NAME','add');              
                    $row['show_way']=1;
                    
                }  
            }

		    $this->preparRow($row);
            $this->ass('id',$id); 
            $this->ass('row',$row);
            
            return $this->template();
	    }
        
	    /**
	     *
	     * checkbox默认值
	     *
	     */
		private function preparRow($row)	
		    {	
			    if($row)
			    {
				    //选中设置
		
				    $type_checked=array($row['type']=>"checked='checked' ");
				    $is_del_checked=array($row['is_del']=>"checked='checked' ");
				    $show_way_checked=array($row['show_way']=>"checked='checked' ");
			    
				    $this->ass('type_checked',$type_checked);
				    $this->ass('is_del_checked',$is_del_checked);
				    $this->ass('show_way_checked',$show_way_checked);
			    
				    $this->ass('row',$row);
			    }
		    }
			
	
	
	
}