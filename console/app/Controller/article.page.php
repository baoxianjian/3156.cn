<?php
/**
* @name: 资讯
* @author: wukai
* @date: 08:32 2015/4/13
*/        

class Controller_article extends Controller_basepage {
    
	//列表页
	function pageList($inPath){
		
		
        #得到当前页码
        $page=$this->getPageNumber();
		#实例化 分类名称 数据模型
		$mdl_article_news = new Model_news_news();
		$mdl_arttype = new Model_news_type();
		$mdl_article_news->setCache(false);


		#搜索
		$srow=$this->request;
		$condition = 'is_del = 0';
		
		if(!empty($srow['is_del'])){
			$condition = 'is_del = '.$srow['is_del'];
		}
		if(!empty($srow['type_id2'])){
			$typedatas = $mdl_arttype->getRowByOne(array('name'=>$srow['type_id2']));
			$condition .= ' and type_id2 = '.$typedatas['nt_id'];
		}
		if($srow['keyword']){
			$condition .= ' and title like "%'.$srow['keyword'].'%"';
		}
		
		if($srow['com_id']){
			$condition .= ' and com_id = '.$srow['com_id'];
		}
		
		if($srow['stime']){
			$condition .= ' and dateline >= '.strtotime($srow['stime']);
		}
		
		if($srow['etime']){
			$etime = strtotime($srow['etime'])+86399;
			$condition .= ' and dateline <= '.$etime;
		}
		
		if($srow['admin_name']){
			$condition .= ' and admin_name like "%'.$srow['admin_name'].'%"';
		}
		
		if($srow['is_html']){
			switch ($srow['is_html']){
				case 1:
					$condition .= ' and audit_state = 1 and is_html = 0';
					break;
				case 2:
					$condition .= ' and audit_state = 2 and is_html = 0';
					break;
				case 3:
					$condition .= ' and audit_state = 2 and is_html = 1';
					break;
				case 4:
					$condition .= ' and audit_state = 3';
					break;
			}
		}
		
		if($condition == '1'){
			$condition = array('is_del'=>0);
		}
		
		#得到搜索列表
		$data = $mdl_article_news->getListAll($page, $condition);
		$data['count'] = $mdl_article_news->count($condition);
		#数据临时处理
		$list=$data['list'];
		foreach($list as $key=> $value){
			$type_data = $mdl_arttype->getRowById($value['type_id2']);
			$list[$key]['type_name'] = $type_data['name'];
			$list[$key]['del_url'] = $url=SRoute::createUrl('article/del', array('page' => $page,'id'=>$value['news_id']), $this->request);
		}
		#设置好要放入模版的数据（变量）
		$param[LIST_VAR_NAME] = $list;
		$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath,30);

		$this->ass('sa_name',$this->saName);
		$param['srow']=$srow;
		$param['is_del']=$srow['is_del'];
        return $this->template($param);
	}
    
    //编辑 增加
	function pageEdit(){
		$id=intval($this->request['id']);
		$mdl_article_news = new Model_news_news();
		$mdl_newpage = new Model_news_page();
		
		//将昵称显示到auther字段
		$mdl_admin=new Model_sys_admins();
		$row_admin=$mdl_admin->getRowByAdminName($this->saName);		
		$this->ass('nick_name', $row_admin['nick_name']);	
		
		//如果有保存请求		
		if($this->request['_ac']=='save'){
			$row = $this->request['row'];			
			
			$row['content'] = htmlspecialchars_decode($row['content']);
			$pic_flag = $this->request['pic_flag'];
			$flashatt = $this->request['flashatt'];
			if(is_array($flashatt)){
				foreach($flashatt as $value){
					$row['pic'] = $value['path'];
				}
			}
			if(empty($row['pic']) && $pic_flag){
				$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/"; 
				preg_match_all($pattern, $row['content'], $match);
				if(count($match[0])>=1){
					$row['pic'] = $match[1][0];
				}
			}
			$passed = true;    
			if($id && $passed){
				//修改
				$this->ass('TIP_NAME','修改');
				$content = $row['content'];
				$row['com_id'] = intval($row['com_id']);
				$row['pdt_id'] = intval($row['pdt_id']);
				if($row['start_time']){
					$row['start_time'] = strtotime($row['start_time']);
				}else{
					$row['start_time'] = intval($row['start_time']);
				}
				if($row['end_time']){
					$row['end_time'] = strtotime($row['end_time']);
				}else{
					$row['end_time'] = intval($row['end_time']);
				}
				$row['pdt_id'] = intval($row['pdt_id']);
				unset($row['content']);
				$result=$mdl_article_news->updateRowById($row,$id);
				if($result || $content)
				{
					$newpage_data = $mdl_newpage->getRowByRow(array('news_id'=>$id));
					if($newpage_data['news_id']){
						$mdl_newpage->updateRowByRow(array('content'=>$content), array('news_id'=>$id, 'page_no'=>1));
					}else{
						$res = $mdl_newpage->addRow(array('news_id'=>$id, 'page_no'=>1, 'content'=>$content, 'dateline'=>NOW)); 
					}
					$this->showMessage('修改成功', 1, '/article/list');
				}else{
					 $this->showMessage('修改失败',3, '/article/edit-id-'.$id); 
				}
			}else if($passed){
				//添加
				$this->ass('TIP_NAME','添加');
				if(empty($row['type_id2'])){
					$this->showMessage('分类不能为空',3);
				}else if(empty($row['title'])){
					$this->showMessage('标题不能为空',3);
				}else if(empty($row['origin'])){
					$this->showMessage('来源不能为空',3);
				}else if(empty($row['keyword'])){
					$this->showMessage('检索关键词不能为空',3);
				}else if(empty($row['description'])){
					$this->showMessage('新闻摘要不能为空',3);
				}else if(empty($row['content'])){
					$this->showMessage('正文不能为空',3);
				}
				$row['dateline']=NOW;
				$row['admin_id'] = $this->saId;
				$row['admin_name'] = $this->saName;
				
				$row['com_id'] = intval($row['com_id']);
				$row['pdt_id'] = intval($row['pdt_id']);
				
				$mdl_arttype = new Model_news_type();
				$type_data = $mdl_arttype->getRowById($row['type_id2']);
				
				//统计文章总数
				$counts = $mdl_article_news->count(array('type_id2'=>$row['type_id2']));
				$mdl_arttype->updateRowById(array('count'=>$counts+1), $row['type_id2']);
				$_counts = $mdl_article_news->count(array('type_id1'=>$type_data['parent_id']));
				$mdl_arttype->updateRowById(array('count'=>$_counts+1), $type_data['parent_id']);
				
				$row['type_id1'] = $type_data['parent_id'];
				if($row['start_time']){
					$row['start_time'] = strtotime($row['start_time']);
				}else{
					$row['start_time'] = intval($row['start_time']);
				}
				if($row['end_time']){
					$row['end_time'] = strtotime($row['end_time']);
				}else{
					$row['end_time'] = intval($row['end_time']);
				}
				$row['pdt_id'] = intval($row['pdt_id']);
				
				$pic_flag = $this->request['pic_flag'];
				$flashatt = $this->request['flashatt'];
				if(is_array($flashatt)){
					foreach($flashatt as $value){
			
						$row['pic'] = $value['path'];

					}
				}
				
				
				if(empty($row['pic']) && $pic_flag){
					$pattern="/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png]))[\'|\"].*?[\/]?>/"; 
					preg_match_all($pattern, $row['content'], $match);
					if(count($match[0])>=1){
						$row['pic'] = $match[0][0];
					}
				}
				$content = $row['content'];
				unset($row['content']);
				$result=$mdl_article_news->addRow($row); 

				if($result){
					$mdl_newpage->addRow(array('news_id'=>$result, 'page_no'=>1, 'content'=>$content, 'dateline'=>NOW)); 
					$this->showMessage('添加成功', 1, '/article/list'); 
				}else{
					$this->showMessage('添加失败', 3); 
				}
			}   
		}else{
			if($id){
				//修改初始化
				$row = $mdl_article_news->getRowById($id);
				$mdl_arttype = new Model_news_type();
				$type_data = $mdl_arttype->getRowById($row['type_id2']);
				$up_type_data = $mdl_arttype->getRowById($type_data['parent_id']);
				$newpage_data = $mdl_newpage->getRowByRow(array('news_id'=>$row['news_id']));
				$row['type_name'] = $type_data['name'];
				$row['type_flag'] = $type_data['type_flag'];
				$row['up_type_name'] = $up_type_data['name'];
				if($newpage_data['content']){
					$row['content'] = $newpage_data['content'];
				}
				if($savetype != "sub"){
					$this->ass('TIP_NAME','修改');
					$this->ass('AC_NAME','modify');
				}

			}else{
				//添加初始化
				$this->ass('TIP_NAME','添加');
				$this->ass('AC_NAME','add');

			}  
		}
		if($row){
			$this->ass('row',$row);
		}


		$this->ass('id', $id);
        return $this->template();
	}
    
    /**
    * 删除
    * 
    */
	function pageDel(){
		$mdl_article_news = new Model_news_news();
		$id=$this->request['id'];
		$ids=$this->request['check'];
		$status=$this->request['status'];
		$stype=$this->request['stype'];
		if(is_array($ids)){
			if($stype == 'is_html'){
				$_ids = array();
				foreach($ids as $val){
					$_data = $mdl_article_news ->getRowById($val);
					if($_data['audit_state'] == 2){
						$_ids[] = $val;
					}else{
						$__ids = $val;
					}
				}
				if(count($_ids)){
					//删除
					$mdl_article_news->deleteRowById($_ids, array($stype=>$status)) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'提交成功，'.implode(',', $__ids)."未通过审核暂时无法发布！"))) : SUtil::success('提交成功，'.implode(',', $__ids)."未通过审核暂时无法发布！") ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'提交失败'))) : SUtil::error('提交失败') );
				}else{
					die(json_encode(array('status'=>0,'info'=>'提交失败，'.implode(',', $__ids)."未通过审核暂时无法发布！")));
				}
			}else{
				//删除
				$mdl_article_news->deleteRowById($ids, array($stype=>$status)) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'提交成功'))) : SUtil::success('提交成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'提交失败'))) : SUtil::error('提交失败') );
			}
		}else{
			
			$_data = $mdl_article_news ->getRowById($id);
			$result=$mdl_article_news->deleteRowById($id);
			if($result){
				$this->showMessage('删除成功',1,'_reload');
			}else{
				$this->showMessage('删除失败',3);
			}
		}
		return $this->template();
    }
    
    /**
     * 刷新网址
     *
     */
    function pageCdn(){
    	
    	   	
       $dates=$this->request["dates"];                      //接收单条网址      
                          
       $xmlrpc;
       $path="http://xmlrpc.powercdn.com/user"; //请求路径;
       $port="80"; //请求端口
       $username="69302011";//验证用户名
       $password="000000";//验证密码
       $method="cache.refreshList"; //批量请求方法
       $methodonly="cache.refresh"; //单个网址请求方法
           
        //实例化                   
       $mdl_xmlrpc= new CI_Xmlrpc(array());
       $server=$mdl_xmlrpc->server($this->path, $this->port);
       $mdl_xmlrpc->client->auth($this->username,md5($this->password));
       
       if(is_array($datas)){
       	$this->xmlrpc->method($this->method); //'account.accountLogin'
       }else{
       	$this->xmlrpc->method($this->methodonly); //'account.accountLogin'
       }
       
       $this->xmlrpc->request(array($datas));
       
       if ( ! $this->xmlrpc->send_request())
       {
       	echo $this->xmlrpc->display_error();
       	return "false";
       } else {
       	return $this->xmlrpc->display_response();
       }                    
                           
                           
                           
                           //接收批量网址 
/*               
       $res=Test::rpcRefresh($dt);                    //刷新单条网址
       if($res){
       	$this->showMessage('网址刷新成功！',1,'_reload');
       }else{
       	$this->showMessage('网址刷新失败！',3);
       }
       
       $pl=explode(',',trim($pl));
        
       $res=Test::rpcRefreshList($dt);               //批量刷新网址
       if($res){
       	$this->showMessage('网址刷新成功！',1,'_reload');
       }else{
       	$this->showMessage('网址刷新失败！',3);
       }
       
      
*/        
        
        
        
                           
     return $this->template();
    }
        
    /*
     * 单条刷新网址 for example www.3158.cn
    */
    public static function  rpcRefresh($url){
    	echo $this->rpcRefreshRpc($url);
    }
    /*
     * 批量刷新网址 for example 　array("www.3158.cn","3156.test")
    */
    public static function  rpcRefreshList($urls){
    	echo $this->rpcRefreshRpc(array($urls,"array"));
    }
    
    function index(){
    	$this->rpcRefreshList(array("3.com","3.3.com"));
    }
    
}
