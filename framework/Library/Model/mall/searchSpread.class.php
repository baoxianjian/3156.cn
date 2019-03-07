<?php
class Model_mall_searchSpread extends Base_model{
	//@override
	public function setPrimaryKey() {
		$this->_PK = 'ss_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'search_spread';

	}
	public function setCache($status=false){
		$this->_cache = $status;
	}
	
	public function getListAll($page){
		return $this->getList("is_del!=1",array('ss_id','x___title','ss_type','keywords','start_time','end_time','`order`','recommend','ss_state','dateline'), array('order'), $page, 10, true);//获取未删除数据
	}
	
	public function getOne_Edit(){
		return $this->getOne("ss_id=".htmlspecialchars($_GET['id'],ENT_QUOTES),array('cmp_id','ss_id','pdt_id','keywords','dateline','start_time','end_time','`order`','recommend','ss_state','ss_type','x__img_url'));
	}
	
}
/**
 End file,Don't add ?> after this.
 */