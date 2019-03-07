<?php
 /**
* @name: 关键字管理的数据模型 
* @author: zhanghao
* @date: 17:37 2015/4/13
*/
class Model_user_agency extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'user_id';
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'user_users';
        
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
       	// $condition='1';
    if(count($srow)>0)
        {   
        	if($user_id=intval($srow['user_id']))
        	{
        	$condition.=" AND `user_id` = $user_id";
        	}
        	if($user_name=trim($srow['user_name']))
        	{
        		$condition.=" AND `user_name` = '$user_name'";
        	}
        	if($link_man=trim($srow['link_man']))
        	{
        		$condition.=" AND `link_man` = '$link_man'";
        	}
        	if($mobile=trim($srow['mobile']))
        	{
        		$condition.=" AND `mobile` = $mobile";
        	}
        	
        }    
       return $this->getList($condition,array('*'),array('user_id'=>'desc'),$page,$limit=30,true);
       
        
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
    public function getRowByName($name)
    {
        if(!$name){return false;}
        return $this->getone("kw_name='$name'");
    }
    
  
    
}
/**
    End file,Don't add ?> after this.
*/