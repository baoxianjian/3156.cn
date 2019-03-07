<?php 
	
	/**
	 * 产品控制器
	 * @author Administrator
	 *
	 */
	class Controller_pdttypes extends Controller_basepage {
		
		/**
		 * 产品分类
		 * @param unknown $inPath
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageList($inPath){
			
			//产品分类model
			$pdtTypes_db = new Model_pdt_pdtTypes();
			$pdtTypes_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$page = $this->getPageNumber($inPath);//获取分页参数
			
			$questArr = SUtil::html_arr($this->request);
			
			//搜索
			if ( $questArr['seek'] == 1 ){
				unset($questArr['page']);//卸载分页
				unset($questArr['seek']);
				$questArr = array_filter($questArr);
					
				$pdt_results = $pdtTypes_db->getGroupList($page,$questArr['pt_name']);
				//	die(SUtil::P($ggmanage_results));
					
			}else{
					
				$pdt_results = $pdtTypes_db->getGroupList($page);
					
			}
			
			
			$count = $pdt_results['count'];
			$pageShow = $this->pageBar($count, $page, $inPath);//获取分页字符串
				
			foreach ( $pdt_results['list'] as &$v ){
					
				//======================= Token授权 POST Start =======================//
					$v['T'] = mt_rand(10000000, 99999999);
					$data = $v['T'].$v['pt_id'];
					$v['K'] = SUtil::create_token($data);
				//======================= Token授权 POST End =======================//
			}
			
			
			//die(SUtil::P($ggmanage_results));
			
			$tplArr = array(
			
					'list'=>$pdt_results['list'],
					'pagehtml'=>$pageShow,//分页字符
			
			);
			
			return $this->template($tplArr);
			
		}
		
		
		/**
		 * 产品分类编辑（添加）视图
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageEdit(){
			
			
			$questArr = SUtil::html_arr($this->request);
			
			//产品分类model
			$pdtTypes_db = new Model_pdt_pdtTypes();
			$pdtTypes_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$questArr = SUtil::html_arr($this->request);//过滤接收参数
			$this->ass('typeName','添加一级子分类');
			if ( $questArr['ac'] == 'edit' ){
				
				$questArr['K'] != SUtil::create_token($questArr['T'].$questArr['pt_id']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
				
				$pdt_result = $pdtTypes_db->getOneById($questArr['pt_id']);
				$pdt_result['parent_id'] != 0 && $pdt_parent = $pdtTypes_db->getParent($pdt_result['parent_id']);
				//die(SUtil::P($ggmanage_parent));
				$pdt_parent == NULL && $pdt_parent = array('pt_id'=>'已是顶级分组','pt_name'=>'已是顶级分组');
				
				$this->ass('parent', $pdt_parent);
				$this->ass('list', $pdt_result);
				$this->ass('ac', 'edit');
				$this->ass('pt_id', $pdt_result['pt_id']);
				$this->ass('typeName','编辑子分类');
				
				
			}elseif ($questArr['ac'] == 'add_children' ){
				
				$pdt_result = $pdtTypes_db->getOneById($questArr['pt_id']);
				$this->ass('parent', $pdt_result);
				$this->ass('typeName','添加子分类');
				
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
		
		
		/**
		 * 编辑产品分类处理方法
		 */
		public function pagedoEdit(){
			
			$questArr = SUtil::html_arr($this->request);
		//	die(SUtil::P($questArr));
			//Token令牌校验
			$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			//die(SUtil::P($questArr));
			
			//产品分类model
			$pdtTypes_db = new Model_pdt_pdtTypes();
			$pdtTypes_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$questArr['ac'] == 'edit' && ( SUtil::Is_number($questArr['pt_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') ) );
			$questArr['ac'] == 'edit' && $pdt_name = $pdtTypes_db->getName($questArr['pt_id']);
			//die(var_dump($pdtTypes_db->exist_name($questArr['pt_name'])));
			
			if ( $pdtTypes_db->exist_name($questArr['pt_name']) != NULL && $questArr['pt_name'] != $pdt_name['pt_name'] ){
					
				SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'分类名称已存在'))) : SUtil::error('分类名称已存在');
					
			}
			
			( $questArr['pt_name'] == NULL || mb_strlen($questArr['pt_name']) > 30 ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'分类名称不能为空且长度不能大于30'))) : SUtil::error('分类名称不能为空且长度不能大于30') );
			
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
						
					$pdtTypes_db->updataById($questArr['pt_id'], $questArr) !== false ? SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'编辑分类成功'))) : SUtil::success('编辑分类成功') : SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'编辑分类失败'))) : SUtil::error('编辑分类失败');
						
				}else{//添加
					
					$questArr['dateline'] = time();
					$pdtTypes_db->insert($questArr) > 0 ? SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'添加分类功'))) : SUtil::success('添加分类成功') : SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'添加分类失败'))) : SUtil::error('添加分类失败');
						
				}
			
		}
		
		
		/**
		 * 异步联动事件处理
		 */
		public function pageajaxGroup(){
			SUtil::isAjax() || die('error');
			$questArr = SUtil::html_arr($this->request);
			
			//产品分类model
			$pdtTypes_db = new Model_pdt_pdtTypes();
			$pdtTypes_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$pdt_results = $pdtTypes_db->getGroup($questArr['groupId']);
		
			foreach ( $pdt_results['list'] as &$v ){
		
				//======================= Token授权 POST Start =======================//
					$v['T'] = mt_rand(10000000, 99999999);
					$data = $v['T'].$v['pt_id'];
					$v['K'] = SUtil::create_token($data);
				//======================= Token授权 POST End =======================//
			}
		
			//die(SUtil::P($ggmanage_results['list']));
			die(json_encode($pdt_results['list']));
		}
		
		/**
		 * 异步校验分组名称处理事件
		 */
		public function pageajaxGname(){
			
			SUtil::isAjax() || die('error');
			$questArr = SUtil::html_arr($this->request);
			
			//产品分类model
			$pdtTypes_db = new Model_pdt_pdtTypes();
			$pdtTypes_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			if ( $pdtTypes_db->getOne($questArr) != NULL ){
				
				$msg_data['status'] = 0;
				$msg_data['info'] = '分组名称已存在';
				
			}elseif($questArr['pt_name'] == NULL ) {
				
				$msg_data['status'] = 0;
				$msg_data['info'] = '分组名不能为空';
				
			}else{
				
				$msg_data['status'] = 1;
				$msg_data['info'] = '分组名称可使用';
			}
		
			die(json_encode($msg_data));
		}
		
	}

?>