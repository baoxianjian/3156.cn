<?php
 /**
* @name: 症状关联疾病
* @author: wukai
* @date: 12:03 2015/4/13
*/
class Model_ill_symptomfordisease extends Base_model{
	public function setPrimaryKey() {
        $this->_PK = '';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'ill_symptomfordisease'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
	public function getListAll($condition, $files = array('*')){
		return $this->getList($condition, $files);
	}
}