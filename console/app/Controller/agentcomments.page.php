<?php
/**
* @name: 留言管理
* @author: zhanghao
* @date: 17:30 2015/4/13
*/        
class Controller_agentcomments extends Controller_basepage {
	//首页
	function pageList($inPath){
		
        #得到当前页码
        $page=$this->getPageNumber($inPath);
        #实例化 关键字管理 数据模型
        $mdl_agentcomments = new Model_agentcomments_agentcomments();
        //$testModel->setReaddb('count');
        $mdl_agentcomments->setCache(false);
         
        #接收需要搜索的类型
        $dateline=$this->request['dateline'];
        $pdt_id=$this->request['pdt_id'];
        $user_id=$this->request['user_id'];
        $areas=$this->request['areas'];
        $company=$this->request['company'];
        $link_man=$this->request['link_man'];
        $tel=$this->request['tel'];
 //     $free=$this->request['free'];
       
        $online_state=$this->request['online_state'];//1
        $this->preparRow($online_state);
        $srow=array('online_state'=>$online_state,'dateline'=>$dateline,'pdt_id'=>$pdt_id,'areas'=>$areas,'user_id'=>$user_id,'tel'=>$tel,'company'=>$company,'link_man'=>$link_man);  
        
        #得到搜索关键字列表
        $data = $mdl_agentcomments->getListAgentcomments($page,$srow);
        
  
             
        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($data['list']);$i++){
            $list[$i]['start_time']=SUtil::formatTime($list[$i]['start_time']);
            $list[$i]['end_time']=SUtil::formatTime($list[$i]['end_time']);
            
            $this->request['id']=$list[$i]['id'];
            $list[$i]['del_url']=$url=SRoute::createUrl('agentcomments/del', array('page' => $page,'id'=>$list[$i]['id'],'t'=>$list[$i]['type']), $this->request);
        }
              
        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);

        $param['srow']=$srow;
        
        
      

        //默认ad/ad_list.html
        #调用相应模版
       
        return $this->render($this->tplFilePath,$param);
	}
		   		
	private function preparRow($srow)	
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
		
			$this->ass('srow',$srow);
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
		$mdl_agentcomments = new Model_agentcomments_agentcomments();
		
		$id=$this->request['id'];
		$type=$this->request['t'];	
	
		if($type=='1'){
			
			    $mdl_books=new Model_agent_books();
				$result=$mdl_books->deleteRowById($id);
				if($result)
				{
					$this->showMessage('删除成功',1,'_reload');
				}
				else
				{
					$this->showMessage('删除失败',3);
				}
		 }else{
			 	$result=$mdl_agentcomments->deleteRowById($id);
		    
			 	if($result)
			 	{
			 		$this->showMessage('删除成功',1,'_reload');
			 	}
			 	else
			 	{
			 		$this->showMessage('删除失败',3);
			 	}
		 }
		return $this->template();
	}
	 
	/**
	 * 添加
	 * 
	 *
	 */
    function pageAdd(){
	 
          
           $mdl_agentcomments = new Model_agentcomments_agentcomments();
                                
            //如果有保存请求
            if($this->request['_ac']=='save')
            {
                $row=$this->request['row'];
                $row=SUtil::html_arr($row);
                
                //与数据库类型一致
                $row['contact']=intval($row['contact']);
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
                if(!$row['contact']) {
                	SUtil::error("提交失败，联系方式不能为空！");
                	exit;
                }
                if(!$row['dateline']) {
                	SUtil::error("提交失败，添加日期不能为空！");
                	exit;
                }
                
          
                    //添加            
            	    $this->ass('TIP_NAME','添加');
            	    $row['dateline']=NOW;
                //	$row['dateline']=SUtil::formatTime(NOW,3);
                    $result=$mdl_stationinfo->addRow($row); 
                    if($result)
                    {
                         $this->showMessage('添加成功');
                    }
              
                
            }else{
            	
            }
          
	       // $this->preparRow($row);
            $this->ass('id',$id); 
            $this->ass('row',$row);
            return $this->template();
	    }
	
	
	
	
	
}