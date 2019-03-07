<?php
 /**
* @name: 站内留言信息管理的数据模型 
* @author: zhanghao
* @date: 10:19 2015/4/9
*/
class Model_stationinfo_stationinfo extends Base_model{
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
    	$condition ="is_del=0"; //查询条件
    	//  print_r($srow);
    	if(count($srow)>0)
    	{
    		if($dateline=strtotime($srow['dateline']))
    		{		
    			$condition.=" AND dateline >={$dateline} and dateline <= ".($dateline + 86400);
    		}
    		if($contact=trim($srow['contact']))
    		{
    			$condition.=" AND contact='{$contact}'";    			 
    		}
    		     	
    	}
    
    	return $this->getList($condition,array('*'),array('ae_id'=>'desc'),$page,$limit=20,true);
    	//  return $data;
    
    }
 
    /**
     * 得到
     *
     * @param mixed $page
     * @return array|false
     */
 /*   public function getRowsetAdsByPage($page)
    {
    	
    	$this->_tableName='cmt_comments cc';
    	$condition="cc.`is_del`='0'";
    			// $leftjoin=array('search_ggs sg'=>'sgp.sg_id=sg.sg_id'); //
    
    	$leftjoin='LEFT JOIN cmt_product_agent cpa ON cc.mod_id=cpa.pdt_id';
        $data=$this->getList($condition,"cpa.*",' ORDER BY  cc.cmt_id desc',1,10,true,$leftjoin);
            		// $this->_tableName='search_ggs';
    
    	return $data; 
    
    	
    	/*
    	$condition = array('is_del'=>'0','sgp_page'=>$page); //查询条件
    
    	return $this->getList($condition,array('*'),array('sgp_page'=>'asc','sgp_area'=>'asc','order'=>'asc'),$page,0,false);
    	
    	}    
              */
  
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