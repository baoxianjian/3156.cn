<?php
 /**
* @name: 关键字管理的数据模型 
* @author: zhanghao
* @date: 17:37 2015/4/13
*/
class Model_sys_friendlinks extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'sfl_id';
    }
    //@override
    public function setTableName() {             
        $this->_tableName = 'sys_friendlinks';
        
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
       	 $condition ="is_del=0"; //查询条件
   
	    if(count($srow)>0)
	        {   	        	
	        	if($sitepage=trim($srow['sitepage']))
	        	{
	        		$condition.=" AND `sitepage` = '$sitepage'";
	        	}
	        	if($show_way=intval($srow['show_way']))
	        	{
	        		$condition.=" AND `show_way` = '$show_way'";
	        	}	        
	        } 
       return $this->getList($condition,array('*'),array('sfl_id'=>'desc'),$page,$limit=10,true);
       
        
    }
    
 
    /**
     * 根据主键id删除一行数据
     *
     * @param int $id
     * @return int|false
     */
    
    public function deleteRowById($id,$destroy=false)
    {
    	if(!$id=intval($id)) return 0;
    
    	return $this->update(array($this->_PK=>$id), array('is_del=1'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    /**
     * 根据主键id删除多行数据
     *
     * @param int $id
     * @return int|false
     */
    
    /**
    * 批量删除记录,根据id数组或id字符串(1,2,3)
    * 
    * @param string $ids
    * @param mixed $destroy
    * @return int|false
    */
    public function deleteRowByIds($ids,$destroy=false)
    {
	    $id_list=$ids;
    	if(!is_array($ids))
    	{
    		$id_list=explode(',', $ids);
    	}
    	foreach ($id_list as $k=>$v)
    	{
    		$id_list[$k]=intval($v);
    	}
    	$ids=implode(',', $id_list);

    	if(!$ids){return false;}
    	return $this->update($this->_PK." in ($ids)", array('is_del=1'));
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
    public function getRowByName($name)
    {
        if(!trim($name)){return false;}
        return $this->getone("kw_name='$name'");
    }
    
  
    
    
    
}
/**
    End file,Don't add ?> after this.
*/