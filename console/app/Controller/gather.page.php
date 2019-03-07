<?php
/**
* @name: 资讯
* @author: wukai
* @date: 08:32 2015/4/13
*/        

class Controller_gather extends Controller_basepage {
    
	//列表页
	function pageList($inPath){
        #得到当前页码
        $page=$this->getPageNumber();
		#实例化 分类名称 数据模型
		$mdl_article_news = new Model_news_gather();
		$mdl_arttype = new Model_news_type();
		$mdl_article_news->setCache(false);


		#搜索
		$srow=$this->request;
		$condition = 'is_del = 0';
		
		if(!empty($srow['is_del'])){
			$condition = 'is_del = '.$srow['is_del'];
		}
		if(!empty($srow['type_id2'])){
			$typedatas = $mdl_arttype->getRowByOne(array('name'=>$srow['type_id2']));
			$condition .= ' and type_id2 = '.$typedatas['nt_id'];
		}
		if(!empty($srow['keyword'])){
			$condition .= ' and keyword like "%'.$srow['keyword'].'%"';
		}
		
		if(!empty($srow['com_id'])){
			$condition .= ' and com_id = '.$srow['com_id'];
		}
		
		if($srow['stime']){
			$condition .= ' and dateline >= '.strtotime($srow['stime']);
		}
		
		if($srow['etime']){
			$etime = strtotime($srow['etime'])+86399;
			$condition .= ' and dateline <= '.$etime;
		}
		
		if($srow['author']){
			$condition .= ' and author like "%'.$srow['author'].'%"';
		}
		
		if($srow['is_html']){
			switch ($srow['is_html']){
				case 1:
					$condition .= ' and audit_state = 1 and is_html = 0';
					break;
				case 2:
					$condition .= ' and audit_state = 2 and is_html = 0';
					break;
				case 3:
					$condition .= ' and audit_state = 2 and is_html = 1';
					break;
				case 4:
					$condition .= ' and audit_state = 3';
					break;
			}
		}
		
		if($condition == '1'){
			$condition = array('is_del'=>0);
		}
		
		#得到搜索列表
		$data = $mdl_article_news->getListAll($page, $condition);
		$data['count'] = $mdl_article_news->count($condition);
		#数据临时处理
		$list=$data['list'];
		foreach($list as $key=> $value){
			$type_data = $mdl_arttype->getRowById($value['type_id2']);
			$list[$key]['type_name'] = $type_data['name'];
			$list[$key]['del_url'] = $url=SRoute::createUrl('article/del', array('page' => $page,'id'=>$value['news_id']), $this->request);
		}
		#设置好要放入模版的数据（变量）
		$param[LIST_VAR_NAME] = $list;
		$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);

	
		
		$param['srow']=$srow;
		$param['is_del']=$srow['is_del'];
        return $this->template($param);
	}
	
	
	/**
    * 删除
    * 
    */
	function pageDel(){
		$mdl_article_gather = new Model_news_gather();
		$id=$this->request['id'];
		$ids=$this->request['check'];
		$status = $this->request['status'];
		if(is_array($ids)){
			if($status == 2){
				$msg = '审核';
			}else if($status == 3){
				$msg = '驳回';
			}
			$mdl_article_gather->deleteRowById($ids, array("audit_state"=>$status)) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>$msg.'成功'))) : SUtil::success($msg.'成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>$msg.'失败'))) : SUtil::error($msg.'失败') );
		}else{
			$result=$mdl_article_gather->deleteRowById($id);
			if($result){
				$this->showMessage('删除成功',1,'_reload');
			}else{
				$this->showMessage('删除失败',3);
			}
		}
		return $this->template();
    }
}
