<?php
class Model_search_searchkeywords extends Base_model{
	//@override
	public function setPrimaryKey() {
		$this->_PK = 'kw_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'search_keywords';

	}
	public function setCache($status=false){
		$this->_cache = $status;
	}
	
	/**
	 * 获取热门搜索关键字
	 */
	public function hotKey(){
		return $this->getList("is_del!=1 and ss_state!=2", array('kw_name'), array('sk_count'=>'DESC'), 0, 10);
	}
}
/**
 End file,Don't add ?> after this.
 */