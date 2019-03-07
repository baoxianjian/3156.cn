<?php   

	/**
	 * 素材管理控制器
	 */
 	Class Controller_ggmaterials extends Controller_basepage {
 		
 		/**
 		 * 素材列表视图
 		 * @return Ambigous <mixed, string, void, string>
 		 */
 		public function pageList($inPath){
 			     
 			$ggmaterials_db = new Model_ggmaterials_ggmaterials();//实例化规格类
 			//$ggmaterials_db->setCache(SUtil::C('sqlCache'));//设置缓存
 			$page = $this->getPageNumber($inPath);//获取分页参数

            $questArr = SUtil::html_arr($this->request);
            unset($questArr['page']); 

 			//搜索
 			if ($questArr){


 				$questArr = array_filter($questArr);//去除空值
 				
 				if ( isset($questArr['__use_count']) ){//是否被使用
 					
 					$user_count = $questArr['__use_count'];
 					$questArr['__use_count'] = array();
 					
 					if ( $user_count == 1 ){//已被使用
 						
 						$questArr['__use_count'][0] = '>';
 						$questArr['__use_count'][1] = 0;
 						
 					}else{
 						
 						$questArr['__use_count'] = 0;
 						
 					}
 				//	die(SUtil::P($questArr));
 					
 				}

                
 				//$count = $ggmaterials_db->getCountList($questArr);
 				$ggmaterials_results = $ggmaterials_db->getListALl($page, $questArr);
 				$count = $ggmaterials_results['count'];
 				
 			}else{
 				
 				$count = $ggmaterials_db->getCountList();//获取数据总数
 				$ggmaterials_results = $ggmaterials_db->getListALl($page);//获取数据
 				
 			}		
 			
 			$pageShow = $this->pageBar($count, $page, $inPath,20);//获取分页字符串
 		//	die(SUtil::P($ggmaterials_results));
 		
 			//规格model
 			$ggstandard_db = new Model_ggstandard_ggstandard();
            
            
            $type_list = $ggmaterials_db->getTypeList();
 			//$ggstandard_db->setCache(SUtil::C('sqlCache'));//设置缓存
 			//========================= Token授权并重组数据 Start =======================//
	 			foreach ( $ggmaterials_results['list'] as $k=>$v ){
	 			
	 				$ggmaterials_results['list'][$k]['t'] = mt_rand(10000000, 99999999);
	 				$data = $v['ggm_id'].$ggmaterials_results['list'][$k]['t'];
	 				$ggmaterials_results['list'][$k]['k'] = SUtil::create_token($data);
	 				
	 				//时间
	 				$ggmaterials_results['list'][$k]['dateline'] = date('Y-m-d H:i:s', $v['dateline']);
	 				
	 				//规格
	 				$ggstandard_name = $ggstandard_db->getName($v['ggs_id']);
 					$ggmaterials_results['list'][$k]['standard'] = $ggstandard_name['ggs_name'];
                    
	 				$ggmaterials_results['list'][$k]['ggm_type'] =$type_list[$v['ggm_type']];
                    
                    
	 				/* //审核
	 				if ( $v['audit_state'] == 1 ){
	 					
	 					$ggmaterials_results['list'][$k]['audit_state'] = '待审核';
	 					
	 				}elseif ( $v['audit_state'] == 2 ){
	 					
	 					$ggmaterials_results['list'][$k]['audit_state'] = '审核通过';
	 					
	 				}else{
	 					
	 					$ggmaterials_results['list'][$k]['audit_state'] = '审核未通过';
	 					
	 				} */
	 				
	 				//使用状态
	 				$ggmaterials_results['list'][$k]['__use_count'] = $v['__use_count'] > 0 ? '已使用' : '未使用';
	 				
	 				//图片名称
	 				$ggmaterials_results['list'][$k]['src'] = basename($v['src']);
	 					
	 			
	 			}
 			//========================= Token授权并重组数据 End =======================//
 				
 			
 			//======================= Token授权 POST Start =======================//
	 			$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
	 			$TOKEN['POST_T2'] = time();
	 			$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
	 			$TOKEN['POST_K'] = SUtil::create_token($data);
 			//======================= Token授权 POST End =======================//
 		//	die(SUtil::P($ggmaterials_results));
 			$tplArr = array(
 					
 					'pagehtml'=>$pageShow,//分页字符
 					'list'=>$ggmaterials_results['list'],
 					'TOKEN'=>$TOKEN
 					
 			);
 			$questArr['__use_count'] = $user_count;
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
 				$ggm_type_checked=array($row['ggm_type']=>"selected='selected'");
 				$audit_state_checked=array($row['audit_state']=>"selected='selected'");
 				$__use_count_checked=array($row['__use_count']=>"selected='selected'");
 		
 				$this->ass('ggm_type_checked',$ggm_type_checked);
 				$this->ass('audit_state_checked',$audit_state_checked);
 				$this->ass('__use_count_checked',$__use_count_checked);
 		
 				$this->ass('row',$row);
 		
 			}
 		}
 		
 		/**
 		 * 素材管理编辑视图
 		 */
 		public function pageEdit(){
 			
 			$standard_db = new Model_ggstandard_ggstandard();//实例化规格类
 			//$standard_db->setCache(SUtil::C('sqlCache'));//设置缓存
 			
 			if ( $this->request['ac'] == 'edit' ){//编辑
 				
 				$this->request['k'] != SUtil::create_token($this->request['ggm_id'].$this->request['t']) && SUtil::error('操作失效');//Token令牌校验
 				
 				$questArr = SUtil::html_arr($this->request);
 				
 				$ggaterials_db = new Model_ggmaterials_ggmaterials();
 				//$ggaterials_db->setCache(SUtil::C('sqlCache'));//设置缓存
 				
 				$ggaterials_result = $ggaterials_db->findByggm_id($questArr['ggm_id'], '=', '*');
 			//	die(SUtil::P($ggaterials_result));
 				$standard = $standard_db->findByggs_id($ggaterials_result['ggs_id'], '=', array('ggs_name','ggs_type','height','width','length'));
 				$ggaterials_result['standard'] = $standard['list']['0']['ggs_name'];
 				$ggaterials_result['ggs_type'] = $standard['list']['0']['ggs_type'];
 				$ggaterials_result['width'] = $standard['list']['0']['width'];
 				$ggaterials_result['length'] = $standard['list']['0']['length'];
 				$ggaterials_result['height'] = $standard['list']['0']['height'];
 				$hidden = "<input type='hidden' name='ggm_id' value=".$ggaterials_result['ggm_id'].">";
 				
 				$this->ass('hidden', $hidden);
 				$this->ass('list', $ggaterials_result);
 			//	die(SUtil::P($ggaterials_result));
 				
 			}
 		//	die(SUtil::P($standard));
 			$standard_results = $standard_db->findByis_del(1, '!=', array('*'));
 		//	die(SUtil::P($standard_results['list']));
 			//======================= Token授权 POST Start =======================//
	 			$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
	 			$TOKEN['POST_T2'] = time();
	 			$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
	 			$TOKEN['POST_K'] = SUtil::create_token($data);
 			//======================= Token授权 POST End =======================//
 			
	 			
	 		$this->request['ac'] == NULL ? $this->request['ac'] = 'add' : $this->request['ac'] = $this->request['ac'];
 			$tplArr = array(
 					'standard'=>$standard_results['list'],//规格
 					'TOKEN'=>$TOKEN,
 					'ac'=>$this->request['ac'],//请求动作
 			);//模板变量
 			//die(SUtil::P($standard_results));
 			return $this->template($tplArr);
 			
 		}
 		
 		/**
 		 * 异步校验素材名称处理方法
 		 */
 		public function pageAjaxCheckName(){
 			
 			SUtil::isAjax() || die('error');
 			$ggmaterials_db = new Model_ggmaterials_ggmaterials();//实例化规格类
 			$ggmaterials_db->setCache(SUtil::C('sqlCache'));//设置缓存
 			
 			$questArr = SUtil::html_arr($this->request);
 			//die(var_dump($ggmaterials_db->findBytitle($questArr['title'])));
 			if ( $ggmaterials_db->findBytitle($questArr['title']) != NULL ){
 				
 				$msg_data['status'] = 0;
 				$msg_data['info'] = '素材名称已存在';
 				
 			}else{
 				
 				$msg_data['status'] = 1;
 				$msg_data['info'] = '素材名称可用';
 				
 			}
 			
 			die(json_encode($msg_data));
 			
 		}
 		
 		
 		/**
 		 * 添加（编辑）提交表单处理方法
 		 */
 		public function pagedoEdit(){
 			
 			$qusetArr = SUtil::html_arr($this->request);//过滤接收参数
 			//die(SUtil::P($qusetArr));
 			$qusetArr['POST_K'] != SUtil::create_token($qusetArr['POST_T'].$qusetArr['POST_T2']) && SUtil::error('操作失效');//Token令牌校验
 			if ( SUtil::isAjax() ){//异步提交
 					
 					$ggmaterials_db = new Model_ggmaterials_ggmaterials();//实例化规格类
 					$ggmaterials_db->setCache(SUtil::C('sqlCache'));//设置缓存
 					//初始化状态
 					$msg_data['status'] = 1;
 						
 					//表单校验
 					foreach ( $qusetArr as $k=>$v ){
 						
 						$gg_title = $ggmaterials_db->findBytitle($v);
 					
 						switch ($k){
 								
 							case 'mod_id'://公司ID
 								if ( SUtil::Is_number($v) == NULL )
                                {	
 									$msg_data['status'] = 0;
 									$msg_data['info'] = '公司ID格式不对';		
 								}
 								
 								//公司model
 								$cmp_db = new Model_cmp_company();
 								//$cmp_db->setCache(SUtil::C('sqlCache'));//设置缓存
 								
 								if ( $cmp_db->getRowById($v) == NULL )
                                {
 									$msg_data['status'] = 0;
 									$msg_data['info'] = '公司ID不存在';
 								}
 								
 								break;
                             case 'pdt_id'://产品ID
                                 $qusetArr[$k]=intval($v);
                                 if($qusetArr[$k])
                                 {
                                     //产品model
                                     $pdt_db = new Model_pdt_products();
                                     //$cmp_db->setCache(SUtil::C('sqlCache'));//设置缓存
                                     
                                     if ( $pdt_db->getRowById($v) == NULL )
                                     {
                                         $msg_data['status'] = 0;
                                         $msg_data['info'] = '产品ID不存在';
                                     }
                                 }
                                 break;
                                     	
 							case 'title'://素材名称
 								if ( mb_strlen($v) > 30 || empty($v) ){
 					
 									$msg_data['status'] = 0;
 									$msg_data['info'] = '素材名称不能大于30且不能为空';
 					
 								}elseif ( $gg_title != NULL ){
 								//	die(SUtil::P($qusetArr));
 									$gg_result = $ggmaterials_db->findByggm_id($qusetArr['ggm_id'],'=','title');
									if (  $gg_title['title'] != $gg_result['title'] ){
										
										$msg_data['status'] = 0;
										$msg_data['info'] = '素材名称已存在';
										
									}
 									
 									
 								}
 							break;
 							
 							case 'slogan'://广告语
 								if ( mb_strlen($v) > 40 || empty($v) ){
 							
 									$msg_data['status'] = 0;
 									$msg_data['info'] = '广告语长度不能大于40且不能为空';
 							
 								}
 							break;
                            case 'gg_title'://广告语
                                 if ( mb_strlen($v) > 40 || empty($v) ){
                             
                                     $msg_data['status'] = 0;
                                     $msg_data['info'] = '广告标题长度不能大于40且不能为空';
                             
                                 }
                             break;
 							case 'ggs_id'://类型id
 								if ( SUtil::Is_number($v) == NULL ){
 							
 									$msg_data['status'] = 0;
  									$msg_data['info'] = '请选择类型';
 							
 								}
 							break;
 							
 							case 'link_url'://链接地址
 								if (!$v)
                                {
 									$msg_data['status'] = 0;
 									$msg_data['info'] = '请输入链接地址';
 							
 								}
 							break;
 							
 							case 'flashatt'://图片路径
 								
 								foreach ( $v as $value ){
 									
 									$path_img = $value['path'];
 									
 								}
 								$qusetArr['src'] = $path_img;
 							//	die($qusetArr['src']);
 							break;
 							
 							case 'POST_T'://TOKEN
 							case 'POST_T2'://TOKEN
 							case 'POST_K'://TOKEN
 								unset($qusetArr[$k]);
 							break;
 							
 						}
 					
 					}
 					
 					if ( $msg_data['status'] != 1  ){
 						
 						die(json_encode($msg_data));
 						
 					}else{
 						//die(var_dump($qusetArr['type']));
 						$qusetArr['dateline'] = time();//添加时间
 						$qusetArr['ggm_type'] = $qusetArr['type'];//添加时间
 						unset($qusetArr['type']);
 					//	die(SUtil::P($qusetArr));
 						if ( $qusetArr['ac'] == 'add' ){
 							
 							unset($qusetArr['ac']);
 							unset($qusetArr['content']);
 							unset($qusetArr['flashatt']);
 							if ( $ggmaterials_db->upadd($qusetArr) > 0 ){//添加数据
 							
 								$msg_data['status'] = 1;
 								$msg_data['info'] = '添加成功';
 							
 							}else{
 							
 								$msg_data['status'] = 0;
 								$msg_data['info'] = '添加失败';
 							
 							}
 							
 						}elseif( $qusetArr['ac'] == 'edit' ){
 							
 							unset($qusetArr['ac']);
 						//	die(SUtil::P($qusetArr));
 							unset($qusetArr['content']);
 							unset($qusetArr['flashatt']);
 							
 							if ( $ggmaterials_db->updateById($qusetArr['ggm_id'], $qusetArr) !== false ){//添加数据
 							
 								$msg_data['status'] = 1;
 								$msg_data['info'] = '编辑成功';
 							
 							}else{
 							
 								$msg_data['status'] = 0;
 								$msg_data['info'] = '编辑失败';
 							
 							}
 							
 						}
 						
 						
 						die(json_encode($msg_data));
 					}
 							
 				
 			}elseif( SUtil::isPost() ){//同步提交
 					
 					$ggmaterials_db = new Model_ggmaterials_ggmaterials();//实例化规格类
 					$ggmaterials_db->setCache(SUtil::C('sqlCache'));//设置缓存
 					//SUtil::P($qusetArr);die;	
 					//表单校验
 					foreach ( $qusetArr as $k=>$v ){
 					
 						switch ($k){
 								
 							case 'mod_id'://公司ID
 								if ( SUtil::Is_number($v) == NULL ){
 
 									SUtil::error('公司ID格式错误');
 										
 								}
 								break;
 									
 							case 'title'://素材名称
 								if ( mb_strlen($v) > 30 || empty($v) ){
 					
 									SUtil::error('素材ID长度不能大于30且不能为空');
 					
 								}elseif ( $gg_title != NULL ){
 								//	die(SUtil::P($qusetArr));
 									$gg_result = $ggmaterials_db->findByggm_id($qusetArr['ggm_id'],'=','title');
									if (  $gg_title['title'] != $gg_result['title'] ){
										
										SUtil::error('改素材名称已存在');
										
									}
 								}
 							break;
 							
                             case 'slogan'://广告语
                                 if ( mb_strlen($v) > 40 || empty($v) ){
                             
                                     $msg_data['status'] = 0;
                                     $msg_data['info'] = '广告语长度不能大于40且不能为空';
                             
                                 }
                             break;
                            case 'gg_title'://广告语
                                 if ( mb_strlen($v) > 40 || empty($v) ){
                             
                                     $msg_data['status'] = 0;
                                     $msg_data['info'] = '广告标题长度不能大于40且不能为空';
                             
                                 }
                             break;
 							
 							case 'ggs_id'://类型id
 								if ( SUtil::Is_number($v) == NULL ){
 							
 									SUtil::error('error');
 							
 								}
 							break;
 							
 							case 'link_url'://链接地址
 								if ( SUtil::Is_Url($v) == NULL ){
 							
 									SUtil::error('链接地址格式错误');
 							
 								}
 							break;
 							
 							case 'upPath'://图片路径
 								$qusetArr['src'] = $qusetArr['upPath'][0];
 								if ( $qusetArr['src'] == NULL && $qusetArr['__length'] == 0 ){
 									SUtil::error('请选择上传图片');
 								}
 								unset($qusetArr[$k]);
 							break;
 							
 							case 'POST_T'://TOKEN
 								unset($qusetArr[$k]);
 							break;
 							
 							case 'POST_T2'://TOKEN
 								unset($qusetArr[$k]);
 							break;
 							
 							case 'POST_K'://TOKEN
 								unset($qusetArr[$k]);
 							break;
 							
 							case 'ac'://TOKEN
 								unset($qusetArr[$k]);
 							break;
 						}
 					
 					}
 					
 						//die(var_dump($qusetArr['type']));
 						$qusetArr['dateline'] = time();//添加时间
 						$qusetArr['ggm_type'] = $qusetArr['type'];//添加时间
 						unset($qusetArr['type']);
 						
 						function arr_null($v){
 							if ( $v === NULL || $v === '' ){
 								return false;
 							}
 							return true;
 						}
 						$qusetArr = array_filter($qusetArr,'arr_null');
 						
 						//die(SUtil::P($qusetArr));
 						if ( $qusetArr['ac'] == 'add' ){
 							
 							unset($qusetArr['content']);
 							unset($qusetArr['flashatt']);
 							unset($qusetArr['ac']);
 							if ( $ggmaterials_db->upadd($qusetArr) > 0 ){//添加数据
 							
 								SUtil::success('添加成功');
 							
 							}else{
 							
 								SUtil::error('添加失败');
 							
 							}
 							
 						}else{
 							//	die(SUtil::P($qusetArr));
 							unset($qusetArr['edit']);
 							unset($qusetArr['content']);
 							unset($qusetArr['flashatt']);
 						//	die('222');
 							if ( $ggmaterials_db->updateById($qusetArr['ggm_id'], $qusetArr) > 0 ){//添加数据
 							
 								SUtil::success('编辑成功');
 							
 							}else{
 							
 								SUtil::error('编辑失败');
 							
 							}
 							
 						}
 						
 				
 			}else{
 				
 				SUtil::error('error');
 				
 			}
 			
 		}
 		
 		
 		/**
 		 * 删除处理方法
 		 */
 		public function pagedel(){
 			
 			$questArr = SUtil::html_arr($this->request);
 			$ggmaterials_db = new Model_ggmaterials_ggmaterials();//实例化规格类
 			$ggmaterials_db->setCache(SUtil::C('sqlCache'));//设置缓存
 			
 			if ( SUtil::isAjax() ){
 				
 				if ( is_array($questArr['check']) ){//异步多选删除
 					
 					$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && die(json_encode(array('status'=>0,'info'=>'操作失效')));//Token令牌校验
 					$delStr = implode($questArr['check'], ',');
 					
 					$use_count = $ggmaterials_db->getList("ggm_id in (".$delStr.") and __use_count>0",'ggm_id');
 				//	die(SUtil::P($questArr['check']));
 					foreach ( $use_count['list'] as $k=>$v ){
 						
 						foreach ( $questArr['check'] as $k2=>$v2 ){
 							
 							if ( $v['ggm_id'] == $v2 ){
 								//die($v2);
 								unset($questArr['check'][$k2]);
 							}
 							
 						}
 						
 					}
 					$delStr = implode(',', $questArr['check']);
 				//	die(SUtil::P($delStr));
 					
 					$ggmaterials_db->del($delStr, true) > 0 ? ($use_count['list']!=NULL ? die(json_encode(array('status'=>1,'info'=>'删除成功,已为您过滤掉正在使用数据'))) : die(json_encode(array('status'=>1,'info'=>'删除成功')))) : die(json_encode(array('status'=>0,'info'=>'删除失败')));
 					
 				}else{//单选异步删除
 					
 					$questArr['k'] != SUtil::create_token($questArr['ggm_id'].$questArr['t']) && die(json_encode(array('status'=>0,'info'=>'操作失效')));//Token令牌校验
 					
 					//使用状态判断
 					$use_count = $ggmaterials_db->findByggm_id($questArr['ggm_id'],'=','__use_count');
 					$use_count['__use_count'] > 0 && die(json_encode(array('status'=>0,'info'=>'该素材正在使用当中，无法删除')));
 					
 					$ggmaterials_db->del($questArr['ggm_id']) > 0 ? die(json_encode(array('status'=>1,'info'=>'删除成功'))) : die(json_encode(array('status'=>0,'info'=>'删除失败')));
 					
 				}
 				
 			}elseif( SUtil::isGet() ){//单选删除
 				
 				$questArr['k'] != SUtil::create_token($questArr['ggm_id'].$questArr['t']) && SUtil::error('操作失效');//Token令牌校验
 				
 				//使用状态判断
 				$use_count = $ggmaterials_db->findByggm_id($questArr['ggm_id'],'=','__use_count');
 				$use_count['__use_count'] > 0 && SUtil::error('该素材正在使用当中，无法删除');
 				
 				$ggmaterials_db->del($questArr['ggm_id']) > 0 ? SUtil::success('删除成功') : SUtil::error('删除失败');
 				
 			}elseif( SUtil::isPost() ){//多选删除
 			
 				$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && SUtil::error('操作失效');//Token令牌校验
 				
 				$delStr = implode($questArr['check'], ',');
 				
 				//使用状态判断
 				$use_count = $ggmaterials_db->getList("ggm_id in (".$delStr.") and __use_count>0",'ggm_id');
 				//	die(SUtil::P($questArr['check']));
 					foreach ( $use_count['list'] as $k=>$v ){
 						
 						foreach ( $questArr['check'] as $k2=>$v2 ){
 							
 							if ( $v['ggm_id'] == $v2 ){
 								//die($v2);
 								unset($questArr['check'][$k2]);
 							}
 							
 						}
 						
 					}
 					$delStr = implode(',', $questArr['check']);
 				//	die(SUtil::P($delStr));
 					
 				$ggmaterials_db->del($delStr, true) > 0 ? ($use_count['list']!=NULL ? SUtil::success('删除成功,已为您过滤掉正在使用数据') : SUtil::success('删除成功')) : SUtil::error('删除失败');
 				
 			/* 	$delStr = implode($questArr['check'], ',');
 				$ggmaterials_db->del($delStr, true) > 0 ? SUtil::success('删除成功') : SUtil::error('删除失败'); */
 				
 			}
 			
 			
 		}
 		
 		/**
 		 * 上传图片处理方法
 		 */
 		public function pageUploadFile(){
 			$path = SUtil::C('console_upload_path').$_SESSION['console_id'];//设置上传路径
 		//	die($path);
 			$upload_result = SUtil::uploadFile($path, $_POST['name']);
 			echo json_encode($upload_result);die;
 		}
 	
        public function pageCheckCID($inPath)
        {
            $cid=intval($this->request['id']);
            
            $mdl_cmp = new Model_cmp_company();
            $row=$mdl_cmp->getRowById($cid,'page_url,cmp_name');
            
           
            if($row)
            {
                if($row['page_url']) 
                {
                    $url='http://www.3156.test/zhaoshang/'.$row['page_url'].'/';
                }
                $result=array('cn'=>"<font color=\"green\">厂商ID:{$cid}存在,其名称为：{$row['cmp_name']}</font>",'url'=>$url);     
                $type=1;
            }
            else             
            {
                $result=array('cn'=>"<font color=\"red\">厂商ID:{$cid}不存在</font><br/>",'url'=>''); 
                $type=3;
            }
            
            $this->showMessage(json_encode($result),$type);
            exit;  
        }
    }
?>
