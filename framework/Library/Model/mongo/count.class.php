<?php
/**
* 统计表
*/

class Model_mongo_count extends Base_mmodel{
    //@override
    public function setPrimaryKey() {
        $this->_PK = '_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'count'; //设置表名
        
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
	
	public function updateRow($data, $condition)
    {
        if(!$data) return false;
		if(!$condition) return false;
        return $this->update($condition, $data);
    }
	
	public function getRowBysRow($srow){
        if(!$srow){return false;}
        return $this->getOne($srow);
    }
    
    /**
    * 得到列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page, $srow, $limit){
        
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
	
	public function getSiteid(){
		$data = array(
			1 => array('id'=>1101, 'name'=>'产品列表页'),
			2 => array('id'=>1102, 'name'=>'产品详情页'),
			3 => array('id'=>1201, 'name'=>'医药资讯首页'),
			4 => array('id'=>1202, 'name'=>'资讯列表页'),
			5 => array('id'=>1203, 'name'=>'资讯详情页'),
			6 => array('id'=>1301, 'name'=>'公司介绍页'),
			7 => array('id'=>1302, 'name'=>'产品展示页'),
			8 => array('id'=>1303, 'name'=>'企业资讯页'),
			8 => array('id'=>1401, 'name'=>'公司招商主页'),
			8 => array('id'=>1501, 'name'=>'留言列表页'),
			8 => array('id'=>1502, 'name'=>'留言详情页'),
			8 => array('id'=>1503, 'name'=>'发布代理'),
		);
		return $data;
	}
}