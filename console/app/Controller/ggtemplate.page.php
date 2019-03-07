<?php 
	
	
	/**
	 * 广告模板控制器
	 */

	header('Content-type:text/html;charset=utf-8');
	Class Controller_ggtemplate extends Controller_basepage {
		
		/**
		 * 广告模板列表页视图
		 */
		public function pageList($inPath){
			
			//链接数据库
			$ggtemplate_db = new Model_ggtemplate_ggtemplate();//模板model
			$ggtemplate_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$page = $this->getPageNumber($inPath);//获取分页参数
		//	die(SUtil::P($page));
			//搜索
			if ( $this->request['seek'] == 1 ){
				
				$seekArr = SUtil::html_arr($this->request);
				unset($seekArr['seek']);
				unset($questArr['page']);
				$seekArr = array_filter($seekArr);
				//die(SUtil::P($seekArr));
				$seekArr['is_del'][0] = '!=';
				$seekArr['is_del'][1] = 1;
				$count = $ggtemplate_db->getCountList($seekArr);//获取数据总数	
				$ggtemplate_results = $ggtemplate_db->getListAll($page,$seekArr);
				
			}else{
				
				$count = $ggtemplate_db->getCountList();//获取数据总数	
				$ggtemplate_results = $ggtemplate_db->getListAll($page);
				
			}
			
			
			//========================= Token授权并重组数据 Start =======================//
	 			foreach ( $ggtemplate_results['list'] as $k=>$v ){
	 			
	 				$ggtemplate_results['list'][$k]['t'] = mt_rand(10000000, 99999999);
	 				$data = $v['ggt_id'].$ggtemplate_results['list'][$k]['t'];
	 				$ggtemplate_results['list'][$k]['k'] = SUtil::create_token($data);
	 				
	 				//添加时间
	 				$ggtemplate_results['list'][$k]['dateline']	= date('Y-m-d H:i:s', $v['dateline']);
	 				
	 				//备注字符长度截取
	 				mb_strlen($v['remark'],'utf8') > 20 && $ggtemplate_results['list'][$k]['remark'] = mb_substr($v['remark'], 0, 20, 'utf8').'...';
	 				
	 				//模板类型
	 				$ggtemplate_results['list'][$k]['ggt_type'] = $v['ggt_type'] == 1 ? '文字' : '图片';
	 				
	 				
	 				
	 			}
 			//========================= Token授权并重组数据 End =======================//
 			
	 		//	die(SUtil::P($ggtemplate_results));
	 			
			//======================= Token授权 POST Start =======================//
				$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
				$TOKEN['POST_T2'] = time();
				$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
				$TOKEN['POST_K'] = SUtil::create_token($data);
			//======================= Token授权 POST End =======================//

			
			$pageShow = $this->pageBar($count, $page, $inPath);//获取分页字符串
			
			$tplArr = array(
					
					'TOKEN'=>$TOKEN,//POST提交token
					'list'=>$ggtemplate_results['list'],
					'pagehtml'=>$pageShow,//分页字符
					'ggt_id'=>$seekArr['ggt_id'],
					'ggt_type'=>$seekArr['ggt_type']
					
			);
			
			$this->preparRow($seekArr);
			$this->ass('row', $seekArr);
			//$param['seekArr']=$seekArr;
			return $this->template($tplArr);
			
		}
		
		/**
		 * 广告模板编辑视图
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageEdit(){
			
			//编辑
			if ( $this->request['ac'] == 'edit' ){
				
				//Token令牌校验
				$this->request['k'] != SUtil::create_token($this->request['ggt_id'].$this->request['t']) && SUtil::error('操作失效');
				
				$questArr = SUtil::html_arr($this->request);
				
				//链接数据库
				$ggtemplate_db = new Model_ggtemplate_ggtemplate();//模板model
				$ggtemplate_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
				$ggtemplate_result = $ggtemplate_db->getOneById($questArr['ggt_id']);
				
				//模板代码反转义
				$ggtemplate_result['code'] = htmlspecialchars_decode(stripslashes($ggtemplate_result['code']));
			//	die(SUtil::P($ggtemplate_result));			
				
				//编辑变量注册
				$this->ass('ac', 'edit');
				$this->ass('hidden_id', "<input type='hidden' value=".$ggtemplate_result['ggt_id']." name='ggt_id'>");
				$this->ass('list', $ggtemplate_result);
				
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
		 * 广告模板修改（添加）处理方法
		 */
		public function pagedoEdit(){
			
			$questArr = SUtil::html_arr($this->request);
		//	die(SUtil::P($questArr));
			//Token令牌校验
			$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			
			//链接数据库
			$ggtemplate_db = new Model_ggtemplate_ggtemplate();//模板model
			$ggtemplate_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//查询名称
			$quest_name = $ggtemplate_db->findByname($questArr['name']);
			
			//表单内容校验
			if ( SUtil::Is_number($questArr['ggt_type']) == NULL ){//类型
				
				SUtil::isAjax() ? die(json_encode(array('status'=>0, 'info'=>'请选择一种类型'))) : SUtil::error('请选择一种类型');
				
			}elseif( mb_strlen($questArr['name'],'utf8') > 30 || empty($questArr['name']) ){//模板名称
				
				SUtil::isAjax() ? die(json_encode(array('status'=>0, 'info'=>'模板名称长度不能大于30且不能为空'))) : SUtil::error('模板名称长度不能大于30且不能为空');
				
			}elseif( $quest_name['name'] != NULL ){//模版名称
				
				if ( $questArr['ac'] == 'edit' ){

					$ggt_name = $ggtemplate_db->findByggt_id($questArr['ggt_id'],'=','name');
 					//die($quest_name['name']."<br>".$ggt_name['name']);
					$ggt_name['name'] != $quest_name['name'] && ( SUtil::isAjax() ? die(json_encode(array('status'=>0, 'info'=>'模板名称已存在'))) : SUtil::error('模板名称已存在') );
					
					
				}else{
					
					SUtil::isAjax() ? die(json_encode(array('status'=>0, 'info'=>'模板名称已存在'))) : SUtil::error('模板名称已存在');
					
				}
				
				
			}
			
			if( $questArr['code'] == NULL ){//模板代码
				
				SUtil::isAjax() ? die(json_encode(array('status'=>0, 'info'=>'模板代码不能为空'))) : SUtil::error('模板代码不能为空');
				
			}elseif( $questArr['ggt_type'] == 1 && SUtil::exist_img(htmlspecialchars_decode(stripslashes($this->request['code']),ENT_QUOTES)) != NULL ){//模板代码
				
				SUtil::isAjax() ? die(json_encode(array('status'=>0, 'info'=>'文字类型模板不能包含图片标签'))) : SUtil::error('文字类型模板不能包含图片标签');
				
			}elseif( $questArr['ggt_type'] == 2 && SUtil::exist_img(htmlspecialchars_decode(stripslashes($this->request['code']),ENT_QUOTES)) == NULL ){
				
				SUtil::isAjax() ? die(json_encode(array('status'=>0, 'info'=>'图片类型模板请至少包含一个图片标签'))) : SUtil::error('图片类型模板请至少包含图片标签');
				
			}
			
			//表单校验结束，卸载多余变量
			unset($questArr['ac']);
			unset($questArr['POST_T']);
			unset($questArr['POST_T2']);
			unset($questArr['POST_K']);
			
			//die(SUtil::P($questArr));
			if ( $this->request['ac'] == 'edit' ){//编辑处理
			//	die(SUtil::P($questArr));
				$ggtemplate_db->updateById($questArr['ggt_id'], $questArr) !== false ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1, 'info'=>'模板编辑成功'))) : SUtil::success('模板编辑成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0, 'info'=>'模板编辑失败'))) : SUtil::error('模板编辑失败') );
				
			}else{
				
				$questArr['dateline'] = time();
				$ggtemplate_db->insert($questArr) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1, 'info'=>'模板添加成功'))) : SUtil::success('模板添加成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0, 'info'=>'模板添加失败'))) : SUtil::error('模板添加失败') );
				
			}
			
			
		}
		
		/**
		 * 删除处理方法
		 */
		public function pagedel(){
			
			//Token令牌校验
			if ( !is_array($this->request['check']) ){
				
				$this->request['k'] != SUtil::create_token($this->request['ggt_id'].$this->request['t']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
				
			}else{
				
				$this->request['POST_K'] != SUtil::create_token($this->request['POST_T'].$this->request['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
				
			}
			
			$questArr = SUtil::html_arr($this->request);
			//die(SUtil::P($questArr));
			
			//链接数据库
			$ggtemplate_db = new Model_ggtemplate_ggtemplate();//模板model
			$ggtemplate_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//删除	
			$ggtemplate_db->del($questArr['check']) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'删除成功'))) : SUtil::success('删除成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'删除失败'))) : SUtil::error('删除失败') );
				
			
			
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
				
				$this->ass('ggt_type_checked',$ggt_type_checked);
				
				$this->ass('row',$row);
		
			}
		}
		
		/**
		 * 异步验证模板名称处理方法
		 */
		public function pageAjaxCheckName(){
			
			SUtil::isAjax() || die('error');
			$questArr = SUtil::html_arr($this->request);
			
			if ( mb_strlen($questArr['name'],'utf8') > 30 || empty($questArr['name']) ){
				
				$msg_data['status'] = 0;
				$msg_data['info'] = '模板名称长度不能大于30且不能为空';
				
			}else{
				
				$ggtemplate_db = new Model_ggtemplate_ggtemplate();//模板model
				$ggtemplate_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
				if ( $ggtemplate_db->findByname($questArr['name']) != NULL ){
					
					$msg_data['status'] = 0;
					$msg_data['info'] = '该模板名称已存在';
					
				}else{
					
					$msg_data['status'] = 1;
					$msg_data['info'] = '模板名称可用';
					
				}
				
			}
			
			die(json_encode($msg_data));
			
		}
		
	}


?>