<?php
/**
* @name: 关键词管理
* @author: wukai
* @date: 08:32 2015/4/13
*/        

class Controller_keys extends Controller_basepage {
    
	//列表页
	function pageList($inPath){
        #得到当前页码
		$page=$this->getPageNumber();

		#实例化 分类名称 数据模型
		$mdl_artkeys = new Model_news_keys();
		$mdl_artkeys->setCache(false);


		#搜索
		$srow['name']=$this->request['name'];
		if(!empty($srow['name'])){
			$condition = 'is_del = 0';
			if(!empty($srow['name'])){
				$condition .= ' and key_name like "%'.$srow['name'].'%"';
			}
		}else{
			$condition = array('is_del'=>0);
		}
		
		#得到搜索列表
		$data = $mdl_artkeys->getListAll($page, $condition);
		$data['count'] = $mdl_artkeys->count($condition);
		#数据临时处理
		$list=$data['list'];
		foreach($list as $key=>$value){
			$list[$key]['dateline'] = SUtil::formatTime($value['dateline']);
			$list[$key]['del_url'] = $url=SRoute::createUrl('keys/del', array('page' => $page,'id'=>$value['key_id']), $this->request);
		}
		#设置好要放入模版的数据（变量）
		$param[LIST_VAR_NAME] = $list;
		$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);

		$param['srow']=$srow;
        return $this->template($param);
	}
    
    //编辑 增加
	function pageEdit(){
		$id=intval($this->request['id']);
		$mdl_artkeys = new Model_news_keys();
		//如果有保存请求
		if($this->request['_ac']=='save'){
			$row = $this->request['row'];
			$passed = true;    
			if($id && $passed){
				//修改
				$this->ass('TIP_NAME','修改');
				$result=$mdl_artkeys->updateRowById($row,$id);
				if($result){      
					$this->showMessage('修改成功');
				}else{
					 $this->showMessage('修改失败',3); 
				}
			}else if($passed){
				//添加
				$this->ass('TIP_NAME','添加');
				if(empty($row['key_name'])){
						$this->showMessage('名称不能为空',3);
				}else if(empty($row['key_name'])){
						$this->showMessage('链接地址不能为空',3);
				}else if(empty($row['key_num'])){
						$this->showMessage('频率不能为空',3);
				}
				$row['dateline']=NOW;
				$result=$mdl_artkeys->addRow($row); 

				if($result){
					$this->showMessage('添加成功'); 
				}else{
					$this->showMessage('添加失败'); 
				}
			}   
		}else{
			if($id){
				//修改初始化
				$row=$mdl_artkeys->getRowById($id);
				if($savetype != "sub"){
					$this->ass('TIP_NAME','修改');
					$this->ass('AC_NAME','modify');
				}

			}else{
				//添加初始化
				$this->ass('TIP_NAME','添加');
				$this->ass('AC_NAME','add');

			}  
		}
		if($row){
			$this->ass('row',$row);
		}


		$this->ass('id', $id);
        return $this->template();
	}
	
	/**
    * 删除
    * 
    */
	function pageDel(){
		$mdl_artkeys = new Model_news_keys();
		$id=$this->request['id'];
		$ids=$this->request['check'];
		$status = $this->request['status'];
		if(is_array($ids)){
			if($status == 1){
				$msg = '删除';
			}else if($status == 0){
				$msg = '恢复';
			}
			//删除
			$mdl_artkeys->deleteRowById($ids, array('is_del'=>$status)) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>$msg.'成功'))) : SUtil::success($msg.'成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>$msg.'失败'))) : SUtil::error($msg.'失败') );
		}else{
			$result=$mdl_artkeys->deleteRowById($ids);
			if($result){
				$this->showMessage('删除成功',1,'_reload');
			}else{
				$this->showMessage('删除失败',3);
			}
		}
		return $this->template();
    }
}
