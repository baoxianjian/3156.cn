<?php
class Model_mall_keywordDel extends Base_model{
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


}