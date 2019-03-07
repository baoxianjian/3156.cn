<?php
/**
* @name: 留言管理
* @author: zhanghao
* @date: 17:30 2015/4/13
*/        
class Controller_agency extends Controller_basepage {
	//首页
	function pageList($inPath){
		
        #得到当前页码
        $page=$this->getPageNumber($inPath);
        #实例化 关键字管理 数据模型
        $mdl_agency = new Model_user_agency();
        //$testModel->setReaddb('count');
        $mdl_agency->setCache(false);
        
        //如果列表页有子动作——解锁
        if($this->request['ac']=='unlock')
        {
        	$user_id=$this->request['id'];
        	$row['can_login']=1;
        	$row['login_ect']=0;
        	$res=$mdl_agency->updateRowById($row,$user_id);
        	if($res){
        		//SUtil::success("添加成功！");
        	}else{
        		//SUtil::error("失败！");
        	}
        }
        #接收需要搜索的类型
        $user_id=$this->request['user_id'];
        $user_name=$this->request['user_name'];
        $link_man=$this->request['link_man'];
        $mobile=$this->request['mobile'];       
        $srow=array('user_id'=>$user_id,'user_name'=>$user_name,'link_man'=>$link_man,'mobile'=>$mobile);  
        
        #得到搜索关键字列表
        $data = $mdl_agency->getListAll($page,$srow);
        
  
             
        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($data['list']);$i++){            
            $this->request['id']=$list[$i]['user_id'];
            $list[$i]['del_url']=$url=SRoute::createUrl('agency/del', array('page' => $page,'id'=>$list[$i]['user_id']), $this->request);
        }
              
        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath,30);

        $param['srow']=$srow;                
       
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
		$mdl_agency = new Model_user_agency();
		
		$id=$this->request['id'];	
	
			 	$result=$mdl_agency->deleteRowById($id);
		    
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
	 * 删除
	 * add by baoxianjian 19:00 2015/3/28 删除单独了
	 *
	 */
  function pageDels()
	{
	
	    $mdl_agency = new Model_user_agency();
		$ids=$this->request['check'];
		 
		$result=$mdl_agency->deleteRowByIds($ids);
	
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
	 * 重置密码
	 *
	 */
	
	public function pageRepass(){
			
//		$questArr = SUtil::html_arr($this->request);
			
//		$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && die('error');
			
		//die(SUtil::P($_POST));
		$user_id=$this->request['user_id'];			
		//随机生成8位数密码
		for ( $i=0; $i<8; $i++ ){
	
			//随机因子
			$charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
	
			$pass .= $charset[rand(0, strlen($charset)-1)];		
		}			
		//代理商model
		$mdl_user = new Model_user_user();
//		$company_db->setCache(SUtil::C('sqlCache'));//设置缓存
        $row['user_pwd']=md5($pass);
		$res=$mdl_user->updateRowById($row,$user_id);
		//更新密码
		if ($res){
	
			//返回信息数组
			$msg_data['info'] = '以重置8位随机密码'.$pass;
			$msg_data['pass'] = $pass;
	
		}else{
	
			$msg_data['重置密码失败'];
	
		}		
			
		die(json_encode($msg_data));
	}
	
	
	
	
	 
	
}