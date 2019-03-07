<?php
/**
* @name: 用户中心代理留言控制器 
* @author: zhanghao
* @date: 16:36 2015/3/29
*/   

     
define('SYS_NAME','agent');
header('Content-type:text/html;charset=utf-8');
class Controller_books extends Controller_basepage {
    
     //代理留言-编辑
    function pageEdit(){
    	$id=intval($this->request['id']);
    	$mdl_books= new Model_agent_books();
    	
    	//保存修改
    	if($this->request['ac']=='save')
    	{
   	 	    $row=$this->request['row'];
   	 	    $row['areas']=$_POST['areas'];
   	 	    $channel=$this->request['channel'];
			$row['channel']=implode(',', $channel);
   	 	    
            $up=$mdl_books->updateRowById($row,$id);    
             print_r($up);	
               
    	 	 if($up){

    	 	 	SUtil::success("修改成功！");
    	 	 }else{
  
    	 	 	SUtil::error("修改失败!");
    	 	 }
    	}
    	else
    	{
    		//初始化
    		$row=$mdl_books->getRowById($id);
   
    		if(!$row['areas'])
    		{
    			$row['areas']="请选择省份";
    		}
    	//	$this->ass('row',$row);
    		$this->preparRow($row);
    		
    		
    	}  	
        return $this->template();
	   }
            
	    //代理留言-添加
	   function pageAdd($inPath){
		       
		        
				$mdl_books= new Model_agent_books();
				$row=$this->request['row'];
				$row['areas']=$_POST['areas'];    
				//保存修改
				if($this->request['ac']=='save')
				{
					$channel=$this->request['channel'];//一个数组；
					$channel=implode(",", $channel);//一个字符串；
					        $ru=$this->getUserFromSession(1);
					 	    $row['link_man']=trim($row['link_man']);
							$row['end_time']=intval($row['end_time']);
							$row['pdt_type2']=trim($row['pdt_type2']);							    								
							$row['tel']=intval($row['tel']);
							$row['mp']=intval($row['mp']);
							$row['qq']=intval($row['qq']);
							$row['channel']=$channel;
							$row['user_id']=$ru['uid'];
							
								$row['dateline']=NOW;
								$result=$mdl_books->addRow($row);
								if($result)
								   {	//$this->showMessage('添加成功');
										SUtil::success("添加成功！");
								   }	
					 
				}
				else 
				{
					//添加初始化
					$row['agent_type']=1;
					
				}
		
		    $this->preparRow($row);
	    	return $this->template();
	    }
     //代理留言列表
    function pageList($inPath){
    	#实例化 关键字管理 数据模型
    	$mdl_books = new Model_agent_books(); 
    	//删除一条数据
        $id=$this->request['ac'];
        if($ab_id){
	        $res=$mdl_books->deleteRowById($id);
	        if($res){
	        	SUtil::success("删除成功！");
	        }else{
	        	SUtil::error("删除失败！");
	        }
        }
        //如果列表页有子动作——刷新
        if($this->request['ac']=='fre')
        {
        	$ab_id=intval($this->request['id']);
        	$row['dateline']=NOW;
        	$res=$mdl_books->updateRowById($row,$ab_id);
        	if($res){
        		//SUtil::success("添加成功！");
        	}else{
        		//SUtil::error("失败！");
        	}
        }
        
        if($this->request['ac']=='del')
        {    
        	$questArr = SUtil::html_arr($this->request);  

        	print_r($questArr);
        	exit("ok");
        	$mdl_books = new Model_agent_books();        	
            $mdl_books->del($questArr['check']) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'删除成功'))) : SUtil::success('删除成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'删除失败'))) : SUtil::error('删除失败') );

        }	
        	//当前页面
        	$page=$this->getPageNumber($inPath);
        	//$testModel->setReaddb('count');
			$mdl_books->setCache(false);       		 
        	#得到搜索关键字列表
			$is_del=$this->request['is_del'];
			
        	if($is_del==1){
        		$data = $mdl_books->getListAllDel($page);
        	}else{
             	$data = $mdl_books->getListAll($page);
        	}
        	
        	$list=$data['list'];
        	for($i=0;$i<count($data['list']);$i++)
        	{
        		$this->request['id']=$list[$i]['ab_id'];
        		$list[$i]['del_url']=$url=SRoute::createUrl('agent/list', array('page' => $page,'id'=>$list[$i]['ab_id']), $this->request);

        		#设置好要放入模版的数据（变量）
        		$param[LIST_VAR_NAME] = $list;
        		$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);
        		$param['srow']=$srow;
        				 
        	}
        	
        	$this->preparRow($row);	
        	$this->ass('row', $srow);					
        	return $this->render($this->tplFilePath,$param);
     	}
        	
        
    
    //默认选中设置
    private function preparRow($row)
    {
    	if($row)
    	{
    		//选中设置    		
    		$agent_type_checked=array($row['agent_type']=>"checked='checked'");
    		$end_time_checked=array($row['end_time']=>"checked='checked'");
    		$channel_checked=array($row['channel']=>"checked='checked'");
    		$is_del_checked=array($row['is_del']=>"selected='selected'");
    		     		
    		$list=explode(',', $row['channel']); //0=>1,1=>2,2=>3
    		foreach ($list as $v) 
    		{
    			$list[$v]="checked='checked'";
    		}

    		$this->ass('is_del_checked',$is_del_checked);
    		$this->ass('agent_type_checked',$agent_type_checked);
    		$this->ass('end_time_checked',$end_time_checked);
    		$this->ass('channel_checked',$list);
    		$this->ass('row',$row);
    		
    	}
    }
    
    /**
     * 恢复
     *
     */
   function pageRe()
    {
    	$mdl_books= new Model_agent_books();
    	$ab_id=intval($this->request['id']);
    	$row['is_del']=STATE_DEL_NO;
    	$res=$mdl_books->updateRowById($row,$ab_id);
    	if($res){
    		$this->showMessage('恢复成功',1,'_reload');
    	}else{
    		SUtil::error("恢复失败！");
    	}
    }
    
    
    
    /**
     * 删除
     *
     */
    function pageDelAll()
    {
    	//如果在列表页删除s数据
    	$mdl_books= new Model_agent_books();
    	$ids=$this->request['check'];
		
    	
    	$result=$mdl_books->deleteRowByIds($ids);
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
     * 刷新 信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    function pageFreshall()
    {
    	//如果在列表页删除s数据
    	$mdl_books= new Model_agent_books();
    	$ids=$this->request['check'];
    
    
    	$result=$mdl_books->freshRowByIds($ids);
    	if($result)
    	{
    		$this->showMessage('刷新成功',1,'_reload');
    	}
    	else
    	{
    		$this->showMessage('刷新失败',3);
    	}
    	return $this->template();
    }
    
    /**
     * 
     * 检查手机号码是否在黑名单中
     * @return mixed
     */
    function pageChecktel()
    {
    	$tel=$this->request['tel'];
    
    	$mdl_blacklist=new Model_agent_blacklist();
    	$res=$mdl_blacklist->getRowByTel($tel);

    	if($res) //有值即错误
    	{
    		$this->showMessage('号码是黑名单',3,'_reload');
    	}
    	else //空则可以用
    	{
    		$this->showMessage('可以使用',1);
    	}
    	
    }

  
            
        
}
