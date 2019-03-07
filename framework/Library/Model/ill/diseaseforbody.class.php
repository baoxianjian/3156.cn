<?php
 /**
* @name: 疾病关联部位 
* @author: wukai
* @date: 12:03 2015/4/13
*/
class Model_ill_diseaseforbody extends Base_model{
	public function setPrimaryKey() {
        $this->_PK = '';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'ill_diseaseforbody'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
	public function getListAll($condition, $files = array('*')){
		return $this->getList($condition, $files);
	}
}