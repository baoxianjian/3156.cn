<?php
 /**
* @name: 资讯的数据模型 
* @author: wukai
* @date: 12:03 2015/4/13
*/
class Model_news_type extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'nt_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'news_type'; //设置表名
        
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
        return $this->getList($condition, array('*'), array('order'=>'desc'),$page);
    }
    
    /**
     * 
     * 得到所有健康资讯列表
     * @param mixed $page
     * @return array|false
     */
    public function getListAlljiankang($page,$num)
    {
    	$condition ="`parent_id`=3"; //查询条件
    	return $this->getList($condition, array('*'), array('order'=>'desc'),$page);
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
    }
    

    /**
    * 添加一个用户 
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
    
    public function getRowByOne($row)
    {
        if(!$row){return false;}
        return $this->getOne($row);
    }
	
	public function getRowByEn($en, $id)
    {
        if(empty($en)){return false;}
		if($id){
			return $this->getOne('en_name = '.$en.' and nt_id !='.$id);
		}else{
        	return $this->getOne(array('en_name'=>$en));
		}
    }
    
    /**
    * 得到新闻分类数据集
    * add by baoxianjian 20:59 2015/4/29
    * @param array $srow pid为父级ID(0为顶级)
    * @return array
    */
    public function getRowsetAll($srow=array())
    {
        $condition='is_del=0';
        $pid=intval($srow['pid']);
        $condition.=" AND parent_id={$pid} ";
        
        $fields="nt_id,`name`";
        $data=$this->getList($condition,$fields,' ORDER BY nt_id ASC',1,1000,0); 
        return $data['list']; 
    }
    
}