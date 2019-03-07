<?php
	header('Content-type:text/html;charset=utf-8');
	

	/**
	 * 广告单管理控制器
	 * @author Administrator
	 *
	 */
	Class Controller_ggqueue extends Controller_basepage {
		
		
		/**
		 * 广告单列表视图
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageList($inPath){
			
			//广告单model
			$ggqueue_db = new Model_ggqueue_ggqueue();
			$ggqueue_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//分组model
			$ggmanage_db = new Model_ggmanage_ggmanage();
			$ggmanage_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//广告位model
			$ggposition_db = new Model_ggposition_ggposition();
			$ggposition_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$page = $this->getPageNumber();
			
			//搜索
			if ( isset($this->request['seek']) ){
				
				//去除空值
				$questArr = array_filter($this->request);
				
				print_r($questArr);
				
				unset($questArr['page']);
				unset($questArr['seek']);
				
				//die(SUtil::P($questArr));//搜索数组
				
				//提取分组
				$parent_id = $questArr['ggpg_id'];
				$parent_id2 = $questArr['ggpg_id2'];
				
				//卸载原有分组
				unset($questArr['ggpg_id']);
				unset($questArr['ggpg_id2']);

				
				//组合搜索字符
				foreach ( $questArr as $k=>$v ){
					
					if ( strpos($k, 'time') > 0 ){//时间格式
						
						$k == 'start_time' ? $sqlStr .= 'GQ.start_time>='.strtotime($v).' and ' : $sqlStr .= 'GQ.end_time<='.strtotime($v).' and ';
						 
					}else if ($k == 'GQ|contractNo_id'){//时间格式
						$sqlStr .= ' GQ.contractNo_id = "'.$v.'" and ';
					}else{

						$sqlStr .= str_replace('|', '.', $k).'='.$v.' and ';
						
					}
					
					
				}
				
				if ( $parent_id != NULL )
					$parent_id2 != NULL ? $ggposition = $ggposition_db->getParentId2($parent_id2) : $parent_id != NULL && $ggposition = $ggposition_db->getParentId1($parent_id);
				
				//有分组搜索
				if ( $ggposition['list'] != NULL ){
					
					$sqlStr .= 'GQ.ggp_id in (';
					foreach ( $ggposition['list'] as $v ){
							
						$sqlStr .= $v['ggp_id'].',';
			
					}
					
					$sqlStr = substr($sqlStr, 0, strrpos($sqlStr, ',')).') and ';
					
				}
				
				//die($sqlStr);
				//$sqlStr = substr($sqlStr, 0, strrpos($sqlStr, ' and '));
				
				//组合分组搜索字符
				$count = $ggqueue_db->listCount($sqlStr);
				$ggqueue_results = $ggqueue_db->getListAll($page,$sqlStr);
				//die(SUtil::P($ggqueue_results));
				//die($sqlStr);
				//die(SUtil::P($questArr));
				
			}else{
				
				$count = $ggqueue_db->listCount();
				$ggqueue_results = $ggqueue_db->getListAll($page);
				
			}
			
			
			
			$pageShow = $this->pageBar($count, $page, $inPath,20);
			//die(SUtil::P($ggqueue_results));
			
			//遍历重组数据
			foreach ( $ggqueue_results['list'] as &$v ){
				
				//将到期提示
				if ( $v['end_time'] - time() <= 7*24*60*60 ){
					
					ceil(($v['end_time'] - time())/60/60/24) == 0 ? $time = 0 : $time = ceil(($v['end_time'] - time())/60/60/24);
					$v['expire_hint'] = "<span style='color:red;display:inline'>【".$time."】</span>";
					
				}
				
				//时间戳格式转化
				$v['start_time'] = date('Y-m-d H:i:s', $v['start_time']);
				$v['end_time'] = date('Y-m-d H:i:s', $v['end_time']);
				
				//审核状态判断
				if ( $v['audit_status'] == 1 ){
					
					$v['audit_status'] = '待审核';
					
				}elseif ( $v['audit_status'] == 2 ){
					
					$v['audit_status'] = '审核通过';
					
				}elseif ( $v['audit_status'] == 3 ){
					
					$v['audit_status'] = '审核未通过';
					
				}else{
					
					$v['audit_status'] = '需重新审核';
					
				}
				
				
				//销售状态
				if ( $v['gg_sale_state'] == 1 ){
					
					$v['gg_sale_state'] = '付费';
					
				}elseif ( $v['gg_sale_state'] == 2 ){
					
					$v['gg_sale_state'] = '配送';
					
				}else{
					
					$v['gg_sale_state'] = '免费';
					
				}
				
				//======================= Token授权 POST Start =======================//
					$v['T'] = mt_rand(10000000, 99999999);
					$data = $v['ggq_id'].$v['T'];
					$v['K'] = SUtil::create_token($data);
				//======================= Token授权 POST End =======================//
				
			}
			
			//======================= Token授权 POST Start =======================//
				$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
				$TOKEN['POST_T2'] = time();
				$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
				$TOKEN['POST_K'] = SUtil::create_token($data);
			//======================= Token授权 POST End =======================//
			
			$group = $ggmanage_db->getGroupList();
		//	die(SUtil::P($group['list']));
			
			$tplArr = array(
					'list'=>$ggqueue_results['list'],
					'group'=>$group['list'],
					'pagehtml'=>$pageShow,//分页字符
					'TOKEN'=>$TOKEN,
			);
			print_r($questArr);
			$this->ass('row', $questArr);
			return $this->template($tplArr);
			
		}
		
		
		/**
		 * 广告单编辑（添加）
		 */
		public function pageEdit($inPath){
				
			//分组model
			$group_db = new Model_ggmanage_ggmanage();
			$group_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
			$group_results = $group_db->getGroupList();//获取分组数据
		//	die(SUtil::P($group_results));
			
			//编辑
			if ( $this->request['ac'] == 'edit' ){
				//die(SUtil::P($this->request));
				//Token令牌校验
				$this->request['K'] != SUtil::create_token($this->request['ggq_id'].$this->request['T']) && SUtil::error('操作失效');
				$questArr = SUtil::html_arr($this->request);
			//	die(SUtil::P($questArr));
				
				//广告位model
				$ggposition_db = new Model_ggposition_ggposition();
				$ggposition_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
				//广告单model
				$ggqueue_db = new Model_ggqueue_ggqueue();
				$ggqueue_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
				$ggqueue_result = $ggqueue_db->getOneById($questArr['ggq_id']);
				
				//时间格式
				$ggqueue_result['start_time'] = date('Y-m-d H:i:s', $ggqueue_result['start_time']);
				$ggqueue_result['end_time'] = date('Y-m-d H:i:s', $ggqueue_result['end_time']);
				
				//获取广告位
				$ggp = $ggposition_db->getOneById($ggqueue_result['ggp_id']);
				
			 	//获取分组
				$group['parent'] = $group_db->getParent($ggp['ggpg_id']);
				$group['children'] = $group_db->getParent($ggp['ggpg_id2']);
				
				$ggqueue_result['group'] = $group;
				$ggqueue_result['gg_position'] = $ggp;
								
			//	die(SUtil::p($ggqueue_result));
			//	die(SUtil::P($ggqueue_result));
				
				$this->ass('ac', 'edit');
				$this->ass('list', $ggqueue_result);
				$this->ass('hidden', "<input type='hidden' name='ggq_id' value='".$ggqueue_result['ggq_id']."'/>");
				
			}
			
			//======================= Token授权 POST Start =======================//
				$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
				$TOKEN['POST_T2'] = time();
				$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
				$TOKEN['POST_K'] = SUtil::create_token($data);
			//======================= Token授权 POST End =======================//
			
			$tplArr = array(
					'group_list'=>$group_results['list'],//分组
					'TOKEN'=>$TOKEN,
			);
			
			return $this->template($tplArr);
			
		}
		
		
		/**
		 * 编辑（添加）提交处理方法
		 */
		public function pagedoEdit(){
			
			$questArr = SUtil::html_arr($this->request);
			$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			
			//素材model
			$materials_db = new Model_ggmaterials_ggmaterials();
			$materials_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//======================= 业务逻辑判断  Start =============================//
			
				//时间对应
				isset($_SESSION['checkStartTime']) && ( $_SESSION['checkStartTime'] != strtotime($questArr['start_time']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'修改时间后请重新选择广告位'))) : SUtil::error('修改时间后请重新选择广告位') ) );
			
				//合同编号
				$questArr['contractNo_id'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'合同编号不能为空'))) : SUtil::error('合同编号不能为空') );
				
				//时间格
				( SUtil::Is_time($questArr['start_time']) == NULL || SUtil::Is_time($questArr['end_time']) == NULL ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'时间格式错误'))) : SUtil::error('时间格式错误') );

				//广告位
				SUtil::Is_number($questArr['ggp_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择广告位'))) : SUtil::error('请选择广告位') );
				
				//公司ID
				SUtil::Is_number($questArr['cmp_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'公司ID只能是数字'))) : SUtil::error('公司ID只能是数字') );
				$materials_db->getOne("mod_id=".$questArr['cmp_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'公司ID不存在'))) : SUtil::error('素材ID不存在') );
				
				//素材ID
				SUtil::Is_number($questArr['ggm_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'素材ID只能是数字'))) : SUtil::error('素材ID只能是数字') );
				$materials_db->getOne("ggm_id=".$questArr['ggm_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'素材ID不存在'))) : SUtil::error('素材ID不存在') );
				
				$materials_db->getOne("ggm_id=".$questArr['ggm_id'].' and mod_id='.$questArr['cmp_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'素材ID与公司ID不对应'))) : SUtil::error('素材ID与公司ID不对应') );
				
				//专业员
				$questArr['major'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'专业员不能为空'))) : SUtil::error('专业员不能为空') );
				
				//组长
				$questArr['leader'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'组长不能为空'))) : SUtil::error('组长不能为空') );
				
				//经理
				$questArr['manager'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'经理不能为空'))) : SUtil::error('经理不能为空') );
				
				//发布人
			//	$questArr['issuer'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'发布人不能为空'))) : SUtil::error('发布人不能为空') );
				
				//财务
				$questArr['finance'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'财务不能为空'))) : SUtil::error('财务不能为空') );
				
			//======================= 业务逻辑判断  End =============================//
			
			//die( SUtil::P($questArr) );
			
			//写在多余变量
			unset($questArr['ggpg_id']);
			unset($questArr['ggpg_id2']);
			unset($questArr['POST_T']);
			unset($questArr['POST_T2']);
			unset($questArr['POST_K']);
			unset($questArr['ac']);
			
			//时间格式
			$questArr['start_time'] = strtotime($questArr['start_time']);
			$questArr['end_time'] = strtotime($questArr['end_time']);
			$questArr = array_filter($questArr);
			
			//广告单model
			$ggqueue_db = new Model_ggqueue_ggqueue();
			$ggqueue_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//编辑
			if ( $this->request['ac'] == 'edit' ){
				//die(SUtil::P($questArr));
				$ggqueue_db->doEdit($questArr) !== false ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'编辑成功'))) : SUtil::success('编辑成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'编辑失败'))) : SUtil::error('编辑失败') );;
				
			}else{
				
			//	die(SUtil::P($questArr));
				
				$questArr['dateline'] = time();
				
				$questArr['start_time'] > $questArr['end_time'] && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'开始时间不能大于结束时间'))) : SUtil::error('开始时间不能大于结束时间') );
				
				if ( $ggqueue_db->insert($questArr) > 0 ){
					
					unset($_SESSION['checkStartTime']);
					SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'添加成功'))) : SUtil::success('添加成功');
					
				}else{
					
					SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'添加失败'))) : SUtil::error('添加失败');
				}
				
				
			}
			
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
		//	die('222');
			$questArr = SUtil::html_arr($this->request);
			//die(SUtil::P($questArr));
		
			//广告单model
			$ggqueue_db = new Model_ggqueue_ggqueue();
			$ggqueue_db->setCache(SUtil::C('sqlCache'));//设置缓存
		
			//删除
			$ggqueue_db->del($questArr['check']) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'删除成功'))) : SUtil::success('删除成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'删除失败'))) : SUtil::error('删除失败') );
		
		
		
		}
		
		
		public function pageaudit(){
			
			//Token令牌校验
			if ( !is_array($this->request['check']) ){
			
				$this->request['K'] != SUtil::create_token($this->request['check'].$this->request['T']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			
			}else{
			
				$this->request['POST_K'] != SUtil::create_token($this->request['POST_T'].$this->request['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			
			}
			
			$questArr = SUtil::html_arr($this->request);
			
			//广告单model
			$ggqueue_db = new Model_ggqueue_ggqueue();
			$ggqueue_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//审核状态
			if ( $questArr['audit'] != 1 && $questArr['audit'] != 2 && $questArr['audit'] != 3 ){
				//die(SUtil::P($questArr));
				SUtil::isAjax() ?  die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效');
				
			}else{
				
				$audit = $questArr['audit'];
				unset($questArr['audit']);
				
			}
			
			//删除
			$ggqueue_db->audit($questArr['check'],$audit) !==false ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'审核成功'))) : SUtil::success('审核成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'审核失败'))) : SUtil::error('审核失败') );
			
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
			
			//广告位model
			$ggposition_db = new Model_ggposition_ggposition();
			$ggposition_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
			//广告单model
			$ggqueue_db = new Model_ggqueue_ggqueue();
			$ggqueue_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
			$validggp = $ggqueue_db->validGposition();//获取有效广告位id
			//die(SUtil::P($validggp));
				
			//初始化sql字符串
			$sqlStr = '';
				
			//组合sql字符串
			foreach ( $validggp['list'] as $v ){
			
				$sqlStr .= $v['ggp_id'].',';
			
			}
				
			//去除结尾','
			$sqlStr = substr($sqlStr, 0, strrpos($sqlStr, ','));
			//die($sqlStr);
		//	die(SUtil::P($ggmanage_results));
		
			//时间格式判断
			SUtil::Is_time($questArr['startTime']) == NULL ? $questArr['startTime'] = time() : $questArr['startTime'] = strtotime($questArr['startTime']);
				
			$validggp = $ggqueue_db->validGposition($questArr['startTime']);//获取武无效广告位id
			
			$ggposition_results = $ggposition_db->ggQueue2($questArr['id'], $sqlStr);//获取有效广告位数据
			
			$ggmanage_results['ggposition'] = $ggposition_results['list'];
		//	die(SUtil::P($ggmanage_results));
			die(json_encode($ggmanage_results));
			
		}
		
		
		/**
		 * 异步联动广告位处理 
		 */
		public function pageAjaxPosition(){
			
			SUtil::isAjax() || die('error');
			$questArr = SUtil::html_arr($this->request);
			
			//广告位model
			$ggposition_db = new Model_ggposition_ggposition();
			$ggposition_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//广告单model
			$ggqueue_db = new Model_ggqueue_ggqueue(); 
			$ggqueue_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//时间格式判断
			SUtil::Is_time($questArr['startTime']) == NULL ? $questArr['startTime'] = time() : $questArr['startTime'] = strtotime($questArr['startTime']);
			//die(SUtil::P($questArr));
			$validggp = $ggqueue_db->validGposition($questArr['startTime']);//获取武无效广告位id
		//	die(SUtil::P($validggp));
		
			//保存时间session提交时校验
			isset($_SESSION) || session_start();
			$_SESSION['checkStartTime'] = $questArr['startTime'];
			
			//初始化sql字符串
			$sqlStr = '';
			
			//组合sql字符串
			foreach ( $validggp['list'] as $v ){
				
				$sqlStr .= $v['ggp_id'].',';
				
			}
			
			//去除结尾','
			$sqlStr = substr($sqlStr, 0, strrpos($sqlStr, ','));
			//die($sqlStr);
		//	die(SUtil::P($questArr));
			
			$ggposition_results = $ggposition_db->ggQueue($questArr['id'], $sqlStr);//获取有效广告位数据
		//	die(SUtil::P($ggposition_results['list']));
			
			die(json_encode($ggposition_results['list']));
			
			
		}
		
		
		/**
		 * 异步获取素材
		 */
		public function pageAjaxGetMaterials($inPath){
			
			SUtil::isAjax() || die('error');
			//	die(SUtil::P($inPath));
            
			//获取素材数据并分页
            $page = $this->getPageNumber();
            
			//素材model
			$materials_db = new Model_ggmaterials_ggmaterials();
            
            
            

			
			//获取条件
			$condition = array(
                  //  'mid_or_pid'=>array(
                        'mod_id'=>intval($this->request['cmp_id']),
                        'pdt_id'=>intval($this->request['pdt_id']),
                //        ),
			);
			
			
			//	die($page);
			//$count = $materials_db->getCountList($condition);
			$materials_results = $materials_db->getListALl($page,$condition);
			//规格model
			$ggstandard_db = new Model_ggstandard_ggstandard();            
            
            
            
            
            
            
            
            
            
		//	die(SUtil::P($materials_results['list']));
			foreach ( $materials_results['list'] as &$v ){
				
				$v['ggm_type'] == 1 ? $v['ggm_type'] = '文字' : $v['ggm_type'] = '图片';
				$v['pdt_id'] = intval($v['pdt_id']);
				//规格
				$ggstandard_name = $ggstandard_db->getName($v['ggs_id']);
				$v['standard'] = $ggstandard_name['ggs_name'];
								
				if ( $v['__use_count'] <= 0 ){
					
					$v['__use_count'] = '未使用';
					
				}else{
					
					$v['__use_count'] = '已使用';
					
				}
				
			}
			
			$pageShow = $this->pageBar($materials_results['count'], $page, $inPath);//获取分页字符串
			//		die(SUtil::P($materials_results));
			$data['mList'] = $materials_results['list'];
			$data['pagehtml'] = $pageShow;
			//die(SUtil::P($data));
			die(json_encode($data));
			
			
		}
		
		
	}