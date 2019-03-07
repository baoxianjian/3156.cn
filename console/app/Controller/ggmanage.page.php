<?php
	
	/**
	 * 分组管理控制器
	 * @author zhangqijun
	 *
	 */
	header('Content-type:text/html;charset=utf-8');
 	Class Controller_ggmanage extends Controller_basepage {
 		
 		/**
 		 * 分组管理首页视图
 		 * @return Ambigous <mixed, string, void, string>
 		 */
 		public function pageList($inPath){
 			
 			$ggmanage_db = new Model_ggmanage_ggmanage();
 			
 			$page = $this->getPageNumber($inPath);//获取分页参数
 			
 			$questArr = SUtil::html_arr($this->request);
 			
 			//搜索
 			if ( $questArr['seek'] == 1 ){
 				unset($questArr['page']);//卸载分页
 				unset($questArr['seek']);
 				$questArr = array_filter($questArr);
 				
 				$count = $ggmanage_db->count($questArr['title']);//获取数据总数
 				$ggmanage_results = $ggmanage_db->getGroupList($page,$questArr['title']);
 				
 				$this->ass('seek',  $questArr['title']);
 			//	die(SUtil::P($ggmanage_results));
 				
 			}else{
 				
 				$count = $ggmanage_db->count();//获取数据总数
 				$ggmanage_results = $ggmanage_db->getGroupList($page);
 				
 			}
 			
 			
 			
 			$pageShow = $this->pageBar($count, $page, $inPath);//获取分页字符串
			
 			foreach ( $ggmanage_results['list'] as $k=>$v ){
 				
 				//======================= Token授权 POST Start =======================//
	 				$ggmanage_results['list'][$k]['T'] = mt_rand(10000000, 99999999);
	 				$data = $ggmanage_results['list'][$k]['T'].$v['ggpg_id'];
	 				$ggmanage_results['list'][$k]['K'] = SUtil::create_token($data);
 				//======================= Token授权 POST End =======================//
 			}
 			
 			
 			//die(SUtil::P($ggmanage_results));
 			
 			$tplArr = array(
 					
 					'list'=>$ggmanage_results['list'],
 					'pagehtml'=>$pageShow,//分页字符
 					
 			);
            
 			return $this->template($tplArr);
 		}
 		
 		/**
 		 * 添加（编辑）分组视图
 		 * @param unknown $inPath
 		 * @return Ambigous <mixed, string, void, string>
 		 */
 		public function pageaddgroup($inPath){
 		//	die(SUtil::P($this->request));
 			$ggmanage_db = new Model_ggmanage_ggmanage();
 			$questArr = SUtil::html_arr($this->request);//过滤接收参数
 			
 			if ( $questArr['ac'] == 'edit' ){
 				
 				$questArr['K'] != SUtil::create_token($questArr['T'].$questArr['ggpg_id']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
 				
 				$ggmanage_result = $ggmanage_db->getOneById($questArr['ggpg_id']);
 				$ggmanage_result['parent_id'] != 0 && $ggmanage_parent = $ggmanage_db->getParent($ggmanage_result['parent_id']);
 				//die(SUtil::P($ggmanage_result));
				$ggmanage_parent == NULL && $ggmanage_parent = array('ggpg_id'=>'已是顶级分组','title'=>'已是顶级分组');
				
 				$this->ass('parent', $ggmanage_parent);
 				$this->ass('list', $ggmanage_result);
 				$this->ass('ac', 'edit');
 				$this->ass('ggpg_id', $ggmanage_result['ggpg_id']);
				
 				
 			}elseif ( $questArr['ac'] == 'add_children' ){
 				
 				$ggmanage_result = $ggmanage_db->getOneById($questArr['ggpg_id']);
 				$this->ass('parent', $ggmanage_result);
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
 			
 			return $this->template($tplArr);
 		}
 		
 		public function pagedoaddgroup(){
 			
 			$questArr = SUtil::html_arr($this->request);
 			
 			//Token令牌校验
 			$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
 			//die(SUtil::P($questArr));
 			$ggmanage_db = new Model_ggmanage_ggmanage();
 			$questArr['ac'] == 'edit' && ( SUtil::Is_number($questArr['ggpg_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') ) );
 			$questArr['ac'] == 'edit' && $ggpg_title = $ggmanage_db->getName($questArr['ggpg_id']);
 			//die(var_dump($ggpg_title));
 			
 			if ( $ggmanage_db->exist_name($questArr['title']) != NULL && $questArr['title'] != $ggpg_title['title'] ){
 				
 				SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'分组名称已存在'))) : SUtil::error('分组名称已存在');
 				
 			}
 			
 			( $questArr['title'] == NULL || mb_strlen($questArr['title']) > 30 ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'分组名称不能为空且长度不能大于30'))) : SUtil::error('分组名称不能为空且长度不能大于30') );
 			SUtil::Is_number($questArr['order']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'排序只能为正整数'))) : SUtil::error('排序只能为正整数') );
 			
 			( SUtil::Is_number($questArr['parent_id']) == NULL ) && $questArr['parent_id'] = 0;
			
 			//卸载无用变量
 			unset($questArr['POST_K']);
 			unset($questArr['POST_T']);
 			unset($questArr['POST_T2']);
 			unset($questArr['ac']);
 			$questArr = array_filter($questArr,function($v){
 				
 				if ( $v === NULL || $v === '' ){
 					return false;
 				}
 				return true;
 			});
 			
 			//die(SUtil::P($questArr));
 			//编辑
 			if ( $this->request['ac'] == 'edit' ){
 				
 				$ggmanage_db->updataById($questArr['ggpg_id'], $questArr) !== false ? SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'编辑分组成功'))) : SUtil::success('编辑分组成功') : SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'编辑分组失败'))) : SUtil::error('编辑分组失败');
 				
 			}else{//添加
 				
 				$questArr['dateline'] = time();
 				$ggmanage_db->insert($questArr) > 0 ? SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'添加分组成功'))) : SUtil::success('添加分组成功') : SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'添加分组失败'))) : SUtil::error('添加分组失败');
 				
 			}
 			
 			
 		}
 		
 		
 		/**
 		 * 异步联动事件处理
 		 */
 		public function pageajaxGroup(){
 			SUtil::isAjax() || die('error');
 			$questArr = SUtil::html_arr($this->request);
 			$ggmanage_db = new Model_ggmanage_ggmanage();
 			$ggmanage_results = $ggmanage_db->getGroup($questArr['groupId']);
 		//	die(SUtil::P($ggmanage_results));
 			foreach ( $ggmanage_results['list'] as $k=>$v ){
 					
 				//======================= Token授权 POST Start =======================//
 				$ggmanage_results['list'][$k]['T'] = mt_rand(10000000, 99999999);
 				$data = $ggmanage_results['list'][$k]['T'].$v['ggpg_id'];
 				$ggmanage_results['list'][$k]['K'] = SUtil::create_token($data);
 				//======================= Token授权 POST End =======================//
 			}
 			
 			//die(SUtil::P($ggmanage_results['list']));
 			die(json_encode($ggmanage_results['list']));
 		}
 		
 		/**
 		 * 异步校验分组名称处理事件
 		 */
 		public function pageajaxGname(){
 			SUtil::isAjax() || die('error');
 			$questArr = SUtil::html_arr($this->request);
 			$ggmanage_db = new Model_ggmanage_ggmanage();
 			if ( $ggmanage_db->getOne($questArr) != NULL ){
 				$msg_data['status'] = 0;
 				$msg_data['info'] = '分组名称已存在';
 			}elseif($questArr['title'] == NULL ) {
 				$msg_data['status'] = 0;
 				$msg_data['info'] = '分组名不能为空';
 			}else{
 				$msg_data['status'] = 1;
 				$msg_data['info'] = '分组名称可使用';
 			}
 			
 			die(json_encode($msg_data));
 		}
 		
 	}