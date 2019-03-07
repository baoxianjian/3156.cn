<?php
	header('Content-type:text/html;charset=utf-8');
	
	/**
	 * 广告资源管理控制器
	 * @author Administrator
	 *
	 */
	Class Controller_ggresource extends Controller_basepage {
		
		public function pageList($inPath){
			
			//广告位model
			$ggposition_db = new Model_ggposition_ggposition();
			$ggposition_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//分组model
			$ggmanage_db = new Model_ggmanage_ggmanage();
			$ggmanage_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//广告单model
			$ggqueue_db = new Model_ggqueue_ggqueue();
			$ggqueue_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$questArr = $this->request;
				
			$page = $this->getPageNumber($inPath);//获取分页参数
			
			if ( $questArr['seek'] == 1 ){
				$questArr = array_filter($questArr);
				unset($questArr['seek']);
				unset($questArr['page']);
			
				//组合查询字符串
				foreach ( $questArr as $k=>$v ){

					if ( $k == 'gq|start_time' ){
						
						$sqlStr .= str_replace('|', '.', $k).'>='.strtotime($v).' and ';
						
					}elseif ( $k == 'gq|end_time' ){
						
						$sqlStr .= str_replace('|', '.', $k).'<='.strtotime($v).' and ';
						
					}else{
						
						$sqlStr .= str_replace('|', '.', $k).'='.$v.' and ';
						
					}
					
						
				}
		//		die($sqlStr);
		
				//关联广告单搜索
				if ( isset($questArr['gq|start_time']) || isset($questArr['gq|end_time']) ){
					 
					$ggposition_results = $ggposition_db->ggresource($page,$sqlStr);
					$count = $ggposition_results['count'];
				//	die(SUtil::P($ggposition_results));
					
				}else{
					
					$count = $ggposition_db->count($sqlStr);
					$ggposition_results = $ggposition_db->getListAll($page,$sqlStr);
					
				}
				
			
			}else{
			
				$count = $ggposition_db->count();
				$ggposition_results = $ggposition_db->getListAll($page);
			
			}
			
			$pageShow = $this->pageBar($count, $page, $inPath,20);//获取分页字符串
			
			//点击量排序
			if ( $questArr['count_order'] == 'ASC' || $questArr['count_order'] == 'DESC' ){
			
				$count = $ggposition_db->count($sqlStr);
				$ggposition_results = $ggposition_db->getListAll($page,$sqlStr,$questArr['count_order']);
			
			
			}
			
			foreach ( $ggposition_results['list'] as $k=>$v ){
					
				//获取分组
				$group = $ggmanage_db->getParent($v['ggpg_id']);
				$v['ggpg_id2'] != NULL && $group2 = $ggmanage_db->getParent($v['ggpg_id2']);
				$ggposition_results['list'][$k]['group'] = $group['title'];
				$v['ggpg_id2'] != -1 && $ggposition_results['list'][$k]['group'] = $ggposition_results['list'][$k]['group'].'=>'.$group2['title'];
				
				//使用情况
				if ( isset($questArr['gq|start_time']) || isset($questArr['gq|end_time']) ){
					
					$ggposition_results['list'][$k]['use'] = '占用时间段：'.date('Y-m-d H:i:s', $v['start_time']).'到'.date('Y:m:d H:i:s', $v['end_time']);
					
				}else{
					
					$ggposition_results['list'][$k]['use'] = $ggqueue_db->ggposition($v['ggp_id']);
					if ( $ggposition_results['list'][$k]['use']['start_time'] < time() && $ggposition_results['list'][$k]['use']['end_time'] > time() ){
							
						$ggposition_results['list'][$k]['use'] = '正在使用';
							
					}else{
							
						$ggposition_results['list'][$k]['use'] = '未使用';
							
					}
					
				}
				
				
							
				//数据替换
				$ggposition_results['list'][$k]['add_red'] = $v['add_red'] == 1 ? '套红' : '未套红';
				$ggposition_results['list'][$k]['gg_sale_state'] = $v['gg_sale_state'] == 1 ? '付费' :( $v['gg_sale_state'] == 2 ? '配送' : '免费' );
				$ggposition_results['list'][$k]['ggt_type'] = $v['ggt_type'] == 1 ? '文字' : '图片';

				
					
			}
				
			//die(SUtil::P($ggposition_results));
			//获取一级分组
			$parentArr = $ggmanage_db->getGroupList();
			
			//die(SUtil::P($ggposition_results));
			$tplArr = array(
						
					'list'=>$ggposition_results['list'],
					'pagehtml'=>$pageShow,//分页字符
					'parentArr'=>$parentArr['list']
						
			);
			$this->ass('row', $questArr);
			return $this->template($tplArr);

			
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
		
		
		
		
	}