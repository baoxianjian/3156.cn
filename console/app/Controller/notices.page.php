<?php 
	
	/**
	 * 产品控制器
	 * @author Administrator
	 *
	 */
	header('Content-type:text/html;charset=utf-8');
	class Controller_notices extends Controller_basepage {
		
		/**
		 * 商品列表视图
		 * @param unknown $inPath
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageList($inPath){
			
			$questArr = SUtil::html_arr($this->request);
			
			//公告model
			$notice_db = new Model_sys_notice();
			$notice_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$page = $this->getPageNumber($inPath);//获取分页参数
			
			//搜索
			if ( $questArr['seek'] == 1 ){
			 	unset($questArr['page']);//卸载分页
				unset($questArr['seek']);
				
				$questArr['dateline'] = $questArr['dateline'];
				
				$questArr = array_filter($questArr);
					
				$notice_results = $notice_db->getListAll($page,$questArr);
				$count = $notice_results['count'];//获取数据总数
				//	die(SUtil::P($ggmanage_results));
				
				//$dataline = $questArr['dateline'] ? date('Y-m-d H:i:s', $questArr['dateline']) : '';
				
				//查询字符显示
				$this->ass('seek', $questArr);
					
			}else{
					
				$notice_results = $notice_db->getListAll($page);
				$count = $notice_results['count'];//获取数据总数
					
			}
			
			//数据重组
			foreach ( $notice_results['list'] as &$v ){
				
				$v['dateline'] = date('Y-m-d H:i:s', $v['dateline']);
				
				$v['T'] = mt_rand(10000000, 99999999);
				$data = $v['T'].$v['sn_id'];
				$v['K'] = SUtil::create_token($data);
				
			}
			
			//die(SUtil::P($notice_results));
			
			$pageShow = $this->pageBar($count, $page, $inPath);//获取分页字符串
			
			//======================= Token授权 POST Start =======================//
				$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
				$TOKEN['POST_T2'] = time();
				$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
				$TOKEN['POST_K'] = SUtil::create_token($data);
			//======================= Token授权 POST End =======================//
			
			$tplArr = array(
 					
 				'list'=>$notice_results['list'],
 				'pagehtml'=>$pageShow,//分页字符
				'TOKEN'=>$TOKEN,
 				
 					
 			);
			
			return $this->template($tplArr);	
			
		}
		
		
		/**
		 * 通告编辑（添加）视图
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageEdit(){
			
			$questArr = SUtil::html_arr($this->request);
			
			
			if ( $this->request['ac'] == 'edit' ){
				
				$questArr['k'] != SUtil::create_token($questArr['t'].$questArr['sn_id']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
				
				//公告model
				$notice_db = new Model_sys_notice();
				$notice_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
				$notice_reuslt = $notice_db->getEdit($questArr['sn_id']);
				
	//	    $notice_reuslt['content'] = htmlspecialchars_decode(htmlspecialchars_decode(stripcslashes($notice_reuslt['content']),ENT_QUOTES));
				
		
			    
				$this->ass('list', $notice_reuslt);
				$this->ass('ac', 'edit');
			//	die(SUtil::P($notice_reuslt));
				
			}
			
			//======================= Token授权 POST Start =======================//
				$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
				$TOKEN['POST_T2'] = time();
				$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
				$TOKEN['POST_K'] = SUtil::create_token($data);
			//======================= Token授权 POST End =======================//
			
			$tplArr = array(

				'TOKEN'=>$TOKEN,
			);
			//die(SUtil::P($tplArr));
			return $this->template($tplArr);		
			
		}
		
		
		/**
		 * 编辑（添加）通告处理方法
		 */
		public function pagedoEdit(){
			
			$questArr = SUtil::html_arr($this->request);
								
			$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			
			//标题
			$questArr['title'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'公告标题不能为空'))) : SUtil::error('公告标题不能为空') );
			
			//内容
			$questArr['content'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'公告内容不能为空'))) : SUtil::error('公告内容不能为空') );
			
		
			//发布人ID
			$questArr['sa_id'] = 1;
			
			//发布人
			$questArr['author'] = '系统管理员';
			
			//公告model
			$notice_db = new Model_sys_notice();
			$notice_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			unset($questArr['POST_T']);
			unset($questArr['POST_T2']);
			unset($questArr['POST_K']);
			unset($questArr['ac']);
			
			if ( $this->request['ac'] != 'edit' ){//添加
				unset($questArr['sn_id']);
				$questArr['dateline'] = time();
		//		$questArr['content']=htmlspecialchars_decode($questArr['content']);
				
				
			
				$notice_db->add($questArr) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'公告发布成功'))) : SUtil::success('公告发布成功') ) :  ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'公告发布失败'))) : SUtil::error('公告发布失败') );;
				
			}else{//编辑
				
				$notice_db->doEdit($questArr['sn_id'], $questArr) !== false ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'公告修改成功'))) : SUtil::success('公告修改成功') )  :  ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'公告修改失败'))) : SUtil::error('公告修改失败') );
				
			}
			
		//	die(SUtil::P($this->request));
			
		}
		
		/**
		 * 删除处理方法
		 */
		public function pagedel(){
			
			$questArr = SUtil::html_arr($this->request);
			//Token令牌校验
			if ( !is_array($this->request['check']) ){
				
				$questArr['k'] != SUtil::create_token($questArr['t'].$questArr['sn_id']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
		
			}else{
		
				$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
		
			}
				
			$questArr = SUtil::html_arr($this->request);
			//die(SUtil::P($questArr));
				
			//公告model
			$notice_db = new Model_sys_notice();
			$notice_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
			//删除
			$notice_db->del($questArr['check']) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'删除成功'))) : SUtil::success('删除成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'删除失败'))) : SUtil::error('删除失败') );
		
				
				
		}
		
		
	}

?>