<?php
 /**
* @name: 站内留言信息管理的数据模型 
* @author: zhanghao
* @date: 10:19 2015/4/9
*/
class Model_stationinforma_stationinforma extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'ae_id';
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'agent_inquiries';
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
        
    /**
     * 站内来电信息 查询
     *
     */
    public function getListAll($page,$srow)
    {
    	$condition ="`is_del`=0"; //查询条件
    	//  print_r($srow);
    	if(count($srow)>0)
    	{
    		if($srow['start_time']!="" && $srow['end_time']!="")
    		{    			
    			$start_time=strtotime($srow['start_time']);
        		$end_time=strtotime($srow['end_time']); //2015-1-10
        		$condition.=" AND `dateline`>=$start_time and `dateline`<=$end_time";
    		}    		    		     	
    	}
    
    	return $this->getList($condition,array('*'),array('ae_id'=>'desc'),$page,$limit=10,true);
    	//  return $data;
    
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