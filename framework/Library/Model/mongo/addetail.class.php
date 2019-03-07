<?php
/**
* 统计广告每日明细
*/

class Model_mongo_addetail extends Base_mmodel{
    //@override
    public function setPrimaryKey() {
        $this->_PK = '_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'addetail'; //设置表名
        
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
    public function getListAll($page, $srow, $limit=10){
        
        $condition = $srow; //查询条件
        return $this->getList($condition, array(), array('_id'=>'-1'), $page, $limit);
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
	
	public function getRowBysRow($srow){
        if(!$srow){return false;}
        return $this->getOne($srow);
    }
	
	public function updateRow($data, $condition)
    {
        if(!$data) return false;
		if(!$condition) return false;
        return $this->update($condition, $data);
    }
	
	
	public function getGroup($condition, $initial = 'ip'){
		$key = array('ip'=>true);   //$key没有指定分组依据，那么所有文档认为属于同一组
		$initial = array("count"=>0, $this->_PK => array(), $initial => array());
		$reduce = "function (obj, prev) {
    	prev.count++;
    	prev._id.push(obj._id);
    	prev.ip.push(obj.ip);
		}";
        return $this->group($condition, $initial, $key, $reduce);
    }
}