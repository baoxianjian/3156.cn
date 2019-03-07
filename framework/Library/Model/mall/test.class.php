<?php
class Model_mall_test extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'pdt_products';
        
    }
   	public function setCache($status=true){
    	$this->_cache = $status;
    }
}
/**
    End file,Don't add ?> after this.
*/