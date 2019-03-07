<?php
/**
* @name: 统计管理 
* @author: wukai
* @date: 10:32 2015/4/20
*/        

class Controller_count extends Controller_basepage {
    
	//列表页
	public function pageAdlist($inPath){
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
	
	public function pageClick($inPath){
		$page = $this->getPageNumber($inPath);//获取分页参数
		$mdl_count = new Model_count_count();
		$limit = 30;
		$list = $mdl_count->getListAll($page, array('type'=>1), $limit);
		$count = $mdl_count->count(array('type'=>1));
		$param['list'] = $list['list'];
		$param['pagehtml'] = $this->pageBar($count, $page, $inPath, $limit);
		return $this->template($param);
	}
	
	public function pageClickshow($inPath){
		$page = $this->getPageNumber($inPath);//获取分页参数
		$starttime = intval($this->request['stime']);
		$endtime = intval($this->request['etime']);
		$mdl_count = new Model_mongo_count();
		$condition = array('addtime'=>array('$gte'=>$starttime, '$lte'=>$endtime));
		$limit = 30;
		$list = $mdl_count->getListAll($page, $condition, $limit);
		$count = $mdl_count->count($condition);
		$param['list'] = $list;
		$param['pagehtml'] = $this->pageBar($count, $page, $inPath, $limit);
		return $this->template($param);
	}
	
	public function pageTcount(){
		$id = $this->request['id'];
		$mdl_countad = new Model_count_ad();
		$counts = $mdl_countad->count(array('position_id'=>$id));
		$mdl_ggposition = new Model_ggposition_ggposition();
		$mdl_ggposition->edit_add(array('click_count'=>$counts), $id);
		$jsondata = array('msg'=>'统计成功', 'data'=>$counts, 'status'=>true);
		echo SUtil::getJson($jsondata);
		exit();
	}
}
