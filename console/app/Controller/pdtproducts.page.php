<?php 
	
	/**
	 * 产品控制器
	 * @author Administrator
	 *
	 */
	header('Content-type:text/html;charset=utf-8');
	class Controller_pdtproducts extends Controller_basepage {
		
		/**
		 * 商品列表视图
		 * @param unknown $inPath
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageList($inPath){					
		    
			//获取分页
			$page = $this->getPageNumber();
			
			$srow=$this->request['row'];
								
			//产品model
			$mdl_pdt = new Model_pdt_products();
			$data=$mdl_pdt->getListAlllistpdt($page,$srow);			
			$list=$data['list'];
			
			
			#设置好要放入模版的数据（变量）
			$param[LIST_VAR_NAME] = $list;
			$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath,30);
			$param['srow']=$srow;						
			
			return $this->render($this->tplFilePath,$param);
			
		}
		
		
		/**
		 * 产品编辑（添加）视图
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageEdit(){
			//die(SUtil::P($this->request));
			$questArr = SUtil::html_arr($this->request);
			//die(SUtil::P($questArr));
		//	$questArr['K'] != SUtil::create_token($questArr['pdt_id'].$questArr['T']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			//药剂类型
			$mdl_pdt = new Model_pdt_products();
			$md_type_list = $mdl_pdt->getMedicamentTypes();
			$this->ass('md_type_list',$md_type_list);
				
			//产品model
			$products_db = new Model_pdt_products();
			$products_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//产品分类model
			$pdtTypes_db = new Model_pdt_pdtTypes();
			$pdtTypes_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$products_result = $products_db->getOneById($questArr['pdt_id']);
		//	die(SUtil::P($products_result));
			
			//重组数据获取分组
			if ( $products_result['medicare_type'] == 2 ){
					
				$products_result['medicare_type'] = array('name'=>'医保甲类型','value'=>2);
					
			}elseif ( $products_result['medicare_type'] == 3 ){
					
				$products_result['medicare_type'] = array('name'=>'医保乙类型','value'=>3);
					
			}else{
					
				$products_result['medicare_type'] = array('name'=>'非医保','value'=>1);
					
			}
			
		
			
			//获取分类
			$type1_id = $products_result['type1_id'] != NULL ? $pdtTypes_db->getParent($products_result['type1_id']) : '';//一级分类
			$type2_id = $products_result['type2_id'] != NULL ? $pdtTypes_db->getParent($products_result['type2_id']) : '';//二级分类
			
			//重组数据
			$products_result['type1_id'] = array('name'=>$type1_id['pt_name'],'value'=>$type1_id['pt_id']);
			$products_result['type2_id'] = array('name'=>$type2_id['pt_name'],'value'=>$type2_id['pt_id']);
			
			$products_result['label'] = explode(',', $products_result['label']);
			
		//die(SUtil::P($products_result));
			
			//获取一级分类
			$group = $pdtTypes_db->getGroup();
			
			//die(SUtil::P($group));
			
			//======================= Token授权 POST Start =======================//
				$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
				$TOKEN['POST_T2'] = time();
				$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
				$TOKEN['POST_K'] = SUtil::create_token($data);
			//======================= Token授权 POST End =======================//
			
			$tplArr = array(
					'list'=>$products_result,
					'group'=>$group['list'],
					'TOKEN'=>$TOKEN,
			);
			
			return $this->template($tplArr);	
			
		}
		
		
		/**
		 * 提交编辑处理方法
		 */
		public function pagedoEdit(){
			
			$questArr = SUtil::html_arr($this->request);
	//		$questArr['POST_K'] != SUtil::create_token($questArr['POST_T'].$questArr['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			
			SUtil::Is_number($questArr['pdt_id']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
			
			//产品model
			$products_db = new Model_pdt_products();
			$products_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//产品名称
			( $questArr['name'] || mb_strlen($questArr['name']) > 30 ) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'产品名称不能为空且长度不能大于30'))) : SUtil::error('产品名称不能为空且长度不能大于30') );
			
			//产品图片
			/* $questArr['upPath'][0] == NULL ? ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请上传产品图片'))) : SUtil::error('请上传产品图片') ) : $questArr['img'] = $questArr['upPath'][0];
			unset($questArr['upPath']); */
			foreach ( $questArr['flashatt'] as $v ){
				
				$img_path = $v['path'];
				
			}
			
			$questArr['img'] = $img_path;
		//	die(SUtil::P($img_path));
			
			//分类
			( SUtil::Is_number($questArr['type1_id']) == NULL  ) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择分类'))) : SUtil::error('请选择分类') );
			
			//医保类型
			SUtil::Is_number($questArr['medicare_type']) == NULL  && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择医保类型'))) : SUtil::error('请选择医保类型') );
			
			//是否中标
		//	SUtil::Is_number($questArr['zb_type']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请选择中标类型'))) : SUtil::error('请选择中标类型') );
			
			//排序
			SUtil::Is_number($questArr['order']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'排序只能填写数字'))) : SUtil::error('排序只能填写数字') );
			
			//中标区域
		/*	
			if ( $questArr['zb_type'] == 1 ){
				
				$questArr['zb_area']=$questArr['zb_area'];
			//	$questArr['zb_area'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'中标区域不能为空'))) : SUtil::error('中标区域不能为空') );
				
			}else{
				
			//	unset($questArr['zb_area']);
				$questArr['zb_area']=$questArr['zb_area'];
			}
		*/	
			//批准文号
			$questArr['confirm_code'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'批准文号不能为空'))) : SUtil::error('批准文号不能为空') );
			
			//生成厂家
			$questArr['producer'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'生产厂家不能为空'))) : SUtil::error('生产厂家不能为空') );
			
			
			//招商地区
			$questArr['area'] == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'招商地区不能为空'))) : SUtil::error('招商地区不能为空') );
			
			//联系方式
			if ( $questArr['link'] == 1 ){//公司联系方式
				
				$cmp_link = $products_db->getCmpLink($questArr['pdt_id']);
				
				//公司名
				$questArr['link_cmp_name'] = $cmp_link['cmp_name'];
				
				//联系人
				$questArr['link_man'] = $cmp_link['link_man'];
				
				//联电话
				$questArr['link_tel'] = $cmp_link['mobile'];
				
				//联qq
				unset($questArr['link_qq']);
				
				//联系邮箱
				$questArr['link_email'] = $cmp_link['cmp_email'];
				
				//联传真
				$questArr['link_fax'] = $cmp_link['fax'];
				
				//联网址
				$questArr['web_url'] = $cmp_link['web_url'];
				
				//联地址
				$questArr['link_address'] = $cmp_link['cmp_addr'];
				//die(SUtil::P($cmp_link));
				
			}else{
				
				$questArr['link_cmp_name'] == NULL &&  ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'公司名称不能为空'))) : SUtil::error('公司名称不能为空') );
				
				$questArr['link_man'] == NULL &&  ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'联系人不能为空'))) : SUtil::error('联系人不能为空') );
				
				( SUtil::Is_mobile($questArr['link_tel']) == NULL && SUtil::Is_telephone($questArr['link_tel']) == NULL ) &&  ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'联电话格式不对'))) : SUtil::error('联电话格式不对') );
				
				
			}
			
			//标签组合
			if ( $questArr['label'] != NULL ){
				$mdl_pdt_keys = new Model_pdt_keys();
				foreach($questArr['label'] as $val){
					$key_data = $mdl_pdt_keys->getRowByName($val);
					if($key_data['id']){
						$keysdata['count'] = $key_data['count']+1;
						$mdl_pdt_keys->updateRowById($keysdata, $key_data['id']);
					}else{
						$keysdata['name'] = $val;
						$keysdata['count'] = 1;
						$keysdata['dateline'] = NOW;
						$mdl_pdt_keys->addRow($keysdata);
					}
				}
				$questArr['label'] = implode($questArr['label'], ',');
				
			}
			
			//验证码
			strtolower($questArr['code']) != $_SESSION['console_code'] && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'验证码错误'))) : SUtil::error('验证码错误') );
			
			unset($questArr['code']);
			unset($questArr['POST_T']);
			unset($questArr['POST_T2']);
			unset($questArr['POST_K']);
			unset($questArr['link']);
			unset($_SESSION['console_code']);
			unset($questArr['content']);
			unset($questArr['flashatt']);
			
			$questArr = array_filter($questArr);
			
			$edit_status = $products_db->updateById($questArr['pdt_id'], $questArr);
			if ( $edit_status != false ){
				
				$products_db->updateById($questArr['pdt_id'], array('audit_state'=>4));
				
			}
			
			$edit_status !== false ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'编辑产品成功'))) : SUtil::success('产品编辑成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'编辑产品失败'))) : SUtil::error('产品编辑失败') );
			
			
			//die(SUtil::P($questArr));
			
		}
		
		
		/**
		 * 删除处理方法
		 */
		public function pagedel(){
		
			//Token令牌校验
	/*		if ( !is_array($this->request['check']) ){
		
				$this->request['K'] != SUtil::create_token($this->request['check'].$this->request['T']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
		
			}else{
		
				$this->request['POST_K'] != SUtil::create_token($this->request['POST_T'].$this->request['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
		
			}
	*/		
			//	die('222');
			$questArr = SUtil::html_arr($this->request);
			//die(SUtil::P($questArr));
		
			//产品model
			$products_db = new Model_pdt_products();
			$products_db->setCache(SUtil::C('sqlCache'));//设置缓存
		
			//删除
			$products_db->del($questArr['check']) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'删除成功'))) : SUtil::success('删除成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'删除失败'))) : SUtil::error('删除失败') );
		
		
		
		}
		
		/**
		 * 恢复处理方法
		 */
		public function pagerenew(){
		
			//Token令牌校验
/*			if ( !is_array($this->request['check']) ){
		
				$this->request['K'] != SUtil::create_token($this->request['check'].$this->request['T']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
		
			}else{
		
				$this->request['POST_K'] != SUtil::create_token($this->request['POST_T'].$this->request['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
		
			}
*/			
			//	die('222');
			$questArr = SUtil::html_arr($this->request);
			//die(SUtil::P($questArr));
		
			//产品model
			$products_db = new Model_pdt_products();
			$products_db->setCache(SUtil::C('sqlCache'));//设置缓存
		
			//删除
			$products_db->renew($questArr['check']) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'恢复成功'))) : SUtil::success('恢复除成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'恢复失败'))) : SUtil::error('恢复失败') );
		
		
		
		}
		
		/**
		 * 审核处理方法
		 */
		public function pageaudit(){

			//Token令牌校验
/*			if ( !is_array($this->request['check']) ){
					
				$this->request['K'] != SUtil::create_token($this->request['check'].$this->request['T']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
					
			}else{
					
				$this->request['POST_K'] != SUtil::create_token($this->request['POST_T'].$this->request['POST_T2']) && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效') );
					
			}
*/				
			$questArr = SUtil::html_arr($this->request);
				
			
			
			//产品model
			$products_db = new Model_pdt_products();
			$products_db->setCache(SUtil::C('sqlCache'));//设置缓存			
			
			//审核状态
			if ( $questArr['audit'] != 1 && $questArr['audit'] != 2 && $questArr['audit'] != 3 ){
				//die(SUtil::P($questArr));
				SUtil::isAjax() ?  die(json_encode(array('status'=>0,'info'=>'操作失效'))) : SUtil::error('操作失效');
		
			}else{
		
				$audit = $questArr['audit'];
				unset($questArr['audit']);
		
			}
			
			if ( !is_array($questArr['check']) && $audit == 2 ){
				
				$products_db->cmpAudit($questArr['check']) == NULL && ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'请先审核公司'))) : SUtil::error('请先审核公司') );
				
			}
			
			//产品主表
			$mainProducts_db = new Model_pdt_mainProducts();
			$mainProducts_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			
			//审核成功时 导入主表
			if( $products_db->audit($questArr['check'],$audit) !==false ){
				
				if ( $audit == 2 && is_array($questArr['check']) ){//获取通过审核产品
					
				
					$auditSucc = $products_db->cmpAudit($questArr['check']);
					
					
					foreach ( $auditSucc['list'] as $v ){
						
						$sqlStr .= $v['pdt_id'].',';
						
					}
					
					$sqlStr = substr($sqlStr, 0, strrpos($sqlStr, ','));
					
					$mainProducts_db->copy1($sqlStr);
				
				}elseif ( $audit == 2 && !is_array($questArr['check']) ){
					
					$sqlStr = $questArr['check'];
					$mainProducts_db->copy2($sqlStr);
				}
				
				//die(SUtil::P($products_data));
				
			
			( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'审核成功'))) : SUtil::success('审核成功') );
				
			}else{
				
				( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'审核失败'))) : SUtil::error('审核失败') );
				
			}
				
		}
		
		
		/**
		 * 批量导入模板视图
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pagefileInto(){
			
	//		$this->request['K'] != SUtil::create_token($this->request['T'].$this->request['T2']) && SUtil::error('访问失效');
			
			//======================= Token授权 POST Start =======================//
				$TOKEN['POST_T'] = mt_rand(10000000, 99999999);
				$TOKEN['POST_T2'] = time();
				$data = $TOKEN['POST_T'].$TOKEN['POST_T2'];
				$TOKEN['POST_K'] = SUtil::create_token($data);
			//======================= Token授权 POST End =======================//
			
			$tplArr = array(
					
					'TOKEN'=>$TOKEN
					
			);	
			
			return $this->template($tplArr);
			
		}
		
		
		public function pagedoFileInto(){
			
		//	$this->request['POST_K'] != SUtil::create_token($this->request['POST_T'].$this->request['POST_T2']) && SUtil::error('访问失效');
			
			//产品model
			$products_db = new Model_pdt_products();
			$products_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$cmp_id = htmlspecialchars($this->request['cmp_id'],ENT_QUOTES);
			
			$cmp_id == NULL && SUtil::error('公司ID不存在');
			
			$mdl_cmp = new Model_cmp_company();
			
			//公司ID 用户ID校验
			$mdl_cmp->checkCmp($cmp_id) == NULL && SUtil::error('公司ID不存在');
			
			//上传类型
			//$uploadType = array('application/vnd.ms-excel');
		//	die(SUtil::C('console_uploadFile_path'));
			//$upfile_result = SUtil::uploadFile(SUtil::C('console_uploadFile_path'), 'upload', $uploadType);			
			//$upfile_result['status'] == 0 && SUtil::error($upfile_result['info']);			
			//打开上传文件
			//$file = file($_SERVER['DOCUMENT_ROOT'].$upfile_result['path']);
			
			$filename = $_FILES['upload']['tmp_name'];
			$filenames = pathinfo($_FILES['upload']['name']);
			if($filenames['extension']!= 'csv'){
				SUtil::error('请选择要导入的CSV文件');
			}
			if (empty ($filename)) {
				SUtil::error('请选择要导入的CSV文件');
			} 
			$handle = fopen($filename, 'r'); 
			$file = $this->input_csv($handle); //解析csv 
			$len_result = count($file); 
			if($len_result==0){
				SUtil::error('没有任何数据');
			}
			
			//医保类型
			$medicare_type = array(
					
					'非医保'=>1,
					'医保甲类型'=>2,
					'医保乙类型'=>3
					
			);
			
			//药剂类型
			$medicament_type = array(
					
					'药片',
					'胶囊',
					'冲剂',
					
			);
			
			//卸载标题
			unset($file[0]);
		//	die(SUtil::P($file));
			//循环插入数据
			foreach ( $file as $v2 ){
				$sql = $v2;
				
				$name = trim(iconv('gb2312', 'utf-8', $sql[0]));
				$selling_points = trim(iconv('gb2312', 'utf-8', $sql[1]));
				$confirm_code = trim(iconv('gb2312', 'utf-8', $sql[2]));
				$patent_code = trim(iconv('gb2312', 'utf-8', $sql[3]));
				$medicare_type = trim(iconv('gb2312', 'utf-8', $sql[4]));
				$producer = trim(iconv('gb2312', 'utf-8', $sql[5]));
				$area = trim(iconv('gb2312', 'utf-8', $sql[6]));
				$medicament_type = trim(iconv('gb2312', 'utf-8', $sql[7]));
				$spec = trim(iconv('gb2312', 'utf-8', $sql[8]));
				$component = trim(iconv('gb2312', 'utf-8', $sql[9]));
				$usage = trim(iconv('gb2312', 'utf-8', $sql[10]));
				$function = trim(iconv('gb2312', 'utf-8', $sql[11]));
				$supply_term = trim(iconv('gb2312', 'utf-8', $sql[12]));
				$offer = trim(iconv('gb2312', 'utf-8', $sql[13]));
				$remark = trim(iconv('gb2312', 'utf-8', $sql[14]));
				$order = trim(iconv('gb2312', 'utf-8', $sql[15]));
				
				$sqlArr['name'] = $name == NULL ? SUtil::error('请填写产品名称后在上传') : $name;
				$sqlArr['selling_points'] = $selling_points;
				$sqlArr['confirm_code'] = $confirm_code == NULL ? SUtil::error('请填写批准文号后在上传') : $confirm_code;
				$sqlArr['patent_code'] = $patent_code;
				
				$sqlArr['zb_type'] = 1;
				$sqlArr['medicare_type'] = $medicare_type == NULL ? SUtil::error('医保类型填写不正确') : $medicare_type;
				
				//药剂
				$sqlArr['medicament_type'] = $medicament_type  == NULL ? SUtil::error('药剂类型填写错误') : $medicament_type;
				
				$sqlArr['producer'] = $producer == NULL ? SUtil::error('请填写生成厂家后在上传') : $producer;
				$sqlArr['area'] = $area == NULL ? SUtil::error('请填写招商区域后在上传') : $area;
				//$sqlArr['medicament_type'] = $sql[7];
				$sqlArr['spec'] = $spec;
				$sqlArr['component'] = $component;
				$sqlArr['usage'] = $usage;
				$sqlArr['function'] = $function;
				$sqlArr['supply_term'] = $supply_term;
				$sqlArr['offer'] = $offer;
				$sqlArr['remark'] = $remark;
				$sqlArr['order'] = SUtil::Is_number(trim($order)) == NULL ? SUtil::error('排序只能填写数字') : trim($order);
				$sqlArr['cmp_id'] = $cmp_id;
				$sqlArr['pdt_date'] = NOW;
				
				$sqlArr = array_filter($sqlArr);
				//die(SUtil::P($sqlArr));
				$products_db->insert($sqlArr) <= 0 && SUtil::error('导入失败');
				
			}
			
			SUtil::success('导入成功');
			
		}
		
		
		/**
		 * 下载模板处理方法
		 */
		public function pagedownload(){
			SUtil::download(SUtil::C('console_download_path'), 'products_tpl.csv');
			
		}
		
		/**
		 * 异步联动分类处理
		 */
		public function pageAjaxGroup(){
				
			SUtil::isAjax() || die('error');
			$questArr = SUtil::html_arr($this->request);
				
			//产品分类model
			$pdtTypes_db = new Model_pdt_pdtTypes();
			$pdtTypes_db->setCache(SUtil::C('sqlCache'));//设置缓存
				
			$pdtTypes_results = $pdtTypes_db->getChildren($questArr['id']);
				
			die(json_encode($pdtTypes_results['list']));
				
		}
		
		
		public function pageAjaxOrder(){
			
			SUtil::isAjax() || die('error');
			$questArr = SUtil::html_arr($this->request);
		//	die(SUtil::P($questArr));
			//产品model
			$products_db = new Model_pdt_products();
			$products_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			foreach ( $questArr['order']['id'] as $k=>$v ){
				
				$id = $v;
			//	die(var_dump($v));
				$data = array('order'=>$questArr['order']['value'][$k]);
			//	die(SUtil::P($data));
				$products_db->updateById($id, $data) === false && die(json_encode(array('status'=>0,'info'=>'排序过程遇到问题，已终止'))) ;
				
			}
			
			 die(json_encode(array('status'=>1,'info'=>'排序成功')));
			
		}
		
		/**
		 * 验证码生成方法
		 * @return string
		 */
		public function pageCreateCode(){
			
			$code = new SCode();
			$code->config('185','34','4','console_code');
			return $code->create();
		}
		
		/**
		 * 上传图片处理方法
		 */
		public function pageUploadFile(){
			$path = SUtil::C('console_upload_path').$_SESSION['console_id'];//设置上传路径
			//die($path);
			
			$typeArr = array(
					
					'image/jpg',
					'image/jpeg',
					'image/gif',
			);
			
			$upload_result = SUtil::uploadFile($path, $_POST['name'], $typeArr);
			die(json_encode($upload_result));
		}
		
		public function input_csv($handle) { 
			$out = array (); 
			$n = 0; 
			while ($data = fgetcsv($handle, 10000)) { 
				$num = count($data); 
				for ($i = 0; $i < $num; $i++) { 
					$out[$n][$i] = $data[$i]; 
				} 
				$n++; 
			} 
			return $out; 
		} 
		
	}

?>