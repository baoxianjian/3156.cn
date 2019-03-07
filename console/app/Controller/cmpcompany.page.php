<?php 
	/**
	 * 产品控制器
	 * @author Administrator
	 *
	 */
	header('Content-type:text/html;charset=utf-8');
	class Controller_cmpcompany extends Controller_basepage {
		
		/**
		 * 商品列表视图
		 * @param unknown $inPath
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageList($inPath){
	          
			$questArr = SUtil::html_arr($this->request);
			$questArr['way']=$this->request['way'];
			$questArr['real_name']=$this->request['real_name'];
			unset($questArr['page']); 

            
			$page = $this->getPageNumber($inPath);//获取分页参数
			
			//厂商model
			$company_db = new Model_cmp_company();
			$company_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//指派员
			
			$mdl_adm = new Model_sys_admins();
			
			$list_sales = $mdl_adm->getRowsetBySysMark('ggxs');
			

			
				
            $this->ass('list_sales', $list_sales);
			//搜索
			if ($questArr){

				//unset($questArr['seek']);
				
				$questArr = array_filter($questArr);
							
				
				$company_results = $company_db->getListAll($page,$questArr);
				
				foreach ( $company_results['list'] as &$v ){
					//获得一级产品类型
					$mdl_pdtype=new Model_pdt_pdtTypes();
					$data=$mdl_pdtype->getListAlltypeone($page);
					$tplist=$data['list'];
					$this->ass('tplist', $tplist);
					$list_sales = $mdl_adm->getRowById($v['seller_id']);
					$v['seller_id'] = $list_sales;
					$v['list_licence_pic']=explode(',',$v['licence_pic']);
                    $v['reg_time'] = SUtil::formatTime($v['reg_time']);
                    
				}
				
				$count = $company_results['count'];
				
				foreach ( $questArr as $k=>&$v ){
					
					switch ( $k ){
						
						case 'money_grade':
							
							if ( $v == 2 ){
							
								$v = array(
										'name'=>'铜牌',
										'value'=>2
								);
							
							}elseif ( $v == 3 ){
							
								$v = array(
										'name'=>'银牌',
										'value'=>3
								);
							
							}elseif ( $v == 4 ){
							
								$v = array(
										'name'=>'金牌',
										'value'=>4
								);
							
							}elseif ( $v == 5 ){
							
								$v = array(
										'name'=>'砖石',
										'value'=>5
								);
							
							}else{
									
								$v = array(
										'name'=>'免费',
										'value'=>1
								);
									
							}
							
							break;
							
						case 'start_time':
						case 'end_time':
							
							$v = date('Y-m-d', $v);
							
							break;
					}
					
				}
				
				
			}else{
				  $company_results = $company_db->getListAll($page);
				  
				  
				  //获得一级产品类型
				  $mdl_pdtype=new Model_pdt_pdtTypes();
				  $data=$mdl_pdtype->getListAlltypeone($page);
				  $tplist=$data['list'];
				  $this->ass('tplist', $tplist);
				  				  
				  				 			        							  
			   	  foreach ( $company_results['list'] as &$v )
			   	  {
				//	$list_sales = $mdl_adm->getRowById($v['seller_id']);
				//	$v['seller_id'] = $list_sales;
							
					    $v['list_licence_pic']=explode(',',$v['licence_pic']);
										
				  }
				  $count = $company_results['count'];									  
				  
			}
			
			
			$this->ass('seekArr', $questArr);								
			$pageShow = $this->pageBar($count, $page, $inPath,30);//获取分页字符串			
			//重组数据
			foreach ( $company_results['list'] as &$v ){
			//时间格式转化
				$v['start_time'] = SUtil::formatTime($v['start_time']);
				if($v['end_time']){
				    $v['end_time'] = date('Y-m-d', $v['end_time']);
				}else{
					$v['end_time'] ='-';
				}
				
				//收费类型
				$v['cmp_type_temp'] = $v['cmp_type'] == 6 ? '收费' : '免费';
                $v['cmp_type_color'] = $v['cmp_type'] == 6 ? 'green' : 'red';
				
				//费用等级
				if ( $v['money'] == 0 ){
					
					$v['money'] = '免费';
					
				}elseif ( $v['money'] >= 1 && $v['money'] <= 4999 ){
					
					$v['money'] = '铜牌';
					
				}elseif ( $v['money'] > 4999 && $v['money'] <= 9999 ){
					
					$v['money'] = '银牌';
					
				}elseif ( $v['money'] > 9999 && $v['money'] <= 19999 ){
					
					$v['money'] = '金牌';
					
				}elseif ( $v['money'] >= 20000 ){
					
					$v['money'] = '砖石';
					
				}else{
					
					$v['money'] = '暂无';
					
				}
				
				$v['cmp_lv'] = $this->ischecklv($v['cmp_lv']);
				
				
				//审核状态
				//公司审核状态
				if ( $v['audit_state'] == 1 ){
				
					$v['audit_state'] = '待审核';
				
				}elseif ( $v['audit_state'] == 2 ){
				
					$v['audit_state'] = '审核通过';
				
				}elseif ( $v['audit_state'] == 3 ){
				
					$v['audit_state'] = '审核未通过';
				
				}else{
				
					$v['audit_state'] = '需重新审核';
				
				}
				
				//======================= Token授权 POST Start =======================//
					$v['T'] = mt_rand(10000000, 99999999);
					$data = $v['cmp_id'].$v['T'];
					$v['K'] = SUtil::create_token($data);
				//======================= Token授权 POST End =======================//
				
				
			}
		//	die(SUtil::P($company_results));
			//======================= Token授权 POST Start =======================//
				$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
				$TOKEN['POST_T2'] = time();
				$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
				$TOKEN['POST_K'] = SUtil::create_token($data);
			//======================= Token授权 POST End =======================//
			
			$tplArr = array(
				
 				'list'=>$company_results['list'],
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
			
			//指派员
			$mdl_adm = new Model_sys_admins();
			$list_sales = $mdl_adm->getRowsetBySysMark('ggxs');
			
			//编辑
			if ( $this->request['ac'] == 'edit' ){
				
				$questArr['K'] != SUtil::create_token($questArr['cmp_id'].$questArr['T']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
				
				//厂商model
				$company_db = new Model_cmp_company();
				$company_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
				$company_result = $company_db->getRowById($questArr['cmp_id']);
				
				//自定义公司编号
				/*$company_result['custom_num'] = substr($company_result['static_url'], strrpos($company_result['static_url'], '/')+1);
				$company_result['custom_num'] = substr($company_result['custom_num'], 0, strrpos($company_result['custom_num'], '/'));*/
				
				//时间格式转化
				if($company_result['end_time']){
					$company_result['end_time'] = date('Y-m-d H:i:s', $company_result['end_time']);
				}
				
				//图片
				$company_result['page_banner'] = json_decode($company_result['page_banner']);
				$mdl_user = new Model_user_user();
				$userdata = $mdl_user->getRowById($questArr['cmp_id']);
				$company_result['userdata'] = $userdata;
				
				$this->ass('list', $company_result);
				
				$sales = $mdl_adm->getRowById($company_result['seller_id']);
				
			//	die(SUtil::P($list_sales));
				
				
			}
			
			//die(SUtil::P($list_sales));
			
			//======================= Token授权 POST Start =======================//
				$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
				$TOKEN['POST_T2'] = time();
				$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
				$TOKEN['POST_K'] = SUtil::create_token($data);
			//======================= Token授权 POST End =======================//
			
			$tplArr = array(

				'TOKEN'=>$TOKEN,
				'list_sales'=>$list_sales,
				'sales'=>$sales
			);
			//die(SUtil::P($tplArr));
			return $this->template($tplArr);		
			
		}
		
		
		/**
		 * 编辑（添加）通告处理方法
		 */
		public function pagedoEdit(){
			
			$questArr = SUtil::html_arr($this->request);
			
            $cmp_id=intval($questArr['cmp_id']);
	//		$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			
			//厂商model
			$company_db = new Model_cmp_company();
	//		$company_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//提交类型判断
			switch ( $questArr['submitType'] ){
				
				//普通
				case 'layfolk':
					
                    $questArr['seller_id']=intval($questArr['seller_id']);
                    $questArr['phone_api_id']=intval($questArr['phone_api_id']); 
                    $questArr['cmp_lv']=intval($questArr['cmp_lv']);  
                    
                    
					//来电显示
					( $questArr['phone_flag'] != 1 && $questArr['phone_flag'] != 2 ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择来电展示'))) : SUtil::error('请选择来电展示') );
					
					//证件展示
					( $questArr['show_credential'] != 1 && $questArr['show_credential'] != 2 ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择证件展示'))) : SUtil::error('请选择证件展示') );

					//Banner图片
					/*( $questArr['show_banner'] != 1 && $questArr['show_banner'] != 2 ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择banner图片'))) : SUtil::error('请选择banner图片') );*/
					
					//销售人员
					( SUtil::Is_number($questArr['seller_id']) == NULL && $questArr['seller_id'] != NULL ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'销售人员选择错误'))) : SUtil::error('销售人员选择错误') );
					
					//消费金额
					$questArr['money']=intval($questArr['money']);
					
					if($questArr['money']>=0 && $questArr['money']<1500){
						$questArr['cmp_lv']=1;
					}elseif($questArr['money']>=1500 && $questArr['money']<5000){
						$questArr['cmp_lv']=2;
					}elseif($questArr['money']>=5000 && $questArr['money']<30000){
						$questArr['cmp_lv']=3;
					}elseif($questArr['money']>=30000 && $questArr['money']<50000){
						$questArr['cmp_lv']=4;
					}elseif($questArr['money']>=50000){
						$questArr['cmp_lv']=5;
					}
					
					/*( SUtil::Is_number($questArr['money']) == NULL && $questArr['money'] !== '0' )  && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请填写正确的消费金额'))) : SUtil::error('请填写正确的消费金额') );*/
					
					//默认广告语
					mb_strlen($questArr['slogan'],'utf8') > 20 && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'默认广告语长度不能大于20'))) : SUtil::error('默认广告语长度不能大于20') );
					
					//商机通
					( $questArr['phone_api_status'] != 1 && $questArr['phone_api_status'] != 2 ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择商机通'))) : SUtil::error('请选择商机通') );
					if ( $questArr['phone_api_status'] == 1 ){
						
						( SUtil::Is_number($questArr['phone_api_id']) == NULL || mb_strlen($questArr['phone_api_id'],'utf8') != 8 ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'商机通ID格式错误'))) : SUtil::error('商机通ID格式错误') );
						
					}
					
					$questArr['slogan'] === '' && $questArr['slogan'] = NULL;//广告语可为空
					
					break;
					
				//高级
				case 'forminfo':
						
					//会员类型
					( $questArr['cmp_type'] != 6 && $questArr['cmp_type'] != 2 ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择会员类型'))) : SUtil::error('请选择会员类型') );
					
					//日期
					( $questArr['end_time'] == NULL ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'时间不能为空'))) : SUtil::error('时间不能为空') );
					
					$questArr['end_time'] = strtotime($questArr['end_time']);
					
					break;
					
				//自助建站
				case 'self_help':
					
					//页面类型
					//( $questArr['page_type'] != 1 && $questArr['page_type'] != 2 ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择页面类型'))) : SUtil::error('请选择页面类型') );
				//	die(SUtil::P($questArr));
				
					//公司链接id校验
					$url_num = $company_db->getRowById($questArr['custom_num']);
					( $url_num != NULL && $url_num['cmp_id'] != $questArr['cmp_id'] ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'该公司链接已存在'))) : SUtil::error('该公司链接已存在') );
					
					//图片获取
					if ( $questArr['flashatt'] != NULL ){
						
						foreach ( $questArr['flashatt'] as $v ){
							
							$imgArr[] = $v['path'];
							
						}
						
						$questArr['page_banner'] = json_encode($imgArr);
						unset( $questArr['flashatt']);
						
					}
					
					break;
				
				default:die('error');
				
			}
			
						
			unset($questArr['POST_T']);
			unset($questArr['POST_T2']);
			unset($questArr['POST_K']);
			unset($questArr['ac']);
			unset($questArr['submitType']);
			unset($questArr['custom_num']);
			
			//unset($questArr['title']);
			//unset($questArr['keywords']);
			//unset($questArr['description']);
			//unset($questArr['ftptpl']);
		//	die(SUtil::P($questArr));
			
			//去除空值
            /*
			$questArr = array_filter($questArr,function($v){
				
				if ( $v === '' ){
					
					return false;
					
				}
				
				return true;
				
			});
            */
			
		//die(SUtil::P($questArr));
		
		//die($questArr['cmp_id']);
		//$questArr['audit_state'] = 1;
		if ( SUtil::Is_password($questArr['user_pwd2']) != NULL ){
			
			foreach ( $questArr as $k=>&$v ){
				
				if ($k == 'user_pwd2' ){
					
					$data['U.'.$k] = md5($v);
					
				}else{
					
					$data['C.'.$k] = $v;
					
				}
				
			}
			
			//厂商model
			$company_db = new Model_cmp_company();							
			$row_cmp=$company_db->getRowById($questArr['cmp_id']);				
			$mdl_user=new Model_user_user();

		//	$mdl_user->updateRowById($data,$row_cmp['user_id']));
			$mdl_user->updateRowById(array('user_pwd2'=>md5($questArr['user_pwd2'])),$row_cmp['user_id'])!== false ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'厂商修改成功'))) : SUtil::success('厂商修改成功') )  :  ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'厂商修改失败'))) : SUtil::error('厂商修改失败') );
		//	die(SUtil::P($data));
		//	$company_db->updateReTable($questArr['cmp_id'], $data) !== false ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'厂商修改成功'))) : SUtil::success('厂商修改成功') )  :  ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'厂商修改失败'))) : SUtil::error('厂商修改失败') );
			
		}else{
			
			$questArr['user_pwd2'] != NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'密码格式错误'))) : SUtil::error('密码格式错误') );

			unset($questArr['user_pwd2']);
            

			 
			$company_db->updateRowById($questArr,$cmp_id) !== false ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'厂商修改成功'))) : SUtil::success('厂商修改成功') )  :  ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'厂商修改失败'))) : SUtil::error('厂商修改失败') );
			
		}
			
		
				
			
		//	die(SUtil::P($this->request));
			
		}
		
		
		/**
		 * 审核处理方法
		 */
		public function pageaudit(){
		
			//Token令牌校验
			$this->request['POST_K'] != SUtil::create_token($this->request['POST_T'].$this->request['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
		
			$questArr = SUtil::html_arr($this->request);
		
			//厂商model
			$company_db = new Model_cmp_company();
			$company_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
			//审核状态
			if ( $questArr['audit'] != 1 && $questArr['audit'] != 2 && $questArr['audit'] != 3 ){
				//die(SUtil::P($questArr));
				SUtil::isAjax() ?  die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效');
		
			}else{
		
				$audit = $questArr['audit'];
				unset($questArr['audit']);
		
			}
			$ids = $oids = array();
			foreach($questArr['check'] as $value){
				$comdata = $company_db->getRowById($value);
				if($comdata['cmp_name']){
					$ids[] = $value;
				}else{
					$oids[] = $value;
				}
			}
			if(count($ids)){
				$company_db->updateAudit($ids,$audit) === false && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'审核失败'))) : SUtil::error('审核失败') );
				if(count($oids)){
					$info = implode(',', $oids).'无公司资料，不能审核！';
				}
			SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'审核成功，'.$info))) : SUtil::error('审核成功，'.implode(',', $oids).'无公司资料，不能审核！');
			}else if(count($oids)){
				SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'审核失败，'.implode(',', $oids).'无公司资料，不能审核！'))) : SUtil::error('审核失败，'.implode(',', $oids).'无公司资料，不能审核！');
			}
		
		}
		
		/**
		 * 异步显示资料
		 */
		public function pageAjaxShowData(){
			
			$questArr = SUtil::html_arr($this->request);
			
			//厂商model
			$company_db = new Model_cmp_company();
			$company_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$company_result = $company_db->getRowById($questArr['id']);
			
			die(json_encode($company_result));
			
		}
		
		/**
		 * 异步解锁
		 */
		
		public function pageAjaxdeblocking(){
			
			//厂商model
			$company_db = new Model_cmp_company();
	//		$company_db->setCache(SUtil::C('sqlCache'));//设置缓存						
			$questArr = SUtil::html_arr($this->request);
			
			$row_cmp=$company_db->getRowById($questArr['mark']);
			$mdl_user=new Model_user_user();
	//		$questArr['K'] != SUtil::create_token($questArr['mark'].$questArr['T']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			
	//		if ( $company_db->updateReTable($questArr['mark'],array('can_login'=>$questArr['can_login'],'login_ect=0')) > 0 ){
				
		    if ($res=$mdl_user->updateRowById(array('can_login'=>$questArr['can_login'],'login_ect=0'),$row_cmp['user_id'])){
				
				$msg_data['status'] = 1;
				
				$msg_data['info'] = '解锁成功';
				
			}else{
				
				$msg_data['status'] = 0;
				
				$msg_data['info'] =$res;
				
			}
			
			die(json_encode($msg_data));
			
		}
				
		
		/**
		 * 异步重置密码
		 */
		
		public function pageAjaxRePass(){
			
			$questArr = SUtil::html_arr($this->request);
			
		//	$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && die('error');
			
			//die(SUtil::P($_POST));
			
			//随机生成8位数密码
			for ( $i=0; $i<8; $i++ ){
				
				//随机因子
				$charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
				
				$pass .= $charset[rand(0, strlen($charset)-1)];
				
				
			}
			
			//厂商model
			$company_db = new Model_cmp_company();
		//	$company_db->setCache(SUtil::C('sqlCache'));//设置缓存
			$row_cmp=$company_db->getRowById($questArr['cmp_id']);				
			$mdl_user=new Model_user_user();
			
			
			
			//更新密码
		//	if ( $company_db->updateReTable($questArr['cmp_id'], array('U.user_pwd'=>md5($pass))) !== false ){
			if ($res=$mdl_user->updateRowById(array('user_pwd'=>md5($pass)),$row_cmp['user_id'])){	
				//返回信息数组
	     		$msg_data['info'] = '已成功重置8位随机密码'.$pass;							
				$msg_data['pass'] = $pass;
				
			}else{
				
				$msg_data['重置密码失败'];
				
			}
			
			
			
			die(json_encode($msg_data));
		}
		
		
		/**
		 * 异步重置二级密码
		 */
		public function pageAjaxRePass2(){
			
			$questArr = SUtil::html_arr($this->request);
	//		$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && die('error');
			
			if($questArr['type']=='save'){
				if(empty($questArr['pwd'])){
					$jsondata = array('message'=>'二级密码密码不能为空', 'status'=>'fail');
					echo SUtil::getJson($jsondata);
					exit();
				}else if(strlen($questArr['pwd'])<8){
					$jsondata = array('message'=>'二级密码必须大于等于八位数', 'status'=>'fail');
					echo SUtil::getJson($jsondata);
					exit();
				}
				$pass = $questArr['pwd'];
				$message = '添加二级密码成功';
			}else if($questArr['type']=='edit'){
				for ( $i=0; $i<8; $i++ ){
					//随机因子
					$charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ123456789';
					$pass .= $charset[rand(0, strlen($charset)-1)];
				}
				$message = '已成功重置，二级密码为：<font color="red">'.$pass.'</font>';
			}
			
			//厂商model
			$company_db = new Model_cmp_company();
		//	$company_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$row_cmp=$company_db->getRowById($questArr['cmp_id']);
			
			$mdl_user=new Model_user_user();
			
			
		//	if ($res=$company_db->updateReTable($questArr['cmp_id'], array('U.user_pwd2'=>md5($pass)))){
			//更新密码
			if ($res=$mdl_user->updateRowById(array('user_pwd2'=>md5($pass)),$row_cmp['user_id'])){
				//返回信息数组
				$jsondata = array('data'=>$pass, 'message'=>$message, 'status'=>'success');
			}else{
				$jsondata = array('message'=>"添加二级密码失败", 'status'=>'fail');
			}
			echo SUtil::getJson($jsondata);
			exit();
		}
		
		public function ischecklv($lv){
			switch ($lv) {
				case 1:
					$lvs = '普通厂商';
					break;
				case 2:
					$lvs = '铜牌厂商';
					break;
				case 3:
					$lvs = '银牌厂商';
					break;
				case 4:
					$lvs = '金牌厂商';
					break;
				case 5:
					$lvs = '钻石厂商';
					break;
			}
			return $lvs;
		}
	}

?>