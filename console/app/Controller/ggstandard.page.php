<?php 

	/**
	 * 规格管理控制器
	 */

	header('Content-type:text/html;charset=utf-8');
 	Class Controller_ggstandard extends Controller_basepage {
 		
 		/**
 		 * 规格列表视图
 		 * @return Ambigous <mixed, string, void, string>
 		 */
 		public function pageList($inPath){
 			
 			$page = $this->getPageNumber($inPath);//获取当前分页

 			$ggstandard_db = new Model_ggstandard_ggstandard();
 			$ggstandard_db->setCache(SUtil::C('sqlCache'));//设置缓存
 			
 			//未搜索
 			if ( empty($this->request['ggs_id']) && empty($this->request['ggs_type']) && empty($this->request['ggs_name']) ){
 				
 				$count = $ggstandard_db->getCount("is_del!=1");//获取总数据
 				
 				$ggstandard_results = $ggstandard_db->findByis_del(1, '!=', array('*'), $page);
 				$pageShow = $this->pageBar($count, $page, $inPath);//获取分页字符串
 				//die(SUtil::P($ggstandard_results));
 				
 				//========================= Token授权 Start =======================//
	 				foreach ( $ggstandard_results['list'] as $k=>$v ){
	 				
	 					$ggstandard_results['list'][$k]['t'] = mt_rand(10000000, 99999999);
	 					$data = $v['ggs_id'].$ggstandard_results['list'][$k]['t'];
	 					$ggstandard_results['list'][$k]['k'] = SUtil::create_token($data);
	 				
	 				}
 				//========================= Token授权 End =======================//
 				
 					
 				//======================= Token授权 POST Start =======================//
	 				$POST_T = mt_rand(10000000, 99999999);
	 				$POST_T2 = time();
	 				$data = $POST_T.$POST_T2;
	 				$POST_K = SUtil::create_token($data);
 				//======================= Token授权 POST End =======================//
 				
 			}else{//搜索
 				
 				$questArr = SUtil::html_arr($this->request);
 				$questArr = array_filter($questArr);
 				unset($questArr['page']);
 				
 				$questArr['is_del'][0] = '!=';
 				$questArr['is_del'][1] = 1;
 				
 				$count = $ggstandard_db->getSeekCount($questArr);//获取总数据
 			//	die(var_dump($count));
 			//die(SUtil::P($questArr));
 				$ggstandard_results = $ggstandard_db->getSeek($questArr, $page);
 				$pageShow = $this->pageBar($count, $page, $inPath);//获取分页字符串
 			//	die(SUtil::P($ggstandard_results));
 				
 			}
 			
 			
 			//die(SUtil::P($ggstandard_results));
 			$tplArr = array(
 					
 					'list'=>$ggstandard_results['list'],
 					'pagehtml'=>$pageShow,
 					'POST_T'=>$POST_T,
 					'POST_T2'=>$POST_T2,
 					'POST_K'=>$POST_K
 					
 			);
 			
 			$this->preparRow($questArr);
 			$this->ass('row', $questArr);
 			return $this->template($tplArr);
 			
 		}
 		/**
 		 * 默认选中设置
 		 */
 		private function preparRow($row)
 		{
 			if($row)
 			{
 				//选中设置
 				$ggs_type_checked=array($row['ggs_type']=>"selected='selected'");
 		
 				$this->ass('ggs_type_checked',$ggs_type_checked);
 		
 				$this->ass('row',$row);
 		
 			}
 		}
 		
 		
 		/**
 		 * 规格管理编辑(添加)视图
 		 */
 		public function pageEdit(){
 			
 			isset($this->request['ac']) ? $this->ass('ac', $this->request['ac']) : $this->ass('ac', 'add');//注册类型 默认为添加
 			$questArr = SUtil::html_arr($this->request);
 			//die(SUtil::P($questArr));
 			if ( @$questArr['ac'] == 'edit' ){//编辑视图
 				
 				$ggstandard_db = new Model_ggstandard_ggstandard();
 				$ggstandard_db->setCache(SUtil::C('sqlCache'));//设置缓存
 				$ggstandard_result = $ggstandard_db->getOneById($questArr['id']);
 				//die(SUtil::P($ggstandard_result));
 				$this->ass('result', $ggstandard_result);
 				$this->ass('typeHidden', "<input type='hidden' name='ggs_id' value='".$questArr['id']."'>");
 				
 			}
 			
 			return $this->template();
 			
 		}
 		
 		/**
 		 * 规格管理编辑(添加处理方法)
 		 */
 		public function pagedoStandard(){
 			
 			SUtil::isPost() || SUtil::error('非法操作');
 			$questArr = SUtil::html_arr($this->request);//过滤参数
 			$ggstandard_db = new Model_ggstandard_ggstandard();
 			$ggstandard_db->setCache(SUtil::C('sqlCache'));//设置缓存
 			
 		//	die(SUtil::P(var_dump($questArr)));
 			
 			if ( mb_strlen($questArr['ggs_name']) > 30 || $questArr['ggs_name'] == NULL ){
 			
 				SUtil::error('规格名不能为空且超过30个字符');
 			
 			}
 			
 			SUtil::Is_number($questArr['ggs_type'], 1, 2) == NULL && SUtil::error('规格类型格式错误');
 			
 			if ( isset($questArr['height']) ){//图片类型

 				SUtil::Is_number($questArr['height']) == NULL && SUtil::error('规格高度只能为整数');
 				SUtil::Is_number($questArr['width']) == NULL && SUtil::error('规格宽度只能为整数');
 				
 			}else{//文字型
 				
 				SUtil::Is_number($questArr['length']) == NULL && SUtil::error('规格长度只能为整数');
 				
 			}
 			
 			
 			$name = $ggstandard_db->getName($questArr['ggs_name']);
 			
 			
 			
 			//添加数据
 			if ( $questArr['ac'] == 'add' ){
 				
 				unset($questArr['ac']);
				$questArr['dateline'] = time();
				
				$name != NULL && SUtil::error('规格名称已存在');
 				
 				if ( $ggstandard_db->insert($questArr) > 0 ){
 				
 					SUtil::success('添加规格成功');
 				
 				}else{
 				
 					SUtil::error('添加规格失败');
 				
 				}
 				
 			}else{//编辑
 				
 				unset($questArr['ac']);
 			//	die(SUtil::P($questArr['ggs_id']));
 				
 				$ggs_name = $ggstandard_db->getName($questArr['ggs_id']);
 				//die(SUtil::P(var_dump($name)));
 				if ( $ggs_name['ggs_name'] != $name['ggs_name'] && $name != NULL ){
 					
 					SUtil::error('规格名称已存在');
 				}
 				
 				//规格类型重置
 				if ( isset($questArr['height']) ){
					
 					$questArr['length'] = 0;
 					
 				}
 				
 				//规格类型重置
 				if ( !isset($questArr['height']) ){
 				//	die(SUtil::P($questArr));
 					$questArr['height'] = 0;
 					$questArr['width'] = 0;
 					
 				}
 			//	die(SUtil::P($questArr));
 				if ( $ggstandard_db->update("ggs_id=".$questArr['ggs_id'], $questArr) !== false ){
 					
 					$ggaterials_db = new Model_ggmaterials_ggmaterials();//素材model
 					$ggaterials_db->setCache(SUtil::C('sqlCache'));//设置缓存
 					
 					$Sql = array('__height'=>$questArr['height'],'__width'=>$questArr['width'],'__length'=>$questArr['length'],'ggm_type'=>$questArr['ggs_type']);
 					$questArr['ggs_type'] == 1 && $Sql['src'] = '';
 					
 					$ggaterials_db->update("ggs_id=".$questArr['ggs_id'], $Sql);
 					
 					SUtil::success('修改规格成功');
 						
 				}else{
 						
 					SUtil::error('修改规格失败');
 						
 				}
 				
 			}
 			
 			die(SUtil::P($this->request));
 			
 		}
 		
 		
 		/**
 		 * 同步单选（多选）删除处理方法
 		 */
 		public function pageDelete(){
 			
 			$ggstandard_db = new Model_ggstandard_ggstandard();//规格model
 			$ggstandard_db->setCache(SUtil::C('sqlCache'));//设置缓存
 			
 			$ggaterials_db = new Model_ggmaterials_ggmaterials();//素材model
 			$ggaterials_db->setCache(SUtil::C('sqlCache'));//设置缓存
 			
 			if ( SUtil::isGet() ){//单选删除
 				
 				$this->request['k'] != SUtil::create_token($this->request['id'].$this->request['t']) && SUtil::error('操作失效');//Token令牌校验
 				//die('del');
 				$ggaterials_db->getOne("ggs_id=".$this->request['id']." and __use_count>0") > NULL && SUtil::error('该规格下有正在使用的素材，您不能删除！');
 				( $ggstandard_db->update('ggs_id='.$this->request['id'], array('is_del'=>1)) > 0  && $ggaterials_db->update("ggs_id=".$this->request['id'], array('is_del'=>1)) !== false ) ? SUtil::success('删除成功') : SUtil::error('删除失败');
 				
 			}elseif ( SUtil::isPost() ){//多选
 				
 				$qusetArr = SUtil::html_arr($this->request);//过滤接收参数
 				$qusetArr['POST_K'] != SUtil::create_token($qusetArr['POST_T'].$qusetArr['POST_T2']) && SUtil::error('操作失效');//Token令牌校验
 				//die(SUtil::P($qusetArr));
 				$delStr = implode(',', $qusetArr['check']);
 				$ggaterials_results = $ggaterials_db->getList("ggs_id in (".$delStr.") and __use_count>0", 'ggs_id');//查找不允许删除的数据
 				
 				foreach ( $ggaterials_results['list'] as $k=>$v ){
 					
 					foreach ( $qusetArr['check'] as $k2=>$v2 ){
 						
 						if ( $v['ggs_id'] == $v2 ){

 							unset($qusetArr['check'][$k2]);
 						}
 						
 					}
 					
 				}
 				$delStr = implode(',', $qusetArr['check']);
 			//	die(SUtil::P($qusetArr['check']).SUtil::P($ggaterials_results['list']).SUtil::P($qusetArr['check']));
 			//	die(SUtil::P($delStr));
			/* 	die($delStr);
 				die(var_dump($ggaterials_db->update("ggs_id in (".$delStr.")", array('is_del'=>1)))); */
 				( $ggstandard_db->update("ggs_id in (".$delStr.")", array('is_del'=>1)) && $ggaterials_db->update("ggs_id in (".$delStr.")", array('is_del'=>1)) !== false ) > 0 ? ($ggaterials_results['list'] != NULL ? SUtil::success('删除成功,已为您过滤掉无法删除数据') : SUtil::success('删除成功')) : SUtil::error('删除失败');
 				
 			}
 			
 			
 		}
 		
 		/**
 		 * 异步校验规格名处理方法
 		 */
 		public function pageAjaxCheckName(){
 			
 			SUtil::isAjax() || die('Error');
 			$name = htmlspecialchars($this->request['name'],ENT_QUOTES);
 			$ggstandard_db = new Model_ggstandard_ggstandard();
 			$ggstandard_db->setCache(SUtil::C('sqlCache'));//设置缓存
 			
 			if ( $ggstandard_db->findByggs_name($name) != NULL ){//校验规格名称
 				
 				$msg_data['status'] = 0;
 				$msg_data['info'] = '规格名称已存在';
 				
 			}else{
 				
 				if ( mb_strlen($name, 'utf8') <= 30 && $name != NULL ){
 					
 					$msg_data['status'] = 1;
 					$msg_data['info'] = '规格名称可用';
 					
 				}else{
 					
 					$msg_data['status'] = 0;
 					$msg_data['info'] = '规格名称不能为空且长度不能大于30';
 					
 				}
 				
 			}
 			
 			die(json_encode($msg_data));
 			
 		}
 		
 	}


?>