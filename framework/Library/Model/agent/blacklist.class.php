<?php
 /**
* @name: 关键字管理的数据模型 
* @author: zhanghao
* @date: 17:37 2015/4/13
*/
class Model_agent_blacklist extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'abl_id';
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'agent_blacklist';
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    /**
    * 得到关键字列表所有
    * 
    */
    public function getListAll($page,$srow)
     { 
       	 $condition ="`is_del`=0"; //查询条件      
       	 if(count($srow)>0)
       	 {       	 	
       	 	if($tel=intval($srow['tel']))
       	 	{
       	 		$condition.=" AND `tel` = $tel";
       	 	}
       	 
       	 }   
       return $this->getList($condition,array('*'),array('abl_id'=>'desc'),$page,$limit=10,true);
       
        
    }
    
    /**
     * 根据主键id删除一行数据
     *
     * @param int $id
     * @return int|false
     */
    
    public function deleteRowById($id,$destroy=false)
    {
    	if(!$id=intval($id)) return false;
    
    	return $this->update(array($this->_PK=>$id), array('is_del=1'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    
    /**
     * 添加
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
    * 根据关键字名称得到一条数据
    * 
    * @param mixed $name
    * @return array|false
    */
    public function getRowByTel($tel)
    {
        if(!$tel){return false;}
        $tel=trim($tel);
        return $this->getone("tel='$tel'");
    }
    
   
    
    
}
/**
    End file,Don't add ?> after this.
*/