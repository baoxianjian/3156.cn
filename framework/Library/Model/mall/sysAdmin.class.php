<?php
class Model_mall_sysAdmin extends Base_model{
	//@override
	public function setPrimaryKey() {
		$this->_PK = 'adm_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'sys_admins';

	}
	public function setCache($status=true){
		$this->_cache = $status;
	}
}
/**
 End file,Don't add ?> after this.
 */