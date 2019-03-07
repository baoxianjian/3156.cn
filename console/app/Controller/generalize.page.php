<?php 

	/**
	* @name: 控制台推广管理控制器
	* @author: 张淇钧
	*/
class Controller_generalize extends Controller_basepage {
	
	/**
	 * 推广管理首页视图
	 * @author张淇钧
	 * @param unknown $inPath
	 * @return Ambigous <mixed, string, void, string>
	 */
	public function pageIndex($inPath){
                
        
			$mdl_spreadDb = new Model_search__searchspread();
			$page = $this->getPageNumber($inPath);//获取当前分页
			
			
			//$count = $mdl_spreadDb->getCount("is_del!=1");//获取总数据
			
			#得到搜索
			$questArr = SUtil::html_arr($this->request);
		
			$cmp_id=$questArr['cmp_id'];
			$pdt_id=$questArr['pdt_id'];
			$ss_type=$questArr['ss_type'];
			$keywords=$questArr['keywords'];
			$dateline=$questArr['dateline'];
			$start_time=$questArr['start_time'];
			$end_time=$questArr['end_time'];
			$recommend=$this->request['recommend'];
			$ss_state=$questArr['ss_state'];
            $overdue=$questArr['overdue'];
            $ps=intval($questArr['ps']);
			
			$srow=array('ss_type'=>$ss_type,'cmp_id'=>$cmp_id,'pdt_id'=>$pdt_id,'keywords'=>$keywords,'dateline'=>$dateline,'start_time'=>$start_time,'end_time'=>$end_time,'recommend'=>$recommend,'ss_state'=>$ss_state,'overdue'=>$overdue,'ps'=>$ps);
            $overdue_selected=array($overdue=>'selected="selected"');
            $pdt_state_selected=array($ps=>'selected="selected"');
			
			$results = $mdl_spreadDb->getListAll($page,$srow);//获取查询数据
            
            //根据cmp_id 得到公司名称
            $mdl_cmp=new Model_cmp_company();
		
            #数据临时处理
            $list=$results['list'];
            for($i=0;$i<count($list);$i++){
                
                $list[$i]['overdue']= NOW > intval($list[$i]['end_time'])? "<font color='red'>过期</font>":"正常"; 
                
                $list[$i]['dateline']=SUtil::formatTime($list[$i]['dateline']);
                $list[$i]['start_time']=SUtil::formatTime($list[$i]['start_time']);
                $list[$i]['end_time']=SUtil::formatTime($list[$i]['end_time']);
                
                if($list[$i]['pdt_id'])
                {
                    $list[$i]['pdt_url']=WWW_URL."/product/{$list[$i]['pdt_id']}.shtml";
                    $list[$i]['pdt_state_color']="green";
                    if($list[$i]['pdt_is_del']==1 || $list[$i]['pdt_audit_state']==1 || $list[$i]['pdt_audit_state']==3)
                    {
                        $list[$i]['pdt_state_color']="red";    
                    }
                }
                
                $list[$i]['cmp_id']=intval($list[$i]['cmp_id'])==0?'-':$list[$i]['cmp_id'];
                $list[$i]['pdt_id']=intval($list[$i]['pdt_id'])==0?'-':$list[$i]['pdt_id'];
                
                
   
                $row_cmp=$mdl_cmp->getRowById($list[$i]['cmp_id']);
                $list[$i]['cmp_name']=$row_cmp['cmp_name'];
                
                
                $this->request['id']=$list[$i]['ss_id'];
                $list[$i]['del_url']=$url=SRoute::createUrl('generalize/del', array('page' => $page,'id'=>$list[$i]['ss_id']), $this->request); 
            }
            
         
					
			$param[LIST_VAR_NAME] = $list;
			$param[PAGE_VAR_NAME] = $this->pageBar($results['count'], $page, $inPath);
			$param['srow']=$srow;
            $param['overdue_selected']=$overdue_selected;
            $param['pdt_state_selected']=$pdt_state_selected;  
			#得到搜索关键字列表
		//	$data = $mdl_spreadDb->getListAll($page,$srow);						
			
		
			return $this->template($param);
	}
	
	/**
	 * 上传图片处理方法
	 */
	public function pageUploadFile(){
		$path = SUtil::C('console_upload_path').$_SESSION['console_id'].'/';//设置上传路径
		$upload_result = SUtil::uploadFile($path, $_POST['name']);
		echo json_encode($upload_result);die;
	}
	
	
	/**
	rwaewr	 */
	public function pageOn_off(){
		SUtil::isAjax() || die("非法操作");//判读请求
		$ss_state = $_POST['status'] == 1 ? 2 : 1;
		$ss_id = intval(htmlspecialchars($_POST['id'],ENT_QUOTES));
		$mdl_spreadDb = new Model_search__searchspread();
		if ($mdl_spreadDb->update("ss_id=$ss_id", "ss_state=$ss_state") > 0 ){
			$msg_data['status'] = 1;
		}else{
			$msg_data['status'] = 0;
			$msg_data['info'] = '操作失效';
		}
		die(json_encode($msg_data));
	}
	
	
	/**
	 * 单选（多选）删除处理方法
     * update by baoxianjian 19:00 2015/3/28 删除统一 暂时不需要多个删除
	 */
    function pageDel()
    {    
        //如果在列表页删除s数据
        $mdl_ss = new Model_search__searchspread();     
        $id=$this->request['id'];
        $result=$mdl_ss->deleteRowById($id);
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
     * 单选（多选）删除处理方法
     * update by baoxianjian 19:00 2015/3/28 删除统一 暂时不需要多个删除
     */
    function pageDelall()
    {    
        //如果在列表页删除s数据
        $mdl_ss = new Model_search__searchspread();     
        $ids=$this->request['check'];
        $result=$mdl_ss->deleteRowByIds($ids);
        if($result)
        {
            $this->showMessage('删除成功',1,'_reload');
        }
        else
        {
            $this->showMessage('删除失败',3);
        }
       
    }
        
    /**
     * 过期自动停用
     * add by baoxianjian 10:57 2015/4/15
     */
    function pageStopOver()
    {    
        //如果在列表页删除s数据
        $mdl_ss = new Model_search__searchspread();
        $result=$mdl_ss->autoUpdateStateAll();
        if($result)
        {
            $this->showMessage('自动停止推广状态成功',1,'_reload');
        }
        else
        {
            $this->showMessage('自动停止推广状态失败',3);
        }
        return $this->template();
    }
    
        
       /*
		$mdl_spreadDb = new Model_search__searchspread();
		SUtil::isAjax() || die('error');
		if ( isset($_POST['del']) ){//单选删除
			if ( $mdl_spreadDb->update("ss_id=".htmlspecialchars($_POST['del'],ENT_QUOTES),'is_del=1') > 0 ){
				$msg_data['status'] = 1;
				$msg_data['info'] = '删除成功';
			}else{
				$msg_data['status'] = 0;
				$msg_data['info'] = '删除失败';
			}
		}else{//多选删除
			$postArr = SUtil::html_arr($_POST['check']);
			$delStr = implode(',', $postArr);
			if ( $mdl_spreadDb->update("ss_id in (".$delStr.")", 'is_del=1') > 0 ){
				$msg_data['status'] = 1;
				$msg_data['info'] = '删除成功';
			}else{
				$msg_data['status'] = 1;
				$msg_data['info'] = '删除';
			}
		}
			
		die(json_encode($msg_data));
        */
	
	
	
	/**
	 * 推广编辑（添加、查看）视图
	 */
	public function pageEditGe(){
		header('Content-type:text/html;charset=UTF8');
			
		//编辑
		if ( isset($_GET['id']) ){
			$mdl_spreadDb = new Model_search__searchspread();
			$spread_results = $mdl_spreadDb->getOneData();//获取编辑数据
			$_SESSION['editGe'] = $spread_results;//保存session 便于后续直接判断
			//die(SUtil::P($spread_results));
			$tpl_array['results'] = $spread_results;
		}
        else
        {
            $tpl_array['results']['ss_type'] = '2'; 
            $tpl_array['results']['ss_state'] = '1'; 
            $tpl_array['results']['recommend'] = '1'; 
        }
		$ss_type = SUtil::C('ss_type');//读取配置文件推广类型
		$tpl_array['config_ss_type'] = $ss_type;
		//die(SUtil::P($tpl_array['ss_type']));
		return $this->template($tpl_array);
	}
	
	/**
	 * 推广编辑表单校验
	 */
	public function pageCheckGeInput(){
		SUtil::isAjax() || die("非法操作");//判读请求
		$postName = htmlspecialchars($_POST['name'],ENT_QUOTES);//过滤post
		$postArr = explode('|', $postName);
		switch ( $postArr[0] ){
			case 'cmp_id'://企业id
				if ( SUtil::Is_number($postArr[1]) == NULL ){
					$msg_data['status'] = 0;
					$msg_data['info'] = '企业id格式不对';
				}else{
					$mdl_spreadDb = new Model_search__searchspread();
					$spread_results = $mdl_spreadDb->getOne($postArr[0]."=".$postArr[1],$postArr[0]);
					if ( $spread_results != NULL ){
						$msg_data['status'] = 0;
						$msg_data['info'] = '该企业id已存在';
					}else{
						$msg_data['status'] = 1;
						$msg_data['info'] = '企业id可以使用';
					}
				}
				die(json_encode($msg_data));
				break;
			case 'pdt_id'://产品id
				if ( SUtil::Is_number($postArr[1]) == NULL ){
					$msg_data['status'] = 0;
					$msg_data['info'] = '产品id格式不对';
				}else{
					$mdl_spreadDb = new Model_mall_searchSpread();
					$spread_results = $mdl_spreadDb->getOne($postArr[0]."=".$postArr[1],$postArr[0]);
					if ( $spread_results != NULL ){
						$msg_data['status'] = 0;
						$msg_data['info'] = '产品id已存在';
					}else{
						$msg_data['status'] = 1;
						$msg_data['info'] = '产品id可以使用';
					}
				}
				die(json_encode($msg_data));
				break;
			case 'keywords'://关键词
				if ( empty($postArr[1]) ){
					$msg_data['status'] = 0;
					$msg_data['info'] = '关键词不能为空';
				}else{
					$msg_data['status'] = 1;
					$msg_data['info'] = '关键词可用';
				}
				die(json_encode($msg_data));
				break;
			case 'order'://添加时间
				if ( SUtil::Is_number($postArr[1]) == NULL ){
					$msg_data['status'] = 0;
					$msg_data['info'] = '推广排序格式不对';
				}else{
					$msg_data['status'] = 1;
					$msg_data['info'] = '推广排序可用';
				}
				die(json_encode($msg_data));
				break;
		}
	}
	
	
	/**
	 * 编辑（添加）提交处理方法
	 */
	public function pagedoGeEdit(){
		SUtil::isAjax() || die("非法操作");//判读请求
		$postArr = SUtil::html_arr($_POST);//过滤post数组
        $postArr['pdt_id']=intval($postArr['pdt_id']);
        $postArr['cmp_id']=intval($postArr['cmp_id']);
        
        /*
		if ( SUtil::Is_number($postArr['cmp_id']) == NULL ){
			$msg_data['status'] = 0;
			$msg_data['info'] = '企业id格式错误';
		}elseif ( SUtil::Is_number($postArr['pdt_id']) == NULL ){
			$msg_data['status'] = 0;
			$msg_data['info'] = '产品id格式错误';
		}else
        */
        if ( $postArr['keywords'] == NULL ){
			$msg_data['status'] = 0;
			$msg_data['info'] = '关键字不能为空';
		}elseif ( SUtil::Is_time($postArr['start_time']) == NULL ){
			$msg_data['status'] = 0;
			$msg_data['info'] = '起始时间格式错误';
		}elseif (SUtil::Is_time($postArr['end_time']) == NULL ){
			$msg_data['status'] = 0;
			$msg_data['info'] = '结束时间格式错误';
		}elseif ( SUtil::Is_number($postArr['order']) == NULL ){
			$msg_data['status'] = 0;
			$msg_data['info'] = '推广排序格式错误';
		}elseif ( $postArr['recommend'] != 0 && $postArr['recommend'] != 1 ){
			$msg_data['status'] = 0;
			$msg_data['info'] = '非法提交';
		}elseif ( $postArr['ss_state'] != 0 && $postArr['ss_state'] != 1 ){
			$msg_data['status'] = 0;
			$msg_data['info'] = '非法提交';
		}elseif ( strtotime($postArr['start_time']) > strtotime($postArr['end_time']) ){
			$msg_data['status'] = 0;
			$msg_data['info'] = '起始日期不能大于结束日期';
		}else{
			$ss_type = array_keys(SUtil::C('ss_type'));
			if ( !in_array($postArr['ss_type'], $ss_type) ){
				$msg_data['status'] = 0;
				$msg_data['info'] = '非法提交';
				die(json_encode($msg_data));
			}
	       $mdl_spreadDb = new Model_search__searchspread();   
            /*
			
			if ( $mdl_spreadDb->getOne("cmp_id=".$postArr['cmp_id']) != NULL && $_SESSION['editGe']['cmp_id'] != $postArr['cmp_id'] && $postArr['ss_id'] != NULL ){
				$msg_data['status'] = 0;
				$msg_data['info'] = '该企业id已存在';
				die(json_encode($msg_data));
			}
	
			if ( $mdl_spreadDb->getOne("pdt_id=".$postArr['pdt_id']) != NULL && $_SESSION['editGe']['pdt_id'] != $postArr['pdt_id'] && $postArr['ss_id'] != NULL ){
				$msg_data['status'] = 0;
				$msg_data['info'] = '该产品id已存在';
				die(json_encode($msg_data));
			}
            */
	
			$postArr['start_time'] = strtotime($postArr['start_time']);
			$postArr['end_time'] = strtotime($postArr['end_time']);

            
			$postArr['x__img_url'] = $postArr['upPath'][0];
			unset($postArr['upPath']);
	
			if ( $postArr['ss_id'] != NULL ){//编辑
				$ss_id = $postArr['ss_id'];
				//	unset($postArr['ss_id']);die(SUtil::P($postArr));
				if ($mdl_spreadDb->update("ss_id=".$ss_id, $postArr) !== false){
					$msg_data['status'] = 1;
					$msg_data['info'] = '修改成功';
					unset($_SESSION['editGe']);
				}else{
					$msg_data['status'] = 0;
					$msg_data['info'] = '修改失败';
				}
			}else{//添加
				unset($postArr['ss_id']);
				$postArr['dateline'] = time();
				if ( $mdl_spreadDb->insert($postArr) ){
					$msg_data['status'] = 1;
					$msg_data['info'] = '添加成功';
				}else{
					$msg_data['status'] = 0;
					$msg_data['info'] = '添加失败';
				}
			}
		}
		die(json_encode($msg_data));
	}
	
	
}

?>