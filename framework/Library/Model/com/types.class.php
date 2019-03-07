<?php
 /**
* @name: 所有模块里的类型集合的数据模型 
* @author: baoxianjian
* @date: 22:20 2015/4/26
*/
class Model_block_templates extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'bt_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'block_templates'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    

    
    
    
    
    
    
    
}