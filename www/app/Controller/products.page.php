<?php 

	define('SYS_NAME','pdt');  
	/**
	 * 产品控制器
	 * @author Administrator
	 *
	 */
	header('Content-type:text/html;charset=utf-8');
	class Controller_products extends Controller_basepage {
		
		/**
		 * 药品库视图
		 */
		public function pageIndex($inPath){
			
 			//产品主表model
			$mdl_products = new Model_pdt_mainProducts();
			
			//产品分类model
			$pdtTypes_db = new Model_pdt_pdtTypes();
			
			//产品分类model
			$mdl_productsType = new Model_pdt_pdtTypes();

			
			$page = $this->getPageNumber($inPath);//获取分页参数
			
			if ( $this->request['seek'] == 1 ){//搜索
				
				//允许搜索字段数组
				$seekField = array(
						'type1_id',
						'type2_id',
						'medicament_type'
				);
				
				//过滤参数
				$questArr = SUtil::html_arr($this->request);

				//分类
				foreach ( $questArr as $k=>$v ){
					
					if ( in_array($k, $seekField) ){
						$seek .= $k."='{$v}' and ";
					}
					
					
				}
				//die($seek);
				$results = $mdl_products->getListAll($page, $seek);
				
				
				//die(SUtil::P($results));
				
				
			}else{//未搜索
				
				//获取数据
				$results = $mdl_products->getListAll($page);
				
			}
			
			
			
			//数据条数
			$count = $results['count'];
			
			//获取分页字符串
			$pageShow = $this->pageBar($count, $page, $inPath);
				
			//获取一级分类
			$productsType_results = $mdl_productsType->getGroupList();
		//	die(SUtil::P($productsType_results));
			
			//获取药剂类型
			$medicamentType = $mdl_products->getMedicamentTypes();
			//die(SUtil::P($medicamentType));
			
			//数据重组
			foreach ( $results['list'] as &$v ){
				
				//获取2级分分类
				$v['group'] = $pdtTypes_db->getParent($v['type2_id']);

				//中标
				if ( $v['zb_type'] == 1 ){
					
					$v['zb_type'] = '中标';
					
				} elseif ( $v['zb_type'] == 2 ){
					
					$v['zb_type'] = '未中标';
					unset($v['zb_area']);
				}else{
					
					$v['zb_type'] = '不显示';
					$v['zb_area'] = '不显示';
					
				}
				
				//医保类型
				if ( $v['medicare_type'] == 1 ){
					
					$v['medicare_type'] = '医保甲类型';
					
				}elseif ( $v['medicare_type'] == 2 ){
					
					$v['medicare_type'] = '医保乙类型';
					
				}else{
							
					$v['medicare_type'] = '非医保类型';
					
				}
				
				
				//标题限制
				if ( mb_strlen($v['name'], 'utf8') > 20 ){
					
					$v['name'] = mb_substr($v['name'], 0, 20, 'utf8').'...';
					
				}
				
			}
			
	//		die(SUtil::P($results));
		//	die(SUtil::P($productsType_results));
		
			$tplArr = array(
					
					'list'=>$results['list'],
					'pagehtml'=>$pageShow,//分页字符
					'type'=>$productsType_results['list'],
					'medicamentType'=>$medicamentType,//药剂类型
					
			);
			
			
			$mdl_areas=new Model_com_area();
			$row_areas=$mdl_areas->getAllRowset(false,false);
			
			$this->ass('row', $row_areas);
			return $this->template($tplArr);
			
		}
		
		
		/**
		 * 产品对比
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageCompare(){
			
			if ( count($this->request['check']) > 3 ) SUtil::error('对比商品不能超过三个');
			
			if ( count($this->request['check']) < 2 ) SUtil::error('请至少选择两个产品作为对比');
			
			//产品主表model
			$mdl_products = new Model_pdt_mainProducts();
			$mdl_products->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//产品分类model
			$mdl_pdtType = new Model_pdt_pdtTypes();
			$mdl_pdtType->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$questArr = SUtil::html_arr($this->request);
			
			//获取对比数据
			foreach ( $questArr['check'] as $v ){
				
				$product[] = $mdl_products->getProductById($v); 
				
			}
			
			//获取数据分类并重组
			foreach ( $product as &$v ){
				
				$v['type1_id'] = $mdl_pdtType->getParent($v['type1_id']);
				$v['type2_id'] = $mdl_pdtType->getParent($v['type2_id']);
				
				if ( $v['medicare_type'] == 3 ){
					
					$v['medicare_type'] = '医保类型乙类型';
					
				}elseif ( $v['medicare_type'] == 2 ){
					
					$v['medicare_type'] = '医保甲类型';
					
				}else{
					
					$v['medicare_type'] = '非医保类型';
					
				}
				
			}
			
	//		die(SUtil::P($product));
			
			$tplArr = array(
					 
					'product'=>$product
					 
			);
			
			return $this->template($tplArr);
			
		}
		
		
		public function pageProductDetails(){
			
			$questArr = SUtil::html_arr($this->request);
			
			//产品主表model
			$mdl_products = new Model_pdt_mainProducts();
			$mdl_products->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//产品分类model
			$pdtTypes_db = new Model_pdt_pdtTypes();
			$pdtTypes_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//获取数据
			$productsDetails = $mdl_products->associatedData($questArr['id']);
		//	die(SUtil::P($productsDetails));
			//获取一级分类
			$productsDetails['type1_id'] = $pdtTypes_db->getParent($productsDetails['type1_id']);
			
			//获取二级级分类
			$productsDetails['type2_id'] = $pdtTypes_db->getParent($productsDetails['type2_id']);
			
			//医保
			if ( $productsDetails['medicare_type'] == 2 ){
				
				$productsDetails['medicare_type'] = '医保甲类型';
				
			}elseif ( $productsDetails['medicare_type'] == 3 ){
				
				$productsDetails['medicare_type'] = '医保乙类型';
				
			}else{
				
				$productsDetails['medicare_type'] = '非医保';
				
			}
			
			//中标
			if ( $productsDetails['zb_type'] == 1 ){
			
				$productsDetails['zb_type'] = '中标';
			
			}elseif ( $productsDetails['zb_type'] == 3 ){
			
				unset($productsDetails['zb_type']);
			
			}else{
			
				$productsDetails['zb_type'] = '不中标';
			
			}
			
		//	die(SUtil::P($productsDetails));
			
			//die(SUtil::P($this->request));
			$tplArr = array(
					
					'list'=>$productsDetails
					
			);
			
			return $this->template($tplArr);
			
		}
		
		
		public function pageAjaxGroup(){
			
			SUtil::isAjax() || die('error');
			
			//产品分类model
			$mdl_pdtTypes = new Model_pdt_pdtTypes();
			$mdl_pdtTypes->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//获取父级id
			$pid = htmlspecialchars($this->request['pid'], ENT_QUOTES);
			
			$pdtTypes_results = $mdl_pdtTypes->getGroup($pid);
			
			die(json_encode($pdtTypes_results['list']));
			
			
		}
		/**
		 * 产品详情页
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageInfo(){
			
			 $id=intval($this->request['id']);
			 $mdl_pdt=new Model_pdt_products(); 
			 $row_pdt=$mdl_pdt->getRowById($id);
			 
			 $ru=$this->getCompanyFromSession(0);
			 $cvip=$ru['cvip'];			 			 
			 $this->ass("cvip", $cvip);
			 
			 if($this->request['ac']=='save'){
			     $row_cmt=$this->request['row'];
			     $row_cmt['channel']=$this->request['channel'];
			     $row_cmt['channel']=implode(',',$row_cmt['channel']);
			     
			    $mdl_cmt=new Model_cmt_comments();
			    $res=$mdl_cmt->addRow($row_cmt);
			       if($res){
			       	    SUtil::success("发布成功！");
			       }else{
			         	SUtil::error("发布失败！");
			       }
			 
			 }
			 
			 $param['row']=$row_pdt;
			 
			 
			 $this->ass('row', $row_pdt);
			return $this->template($param);
		}
		
		
		
		
		
		
		
		
		
		
		
	}		
?>