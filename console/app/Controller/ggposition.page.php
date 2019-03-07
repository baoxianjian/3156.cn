<?php
	header('Content-type:text/html;charset=utf-8');
	
	/**
	 * 广告位管理控制器
	 * @author Administrator
	 *
	 */
	Class Controller_ggposition extends Controller_basepage {
		
		/**
		 * 广告位列表页视图
		 * @param unknown $inPath
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageList($inPath){
			  
			
			//广告位model
			$ggposition_db = new Model_ggposition_ggposition();
			$ggposition_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//分组model
			$ggmanage_db = new Model_ggmanage_ggmanage();
			$ggmanage_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
		//	die(SUtil::P($inPath));
			
			$questArr = $this->request;
			
			$page = $this->getPageNumber($inPath);//获取分页参数
			
			if ( $questArr['seek'] == 1 ){
				
				$questArr = array_filter($questArr);
				unset($questArr['seek']);
				unset($questArr['page']);
			//	die(SUtil::P($questArr));
				//组合查询字符串
				foreach ( $questArr as $k=>$v ){
					
					$sqlStr .= str_replace('|', '.', $k).'='.$v.' and ';
					
				}
				//die($sqlStr);
				$count = $ggposition_db->count($sqlStr);
				$ggposition_results = $ggposition_db->getListAll($page,$sqlStr);
				
			}else{
				
				$count = $ggposition_db->count();
				$ggposition_results = $ggposition_db->getListAll($page);
				
			}
			
			//点击量排序
			if ( $questArr['count_order'] == 'ASC' || $questArr['count_order'] == 'DESC' ){
				
				$count = $ggposition_db->count($sqlStr);
				$ggposition_results = $ggposition_db->getListAll($page,$sqlStr,$questArr['count_order']);
				
				
			}

			$pageShow = $this->pageBar($count, $page, $inPath,20);//获取分页字符串
			//die($pageShow);
			 foreach ( $ggposition_results['list'] as $k=>$v ){
			 	
			 	//获取分组
			 	$group = $ggmanage_db->getParent($v['ggpg_id']);
			 	$v['ggpg_id2'] != NULL && $group2 = $ggmanage_db->getParent($v['ggpg_id2']);
			 	$ggposition_results['list'][$k]['group'] = $group['title'].'-'.$v['ggpg_id'];
			 	$v['ggpg_id2'] != -1 && $ggposition_results['list'][$k]['group'] = $ggposition_results['list'][$k]['group'].'/'.$group2['title'].'-'.$v['ggpg_id2'];;
			 	
			 	
			 	//数据替换
			 	$ggposition_results['list'][$k]['add_red'] = $v['add_red'] == 1 ? '套红' : '未套红';
			 	$ggposition_results['list'][$k]['gg_sale_state'] = $v['gg_sale_state'] == 1 ? '付费' :( $v['gg_sale_state'] == 2 ? '配送' : '免费' );
			 	$ggposition_results['list'][$k]['ggt_type'] = $v['ggt_type'] == 1 ? '文字' : '图片';
			 	
			 	//======================= Token授权 POST Start =======================//
				 	$ggposition_results['list'][$k]['T'] = mt_rand(10000000, 99999999);
				 	$data = $v['ggp_id'].$ggposition_results['list'][$k]['T'];
				 	$ggposition_results['list'][$k]['K'] = SUtil::create_token($data);
			 	//======================= Token授权 POST End =======================//
				
			
			 }
			
			 
			 //获取一级分组
			 $parentArr = $ggmanage_db->getGroupList();
			 
			 //======================= Token授权 POST Start =======================//
				 $TOKEN['POST_T'] = mt_rand(10000000, 99999999);
				 $TOKEN['POST_T2'] = time();
				 $data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
				 $TOKEN['POST_K'] = SUtil::create_token($data);
			 //======================= Token授权 POST End =======================//
			 
			//die(SUtil::P($ggposition_results));
			$tplArr = array(
			
					'list'=>$ggposition_results['list'],
					'pagehtml'=>$pageShow,//分页字符
					'TOKEN'=>$TOKEN,
					'parentArr'=>$parentArr['list']
			
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
				$ggt_type_checked=array($row['ggt_type']=>"selected='selected'");
				$ggt_type_checked=array($row['ggt_type']=>"selected='selected'");
				$ggt_type_checked=array($row['ggt_type']=>"selected='selected'");
		
				$this->ass('ggt_type_checked',$ggt_type_checked);
				$this->ass('ggt_type_checked',$ggt_type_checked);
				$this->ass('ggt_type_checked',$ggt_type_checked);
		
				$this->ass('row',$row);
		
			}
		}
		/**
		 * 广告位编辑（添加）视图
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageEdit(){
			
			//广告位model
			$ggposition_db = new Model_ggposition_ggposition();
			$ggposition_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//分组model
			$ggmanage_db = new Model_ggmanage_ggmanage();
			$ggmanage_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//模板model
			$ggtemplate_db = new Model_ggtemplate_ggtemplate();
			$ggtemplate_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$questArr = SUtil::html_arr($this->request);
			//编辑
			if ( $this->request['ac'] == 'edit' ){
				
				//Token令牌校验
				$this->request['K'] != SUtil::create_token($this->request['ggp_id'].$this->request['T']) && SUtil::error('操作失效');
				
				//取得编辑数据
				$ggposition_result = $ggposition_db->getOneById($questArr['ggp_id']);
				
				//获取分组
				$ggposition_result['group'] = $ggmanage_db->getParent($ggposition_result['ggpg_id']);
				$ggposition_result['ggpg_id2'] != NULL && $ggposition_result['group2'] = $ggmanage_db->getParent($ggposition_result['ggpg_id2']);
				
				//获取模板
				$ggposition_result['tpl'] = $ggtemplate_db->findByggt_id($ggposition_result['gg_tpl_id'],'=','name,ggt_id,ggt_type');
				
				//die(SUtil::P($ggposition_result));
				$this->ass('list', $ggposition_result);
				$this->ass('ac', 'edit');
				
			}
			
			//获取顶级分组
			$ggmanage_results = $ggmanage_db->getGroupList();
			//die(SUtil::P($ggmanage_results));
			
			//获取模板
			$ggtemplate_results = $ggtemplate_db->getListAll();
			//die(SUtil::P($ggtemplate_results));
			
			//======================= Token授权 POST Start =======================//
				$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
				$TOKEN['POST_T2'] = time();
				$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
				$TOKEN['POST_K'] = SUtil::create_token($data);
			//======================= Token授权 POST End =======================//
			$tplArr = array(
					
					'TOKEN'=>$TOKEN,
					'ggmanage_list'=>$ggmanage_results['list'],
					'ggtemplate_list'=>$ggtemplate_results['list']
			);
			return $this->template($tplArr);
			
		}
		
		
		/**
		 * 添加编辑广告位处理方法
		 */
		public function pagedoEdit(){
			
			$questArr = SUtil::html_arr($this->request);
			$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			
			//广告位model
			$ggposition_db = new Model_ggposition_ggposition();//广告位model
			$ggposition_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$questArr['ac'] == 'edit' && ( SUtil::Is_number($questArr['ggp_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') ) );
			$questArr['ac'] == 'edit' && ( $ggposition_name = $ggposition_db->getName($questArr['ggp_id']) );
			$name = $ggposition_db->exist_Name($questArr['title']);
			
			//分组校验
			SUtil::Is_number($questArr['ggpg_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择分组'))) : SUtil::error('请选择分组') );
			
			//分组名称格式校验
			( $questArr['title'] == NULL || mb_strlen($questArr['title'],'utf8') > 30 ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'广告位名称长度不能大于30且不能为空'))) : SUtil::error('广告位名称长度不能大于30且不能为空') );
			
			//分组名称重复校验
			if ( $questArr['ac'] == 'edit' ){
				
				( $name != NULL && $name['title'] != $ggposition_name['title'] ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'该广告位名称已存在'))) : SUtil::error('该广告位名称已存在') );
				
			}else{
				
				$name != NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'该广告位名称已存在'))) : SUtil::error('该广告位名称已存在') );
				
			}
		//	die(SUtil::P($questArr));
			//模板校验
			SUtil::Is_number($questArr['gg_tpl_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择模板'))) : SUtil::error('请选择模板') );
			
			//套红校验
			SUtil::Is_number($questArr['add_red']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择是否套红'))) : SUtil::error('请选择是否套红') );
			
			//销售类型校验
			SUtil::Is_number($questArr['gg_sale_state']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择是销售类型'))) : SUtil::error('请选择是销售类型') );
			
			//价格档次格式校验
			SUtil::Is_number($questArr['price_level']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'价格档次格式错误'))) : SUtil::error('价格档次格式错误') );
			
			//排序格式校验
			SUtil::Is_number($questArr['order']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'排序格式错误'))) : SUtil::error('排序格式错误') );
			
			$questArr = array_filter($questArr);
			
			//卸载失效变量
			unset($questArr['POST_T']);
			unset($questArr['POST_T2']);
			unset($questArr['POST_K']);
			
			//编辑
			if ( $this->request['ac'] == 'edit' ){
				unset($questArr['ac']);
				!isset($questArr['ggpg_id2']) && $questArr['ggpg_id2'] = -1;
				//die(SUtil::P($questArr));
				$ggposition_db->edit_add($questArr,$questArr['ggp_id']) !== false ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'广告位修改成功'))) : SUtil::success('广告位修改成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'广告位修改失败'))) : SUtil::error('广告位修改失败') );
				
			}else{//添加
				
				$questArr['dateline'] = time();
				$ggposition_db->edit_add($questArr) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'广告位添加成功'))) : SUtil::success('广告位添加成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'广告位添加失败'))) : SUtil::error('广告位添加失败') );
				
			}
			
			//$questArr
			
		}
		
		
		/**
		 * 删除处理方法
		 */
		public function pagedel(){
			
			//Token令牌校验
			if ( !is_array($this->request['check']) ){
		
 				$this->request['K'] != SUtil::create_token($this->request['check'].$this->request['T']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
		
			}else{
		
				$this->request['POST_K'] != SUtil::create_token($this->request['POST_T'].$this->request['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
		
			}
	
			$questArr = SUtil::html_arr($this->request);
			//die(SUtil::P($questArr));
				
			//广告位model
			$ggposition_db = new Model_ggposition_ggposition();
			$ggposition_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
			//删除
			$ggposition_db->del($questArr['check']) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'删除成功'))) : SUtil::success('删除成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'删除失败'))) : SUtil::error('删除失败') );
		
				
				
		}
		
		/**
		 * 异步联动分组处理
		 */
		public function pageAjaxGroup(){
			
			SUtil::isAjax() || die('error');
			$questArr = SUtil::html_arr($this->request);
			
			//分组model
			$ggmanage_db = new Model_ggmanage_ggmanage();
			$ggmanage_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$ggmanage_results = $ggmanage_db->getChildren($questArr['id']);
			
			die(json_encode($ggmanage_results['list']));
			
		}
		
		
		/**
		 * 异步校验广告位名称处理方法
		 */
		public function pageAjaxCheckName(){
			
			SUtil::isAjax() || die('error');
			
			//广告位model
			$ggposition_db = new Model_ggposition_ggposition();//广告位model
			$ggposition_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$questArr = SUtil::html_arr($this->request);
			
			if ( $ggposition_db->exist_Name($questArr['name']) != NULL ){
				
				$msg_data['status'] = 0;
				$msg_data['info'] = '该广告位名称已存在';
				
			}else{
				
				$msg_data['status'] = 1;
				$msg_data['info'] = '广告位名称可用';
				
			}
			
			die(json_encode($msg_data));
			
		}
		
	}