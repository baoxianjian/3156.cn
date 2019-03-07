<?php
/**
* 统计表
*/

class Model_mongo_comments extends Base_mmodel{
    //@override
    public function setPrimaryKey() {
        $this->_PK = '_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'comments'; //设置表名
        
    }
    
    //@override
    public function setDataBase() {
        $this->_dataBase = '3156db'; //设置默认库
        
    }
    
    //@override
    public function setCache($status=false){
    	$this->_cache = $status;
    }
	
	public function count($condition){
        return $this->getCount($condition);
    }
    
    /**
    * 得到列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page, $srow){
        
        $condition = $srow; //查询条件
        return $this->getList($condition, array(), array('_id'=>'-1'), $page);
    }
    
    /**
    * 添加一个用户 
    * 
    * @param array $row['id']:来源ID，$row['type']:1(广告),2,3,4
    * @return int|false
    */
    public function addRow($row){
        if(!$row) return false;
        $row['id'] = intval($row['id']);
        return $this->insert($row);
        //return $this->delete(array($this->_PK=>$id));
    }
    
    /**
    * 得到一条记录（根据id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getRowById($id){
        if(!$id=intval($id)){return false;}
        return $this->getOne(array($this->_PK=>$id));
    }
}