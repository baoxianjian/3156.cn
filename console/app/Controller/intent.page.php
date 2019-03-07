<?php
header('Content-type:text/html;charset=utf-8');

/**
* @name: 留言管理
* @author: zhanghao
* @date: 17:30 2015/4/13
*/        
class Controller_intent extends Controller_basepage {
	
		function pageList($inPath){		
        
        //得到登录的人         
        $mdl_admin=new Model_sys_admins();        
        $row_admin= $mdl_admin->getRowByAdminName($this->saName);       
        
        
      //得到销售所管的公司id  
       if($row_admin['sa_level']<10000){ 
       //  if($this->saName){   
              $mdl_cmp=new Model_cmp_company();
              $data=$mdl_cmp->getListAllsellercmp($page,$row_admin['sa_id']);      
              $cmp_list=$data['list'];
      //将销售负责的公司id付给一个数组   
              foreach($cmp_list as $k=>$v ){                  
                   $cmp_ids[]= $v['cmp_id'];                                     
              } 
               
                                    
         }
         
     
         
                	
		#得到当前页码
		$page=$this->getPageNumber($inPath);
		#实例化 关键字管理 数据模型
		$mdl_cmt = new Model_agent_agent(); //被动	
	
		#接收需要搜索的类型
		$dateline=$this->request['dateline'];
		$pdt_name=$this->request['pdt_name'];
		$areas=$this->request['areas'];
		$cmp_name=$this->request['cmp_name'];
		$link_man=$this->request['link_man'];
		$tel=$this->request['tel'];
		$mp=$this->request['mp'];
		$user_id=$this->request['user_id'];
		$sa_id=$this->request['sa_id'];
		$is_del=$this->request['is_del'];
		$online_state=$this->request['online_state'];//1
		$audit_state=$this->request['audit_state'];
	
		$siteid=$this->request['siteid'];//1
		$webid=$this->request['webid'];//1
		$modtype=$this->request['modtype'];//1
		$modid=$this->request['modid'];//1
		$notice_type=$this->request['notice_type'];//1
			
		$this->preparRow($online_state);
		$srow=array('dateline'=>$dateline,'pdt_name'=>$pdt_name,'areas'=>$areas,'cmp_name'=>$cmp_name,'tel'=>$tel,'mp'=>$mp,'link_man'=>$link_man,'user_id'=>$user_id,'sa_id'=>$sa_id,'online_state'=>$online_state,'audit_state'=>$audit_state,'is_del'=>$is_del, 'site_id'=>$siteid, 'web_id'=>$webid, 'mod_type'=>$modtype, 'mod_id'=>$modid);

		
		
		#得到搜索关键字列表
		  $data = $mdl_cmt->getListAgentcomments($page, $srow,$cmp_ids);
		
			#数据临时处理
			$list=$data['list'];
			$mdl_area = new Model_com_area();
			foreach($data['list'] as $key=>$value){
			$list[$key]['start_time']=SUtil::formatTime($list[$i]['start_time']);
			$list[$key]['end_time']=SUtil::formatTime($list[$i]['end_time']);
			$list[$key]['del_url']=$url=SRoute::createUrl('intent/del', array('page' => $page,'id'=>$value['id'],'t'=>$value['type']), $this->request);
			$list[$key]['rec_url']=$url=SRoute::createUrl('intent/rec', array('page' => $page,'id'=>$value['id'],'t'=>$value['type']), $this->request);
			$list[$key]['cmp_name']=SString::dcutstr($value['cmp_name'],15);
			$list[$key]['pdt_name']=SString::dcutstr($value['pdt_name'],15);
					 
			}
	
			#设置好要放入模版的数据（变量）
					$param[LIST_VAR_NAME] = $list;
							$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath,30);
	
									$param['srow']=$srow;
			
									//默认ad/ad_list.html
									#调用相应模版
	
									return $this->render($this->tplFilePath,$param);
		}

	
	function pageDel()
	{	
		$id=$this->request['id'];			
		
		$mdl_cmt_comments = new Model_cmt_comments();
			$result=$mdl_cmt_comments->deleteRowById($id);
	
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
	 * 批量删除被动留言
	 *
	 */
	function pageDelallcmts()
	{
	
		$ids=$this->request['check'];
	
		$mdl_cmt_comments = new Model_cmt_comments();
		$result2=$mdl_cmt_comments->deleteRowByIds($ids);
	
	
		if($result2)
		{
		$this->showMessage('删除成功!',1,'_reload');
		}
		else
			{
			$this->showMessage('删除失败!',3,'_reload');
		}
	
		return $this->template();
		}
	
	/**
	 *
	 * 批量审核被动留言
	 *
	 */
	function pageExamineallcmts()
	{
	
		$ids=$this->request['check'];
				
		$mdl_agent = new Model_cmt_comments();
		$result2=$mdl_agent->examineRowByIds($ids);
		
		if($result2)
		{
		$this->showMessage('审核成功!',1,'_reload');
		}
		else
		{
		$this->showMessage('审核失败!',3,'_reload');
		}
	
	
			return $this->template();
		}
	
		/**
		 *
		 * 批量取消审核被动留言
		 *
		 */
		function pageUnexamineallcmts()
		{
		
			$ids=$this->request['check'];
			
			$mdl_agent = new Model_cmt_comments();
			$result2=$mdl_agent->unexamineRowByIds($ids);
		
		
			if($result2)
			{
			$this->showMessage('取消审核成功!',1,'_reload');
			}
		else
			{
			$this->showMessage('取消审核失败!',3,'_reload');
			}
		
		
			return $this->template();
		}
		
	
		/**
		 *
		 * 批量恢复被动留言
		 *
		 */
		function pageReallcmts()
		{
				
		
			$ids=$this->request['check'];
			
			$mdl_agent = new Model_cmt_comments();
			$result2=$mdl_agent->recoveryRowByIds($ids);
		
		
			if($result2)
			{
			    $this->showMessage('恢复成功!',1,'_reload');
			}
			else
			{
				$this->showMessage('恢复失败!',3,'_reload');
			}
		
		
				return $this->template();
		}
		
	
	/**
	 * 
	 * 恢复单条主动留言
	 *
	 */
	function pageRecagent()
	{			
		$id=$this->request['id'];
					
			$mdl_books=new Model_agent_books();	
			$row['is_del']=0;
			$result=$mdl_books->updateRowById($row,$id);
			if($result)
			{
				$this->showMessage('恢复成功',1,'_reload');
			}
			else
			{
				$this->showMessage('恢复失败',3);
			}
		
		return $this->template();
	}
	/**
	 *
	 * 恢复单条被动留言
	 *
	 */
	function pageRec()
	{
		//如果在列表页删除s数据
		$mdl_agent = new Model_agent_agent();
	
		$id=$this->request['id'];
		
	
			$row['is_del']=0;
			$result=$mdl_agent->updateRowById($row,$id);
	
			if($result)
			{
				$this->showMessage('恢复成功',1,'_reload');
			}
			else
			{
				$this->showMessage('恢复失败',3);
			}
		
		return $this->template();
	}
	 
	/**
	 * 
	 * 添加
	 *
	 */
    function pageAdd(){
	          
           $mdl_books = new Model_agent_books();
           
           //获得一级产品类型
           $mdl_pdtype=new Model_pdt_pdtTypes();
           $data=$mdl_pdtype->getListAlltypeone($page);
           $tplist=$data['list'];
           $this->ass('tplist', $tplist);
                    
                                
            //如果有保存请求
            if($this->request['_ac']=='save')
            {
                $row=$this->request['row'];
                $row['sa_id']=$this->saId; 
                $row=SUtil::html_arr($row);
                
                //处理地区
                $mdl_area = new Model_com_area();
                if($row['areas_code'])
                {
                	foreach ($row['areas_code'] as $v)
                	{
                		$areas_code .=','.$v;
                		$areas_str .=','.$mdl_area->getNameByCodeId($v);
                	}
                	$areas_code = ltrim($areas_code,',');
                	$areas_str = ltrim($areas_str,',');
                
                	$a = $mdl_area->getSplitedCodeIds($row['areas_code'][0]);
                
                	$row['province'] = $mdl_area->getNameByCodeId($a['0'].'0000');
                	$row['areas'] =  $areas_str;
                	$row['areas_code'] = $areas_code;
                	$row['areas_code1']=$a[0];
                }
                
                
                //与数据库类型一致 
                $row['audit_state']=2;           
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
                if(!$row['link_man']) {
                	SUtil::error("提交失败，联系人不能为空！");
                	exit;
                }
         //       if(!$row['tel']) {
         //       	SUtil::error("提交失败，联系方式不能为空！");
         //       	exit;
         //       }
                if(!$row['qq']) {
                	SUtil::error("提交失败，qq不能为空！");
                	exit;
                }
                if(!$row['pdt_type1']) {
                	SUtil::error("提交失败，产品类型不能为空！");
                	exit;
                } 
                if(!$row['content']) {
                	SUtil::error("提交失败，留言内容不能为空！");
                	exit;
                }                        
                    //添加            
            	    $row['dateline']=NOW;
                //	$row['dateline']=SUtil::formatTime(NOW,3);
                    $result=$mdl_books->addRow($row); 
                    if($result)
                    {
                         $this->showMessage('添加成功');
                    }
              
                
            }else{
            	
            }
          
	        $this->preparRow($row);
            $this->ass('id',$id); 
            $this->ass('row',$row);
            return $this->template();
	    }
	
	    /**
	     *
	     * 批量留言
	     *
	     */

	    public function pagedoFileInto(){
	    	$mdl_books = new Model_agent_books();
	    	//上传类型
	    	//$uploadType = array('application/vnd.ms-excel');
	    	//	die(SUtil::C('console_uploadFile_path'));
	    	//$upfile_result = SUtil::uploadFile(SUtil::C('console_uploadFile_path'), 'upload', $uploadType);
	    		
	    	//$upfile_result['status'] == 0 && SUtil::error($upfile_result['info']);
	    		
	    	//打开上传文件
	    	//$file = file($_SERVER['DOCUMENT_ROOT'].$upfile_result['path']);
	    	//转码
	    	/*foreach ( $file as  &$v ){
	    		$v = iconv("GB2312","UTF-8",$v);
			}*/
			$filename = $_FILES['upload']['tmp_name'];
			$filenames = pathinfo($_FILES['upload']['name']);
			if($filenames['extension']!= 'csv'){
				SUtil::error('请选择要导入的CSV文件');
			}
			if (empty ($filename)) {
				SUtil::error('请选择要导入的CSV文件');
			} 
			$handle = fopen($filename, 'r'); 
			$file = $this->input_csv($handle); //解析csv 
			$len_result = count($file); 
			if($len_result==0){
				SUtil::error('没有任何数据');
			}
	    	//产品类型
	    	$pdt_type1 = array(
	    				
	    			'OTC'=>1,
	    			'保健品'=>2,
	    			'医疗器械'=>3
	    				
	    	);
	
	    	//卸载标题
	    	unset($file[0]);
	    	
	    	//print_r($file);
	
	    	
	    	//	die(SUtil::P($file));
	    	//循环插入数据
	    	foreach ( $file as $v2 ){
	    		$sql = $v2;
				$pdt_name = trim(iconv('gb2312', 'utf-8', $sql[0]));
				$pdt_type1 = intval(trim(iconv('gb2312', 'utf-8', $sql[1])));
				$areas = trim(iconv('gb2312', 'utf-8', $sql[2]));
				$link_man = trim(iconv('gb2312', 'utf-8', $sql[3]));
				$tel = trim(iconv('gb2312', 'utf-8', $sql[4]));
				$qq = trim(iconv('gb2312', 'utf-8', $sql[5]));
				$content = trim(iconv('gb2312', 'utf-8', $sql[6]));
				
	    		$sqlArr['pdt_name'] = $pdt_name == NULL ? SUtil::error('请填写产品名称后再上传') : $pdt_name;
				if(!is_int($pdt_type1)){
					SUtil::error('产品类型必须填写对应ID');
				}else{
					$sqlArr['pdt_type1'] = $pdt_type1 == NULL ? SUtil::error('请填写产品类型后再上传') : $pdt_type1;
				}
	    		$sqlArr['areas'] = $areas == NULL ? SUtil::error('请填写意向区域后再上传') : $areas;
	    		$sqlArr['link_man'] = $link_man == NULL ? SUtil::error('请填写联系人后再上传') : $link_man;
	    		$sqlArr['tel'] = $tel == NULL ? SUtil::error('请填写联系电话后再上传') : $tel;
	    		$sqlArr['qq'] = $qq == NULL ? SUtil::error('请填写qq后再上传') : $qq;
	    		$sqlArr['content'] = $content == NULL ? SUtil::error('请填写代理留言后再上传') : $content;
	             
	    		$sqlArr = array_filter($sqlArr);
	    		$sqlArr['dateline']=NOW;	    
	    		$mdl_books->insert($sqlArr) <= 0 && SUtil::error('导入失败');
	    	}
 
	    	SUtil::success('导入成功');
	    		
	    }

	    /**
	     * 下载模板处理方法
	     */
	    public function pagedownload(){
	    		
	    	SUtil::download(SUtil::C('console_download_path'), 'msg.csv');
	    		
	    }
	   
	   
	    /**
	     * 备注被动留言
	     */
	    public function pageCheck($inPath){
	    
	    	$id=intval($this->request['id']);
	    	//如果有保存请求
	    	if($this->request['_ac']=='save')
	    	{
	    		$row=$this->request['row'];
	    		$mdl_agent = new Model_agent_agent();	    	    		 
	    		$res=$mdl_agent->updateRowById($row,$id);
	    
	    		if($res)
	    		{
	    			$this->showMessage("操作成功",1);
	    		}else{
	    			$this->showMessage("提交失败！",3);
	    		}
	    
	    	}
	    	else
	    	{
	    	    		
	    			$mdl_agent = new Model_agent_agent();
	    			$row=$mdl_agent->getRowById($id);
	    			$this->ass('row',$row);
	    		    	    
	    	}
	    	$this->ass('row',$row);
	    	
	    	$this->ass('id',$id);
	    	$this->preparRow($srow);
	    	return $this->template();
	    }
	   
	    /**
	     *编辑被动留言
	     */
	    
	    public function pageEdit($inPath){
	    
	    	//获得一级产品类型
	    	$mdl_pdtype=new Model_pdt_pdtTypes();
	    	$data=$mdl_pdtype->getListAlltypeone($page);
	    	$tplist=$data['list'];
	    	$this->ass('tplist', $tplist);
	    
	    
	    
	    
	    	$id=intval($this->request['id']);
	    	
	    
	    	if($this->request['ac']=='save'){
	    
	    		$row=$this->request['row'];
	    		 
	    		//img/uploads/guestbook
	    		$mdl_area = new Model_com_area();
	    		if($row['areas_code'])
	    		{
	    			foreach ($row['areas_code'] as $v)
	    			{
	    				$areas_code .=','.$v;
	    				$areas_str .=','.$mdl_area->getNameByCodeId($v);
	    			}
	    			$areas_code = ltrim($areas_code,',');
	    			$areas_str = ltrim($areas_str,',');
	    
	    			$a = $mdl_area->getSplitedCodeIds($row['areas_code'][0]);
	    
	    			$row['province'] = $mdl_area->getNameByCodeId($a['0'].'0000');
	    			$row['areas'] =  $areas_str;
	    			$row['areas_code'] = $areas_code;
	    			$row['areas_code1']=$a[0];
	    		}
    		
	    			$mdl_agent=new Model_agent_agent();
	    
	    			$channel=$this->request['channel'];
	    			$row['channel']=implode(',', $channel);
	    			
	    			$up=$mdl_agent->updateRowById($row,$id);
	    			if($up){
	    				 
	    				SUtil::success("修改成功！");
	    			}else{
	    				 
	    				SUtil::success("修改成功！");
	    			}
	    	
	    		 
	    	}else{
	    		
	    			$mdl_cmt_comments = new Model_cmt_comments();
	    			$mdl_area = new Model_com_area();
	    			$row=$mdl_cmt_comments->getRowById($id);
	    			 
	    			$areas_array = explode(',', $row['areas']);
	    			foreach($areas_array as $k=>$val){
	    				$_areas = $mdl_area->getNameByCodeId($val);
	    				$areas_arrays[$k] = $_areas;
	    			}
	    			$row['area_list'] = implode(',', $areas_arrays);
	    
	    
	    
	    	}
	    
	    	$this->preparRow($row);
	    
	    	$this->ass('id', $id);
	    	$this->ass('type', $type);
	    	return $this->template();
	    }
	    /**
	     * 审核
	     */
	    public function pageExamine(){  
	    	//如果在列表页删除s数据
	    	$mdl_books= new Model_agent_books();
	    	$ids=$this->request['check'];
	    	
	    	
	    	$result=$mdl_books->examRowByIds($ids);
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
	     * 设置选中值
	     */
	    private function preparRow($row)
	    {
	    	if($row)
	    	{
	    		//选中设置
	    	
	    	
	    		$agent_type_checked=array($row['agent_type']=>"checked='checked'");
	    		$end_time_checked=array($row['end_time']=>"checked='checked'");
	    		$channel_checked=array($row['channel']=>"checked='checked'");
	    		$pdt_types_checked=array($row['pdt_types']=>"checked=true");
	    		    			    			    		
	    		$list=explode(',', $row['channel']); //0=>1,1=>2,2=>3
	    		foreach ($list as $v)
	    		{
	    			$list[$v]="checked='checked'";
	    		}	    		

	                $row['areas_code']=trim($row['areas_code'],',');
	                $areas_list=explode(',',$row['areas_code']);
	                $areas_str = json_encode($areas_list);
	                $this->ass('areas_str',$areas_str);
                    
                    //$this->ass('areas_code1',$row['areas_code1']);
                    
                   
                    
		
	    		$this->ass('agent_type_checked',$agent_type_checked);
	    		$this->ass('end_time_checked',$end_time_checked);
	    		$this->ass('channel_checked',$list);
	    		$this->ass('pdt_types_checked',$pdt_types_checked);
	    	
	    		
	    		$this->ass('row',$row);
	    	}
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
	    
	    public function input_csv($handle) { 
			$out = array (); 
			$n = 0; 
			while ($data = fgetcsv($handle, 10000)) { 
				$num = count($data); 
				for ($i = 0; $i < $num; $i++) { 
					$out[$n][$i] = $data[$i]; 
				} 
				$n++; 
			} 
			return $out; 
		} 
	    
}

