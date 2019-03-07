<?php
/**
* @name: 代理信息控制器 
* @author: zhanghao
* @date: 19:22 2015/4/7
*/        
define('SYS_NAME','cmt');


class Controller_comments extends Controller_basepage {
       
  /*   //企业中心首页
    function pageIndex($inPath){
        $mdl_user=new Model_user_user(); 
        
        $ru=$this->getUserFromSession();
                
        $row=$mdl_user->getRowById($ru['id']);
        
        //$this->buildTplFilePath('user.index','user');  
        
        $row['reg_time']=SUtil::formatTime($row['reg_time']);
        $params['info']=$row;
        return $this->template($params);
    }
 */
    /**
     * 显示账户信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
/*    
    function pageInfo()
    {
       $mdl_cmp=new Model_cmp_company();
       return $this->template(); 
    }
    
    function pageInfoEdit()
    {
        $this->getCompanyFromSession(1);
        
        
        $mdl_area=new Model_com_area();
        //得到地区编码
        $area_rowset = $mdl_area->getAllRowset(false,false);
        $super = 'A'.substr($row['citycode'],0,2).'0000';
        $area_sel[$super] = 'selected="selected"';
        
        $sub_area_rowset = $mdl_area->getAllRowset($super,true);
        $sub = 'A'.substr($row['citycode'],0,4).'00';
        $sub_area_sel[$sub] = 'selected="selected"';
        
        $min_sub_rowset =  $mdl_area->getAllRowset($sub,true);
        $min_sub_sel['A'.$row['citycode']] = 'selected="selected"';
        
        
        $this->ass('area_rowset',$area_rowset);
        
        $this->ass('session_id',session_id());
        
        return $this->template(); 
    }
    */
    /**
     * 显示 我的代理信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    function pageMyinfo($inPath){
    	$page=$this->getPageNumber($inPath);
    	#实例化 关键字管理 数据模型
    	$mdl_cmt = new Model_cmt_comments();
    	//$testModel->setReaddb('count');
    	$mdl_cmt->setCache(false);
    	#得到搜索
   	    $start_time=$this->request['start_time'];
    	$end_time=$this->request['end_time'];    
    	$is_read=$this->request['read'];
    	print_r($start_time);
    	print_r($end_time);
        if($this->request['d']=="today")
    	{
    	    $srow=array('start_time'=>$start_time,'end_time'=>$end_time,'is_read'=>$is_read,'today'=>date('Y-m-d',NOW));
    	}elseif ($this->request['d']=="yester")
    	{
    		$srow=array('start_time'=>$start_time,'end_time'=>$end_time,'is_read'=>$is_read,'yester'=>date('Y-m-d',NOW));
    	}elseif ($this->request['d']=="near")
    	{
    		$srow=array('start_time'=>$start_time,'end_time'=>$end_time,'is_read'=>$is_read,'near'=>date('Y-m-d',NOW));
    	}else{
    		$srow=array('start_time'=>$start_time,'end_time'=>$end_time,'is_read'=>$is_read);
    	}
    	
    	if($this->request['ac']=="read"){
	    	$ids=$this->request['check'];
	    	$result=$mdl_cmt->readRowByIds($ids);
	    	if($result)
	    	{
	    		$this->showMessage('标记成功',1,'_reload');
	    	}
	    	else
	    	{
	    		$this->showMessage('标记失败',3);
	    	}
    	}
    	#得到搜索关键字列表
    	//$data = $mdl_keywords->getListAll($page,$srow);    	       	
    	$data = $mdl_cmt->getListAll($page,$srow);
    	#数据临时处理
    	$list=$data['list'];
    	for($i=0;$i<count($data['list']);$i++){
	
    		$this->request['id']=$list[$i]['ab_id'];
    		$list[$i]['del_url']=$url=SRoute::createUrl('comments/myinfo', array('page' => $page,'id'=>$list[$i]['ab_id']), $this->request);
    	}
    	
    	#设置好要放入模版的数据（变量）
    	$param[LIST_VAR_NAME] = $list;
    	$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);
    	$param['srow']=$srow;
    	$this->preparRow($srow);
    	//return $this->render($this->tplFilePath,$param);
    	return $this->template($param);
    }
    
    /**
     * 显示 免费会员代理信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    function pageOrdinary($inPath){
    	$page=$this->getPageNumber($inPath);
    	#实例化 关键字管理 数据模型
    	$mdl_cmt = new Model_cmt_comments();
    	//$testModel->setReaddb('count');
    	$mdl_cmt->setCache(false);
    	#得到搜索
    	$start_time=$this->request['start_time'];
    	$end_time=$this->request['end_time'];
    	$tel=$this->request['tel'];
    	$pdt_names=$this->request['pdt_names'];
    	$srow=array('start_time'=>$start_time,'end_time'=>$end_time,'tel'=>$tel,'pdt_names'=>$pdt_names);
    	
    	#得到搜索关键字列表
    	//$data = $mdl_keywords->getListAll($page,$srow);
    	$data = $mdl_cmt->getListFree($page, $srow);
    	#数据临时处理
    	$list=$data['list'];
    	for($i=0;$i<count($data['list']);$i++){
    			$this->request['id']=$list[$i]['ab_id'];
    			$list[$i]['del_url']=$url=SRoute::createUrl('comments/ordinary', array('page' => $page,'id'=>$list[$i]['ab_id']), $this->request);
    	}
    	#设置好要放入模版的数据（变量）
    	$param[LIST_VAR_NAME] = $list;
    	$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);
    	$param['srow']=$srow;
    	$this->preparRow($srow);
    	return $this->template($param);
    }
    /**
     * 显示 高级会员代理信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    function pageSenior($inPath){
    	$page=$this->getPageNumber($inPath);
    	#实例化 关键字管理 数据模型
    	$mdl_cmt = new Model_cmt_comments();
    	//$testModel->setReaddb('count');
    	$mdl_cmt->setCache(false);
    	#得到搜索
    	
    	$start_time=$this->request['start_time'];
    	$end_time=$this->request['end_time'];
    	$tel=$this->request['tel'];
    	$pdt_names=$this->request['pdt_names'];
    	$pdt_type1=$this->request['pdt_type1'];
    	$favorite=$this->request['favorite'];
    	$areas=$this->request['areas'];
    	$srow=array('start_time'=>$start_time,'end_time'=>$end_time,'tel'=>$tel,'pdt_names'=>$pdt_names,'pdt_type1'=>$pdt_type1,'favorite'=>$favorite,'areas'=>$areas);
 
    	#得到搜索关键字列表
    	//$data = $mdl_keywords->getListAll($page,$srow);
    	$data = $mdl_cmt->getListAllSen($page, $srow);
    	#数据临时处理
    	$list=$data['list'];
    	for($i=0;$i<count($data['list']);$i++){    	
    	    $this->request['id']=$list[$i]['cmt_id'];
    	    $list[$i]['del_url']=$url=SRoute::createUrl('comments/senior', array('page' => $page,'id'=>$list[$i]['cmt_id']), $this->request);
    	}    	 
    	#设置好要放入模版的数据（变量）
    	$param[LIST_VAR_NAME] = $list;
    	$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);
    	$param['srow']=$srow;
    	$this->preparRow($srow);
    	//return $this->render($this->tplFilePath,$param);
    	return $this->template($param);
    }
    
    /**
     * 显示 站内来电信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    function pageCall($inPath){
    	$page=$this->getPageNumber($inPath);
    	#实例化 关键字管理 数据模型
    	$mdl_cmt = new Model_cmt_comments();
    	//$testModel->setReaddb('count');
    	$mdl_cmt->setCache(false);
    	#得到搜索
    	$start_time=$this->request['start_time'];
    	$end_time=$this->request['end_time'];
    	$srow=array('start_time'=>$start_time,'end_time'=>$end_time);
    	
    	#得到搜索关键字列表
    	//$data = $mdl_keywords->getListAll($page,$srow);
    	$data = $mdl_cmt->getListAllCall($page, $srow);
    	#数据临时处理
    	$list=$data['list'];
    	for($i=0;$i<count($data['list']);$i++){
    			$this->request['id']=$list[$i]['cmt_id'];
    			$list[$i]['del_url']=$url=SRoute::createUrl('comments/call', array('page' => $page,'id'=>$list[$i]['cmt_id']), $this->request);
    	}
    	#设置好要放入模版的数据（变量）
    	$param[LIST_VAR_NAME] = $list;
    	$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);
    	$param['srow']=$srow;
    	$this->preparRow($srow);
    	//return $this->render($this->tplFilePath,$param);
    	return $this->template($param);
    }
    /**
     * 分配 信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    private function preparRow($srow)
    {
    	if($srow)
    	{
    		//选中设置    
    		$is_read_checked=array($srow['is_read']=>"checked='checked' "); 
    		$favorite_checked=array($srow['favorite']=>"checked='checked' ");
    		
    		$this->ass('is_read_checked',$is_read_checked);
    		$this->ass('favorite_checked',$favorite_checked);
      	  
    		$this->ass('srow',$srow);
    	}
    }
    
    /**
     * 已读 信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    function pageReadAll()
    {
    	
    	$mdl_cmt = new Model_cmt_comments();
    	$ids=$this->request['check'];      
    	$result=$mdl_cmt->readRowByIds($ids);
    	if($result)
    	{
    		$this->showMessage('标记成功',1,'_reload');
    	}
    	else
    	{
    		$this->showMessage('标记失败',3);
    	}
    	return $this->template();
    }
    
    /**
     * 已读 信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    function pageNoReadAll()
    {
    	 
    	$mdl_cmt = new Model_cmt_comments();
    	$ids=$this->request['check'];
    	$result=$mdl_cmt->noreadRowByIds($ids);
    	if($result)
    	{
    		$this->showMessage('标记成功',1,'_reload');
    	}
    	else
    	{
    		$this->showMessage('标记失败',3);
    	}
    	return $this->template();
    }
    
    /**
     * 是否收藏
     *
     */
    function pageCollectAll()
    {
    	$mdl_cmt = new Model_cmt_comments();
    	$ids=$this->request['check'];  
    	
    	$result=$mdl_cmt->collectallRowByIds($ids);
    	if($result)
    	{
    		$this->showMessage('收藏成功',1,'_reload');
    	}
    	else
    	{
    		$this->showMessage('收藏失败',3);
    	}
    	return $this->template();
    }
    
    /**
     * 改变是否收藏
     *
     */
    function pageCollection()
    {   
    	
    	$cmt_id=intval($this->request['id']);
    	$favorite=$this->request['fid'];
    	$type=$this->request['type'];
          if($type==1){
          	    $mdl_agent=new Model_agent_books();
			    	if($favorite==0){
			    		  $row['favorite']=1;
			    	      $result=$mdl_agent->updateRowById($row,$cmt_id);   	           	        
					    	if($result)
					    	{
					    		$this->showMessage('标记成功',1,'_reload');
					    	//	$this->showMessage('标记成功',1);
					    	}
					    	else
					    	{
					    		$this->showMessage('失败1',3);
					    	}
			    	}else{
			    		$row['favorite']=0;
			    		$result=$mdl_agent->updateRowById($row,$cmt_id);
			    		if($result)
			    		{
			    			$this->showMessage('标记成功',1,'_reload');
			    		}
			    		else
			    		{
			    			$this->showMessage('失败2',3);
			    		}
			    		
			    	}
          }else{
          	    $mdl_cmt = new Model_cmt_comments();
		          	if($favorite==0){
		          		$row['favorite']=1;
		          		$result=$mdl_cmt->updateRowById($row,$cmt_id);
		          		if($result)
		          		{
		          			$this->showMessage('标记成功',1,'_reload');
		          			//	$this->showMessage('标记成功',1);
		          		}
		          		else
		          		{
		          			$this->showMessage('失败1',3);
		          		}
		          	}else{
		          		$row['favorite']=0;
		          		$result=$mdl_cmt->updateRowById($row,$cmt_id);
		          		if($result)
		          		{
		          			$this->showMessage('标记成功',1,'_reload');
		          		}
		          		else
		          		{
		          			$this->showMessage('失败2',3);
		          		}
          		 
                	}
          	          	         	          	
          }	    	
    	return $this->template();
    }
    
    
    
        
}