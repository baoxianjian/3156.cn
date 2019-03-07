<?php

class Model_mall_admanage extends Base_model{
	//@override
	public function setPrimaryKey() {
		$this->_PK = 'ggpg_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'gg_position_group';

	}
	public function setCache($status=false){
		$this->_cache = $status;
	}


}
/**
 End file,Don't add ?> after this.
 */