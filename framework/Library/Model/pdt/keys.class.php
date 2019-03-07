<?php
 /**
* @name: 关键词数据模型 
* @author: wukai
* @date: 12:03 2015/4/13
*/
class Model_pdt_keys extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'pdt_products_keys'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    
    /**
    * 获取列表数量
    * 
    * @return array|false
    */
    public function count($srow){
        
        $condition = $srow; //查询条件
        return $this->getCount($condition);
    }
	
	/**
    * 得到列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page,$srow)
    {
        
        $condition = $srow; //查询条件
        return $this->getList($condition,array('*'),array('id'=>'desc'),$page);
    }
    
    
    /**
    * 根据主键id删除一行数据
    * 
    * @param int $id
    * @return int|false
    */
    public function deleteRowById($id, $row = array()){
		if (is_array($id) ){//多选
			$delStr = implode($id, ',');
			return $this->update($this->_PK." in (".$delStr.")", $row);
		}else{
			if(!$id=intval($id)) return 0;
			return $this->update(array($this->_PK=>$id), array('is_del=1'));
		}
        //return $this->delete(array($this->_PK=>$id));
    }
    

    /**
    * 添加一个数据
    * 
    * @param array $row
    * @return int|false
    */
    public function addRow($row)
    {
        if(!$row) return false;
        return $this->insert($row);
        //return $this->delete(array($this->_PK=>$id));
    }
    
    /**
    * 修改一条记录（根据Id）
    * 
    * @param array $row
    * @param int $id
    * @return int|false
    */
    public function updateRowById($row,$id=0)
    {
        if(!$row) return false;
        if(!$id=intval($id)){return false;}

        unset($row[$this->_PK]);
        return $this->update(array($this->_PK=>$id),$row);
        //return $this->delete(array($this->_PK=>$id));
    }
    

	
    /**
    * 得到一条记录（根据id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getRowByName($name)
    {
        if(!$name){return false;}
        return $this->getOne(array('name'=>$name));
    }
}