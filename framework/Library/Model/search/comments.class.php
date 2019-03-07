<?php
 /**
* @name: 关键字管理的数据模型 
* @author: zhanghao
* @date: 11:08 2015/3/22
*/
class Model_search_comments extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'cmt_id';
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'cmt_comments';
        
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
        	if($online_state=intval($srow['online_state']))
        	{
        	$condition.=" AND `online_state` = $online_state";
        	}
        	if($dateline=trim($srow['dateline']))
        	{
        		$condition.=" AND `dateline` = $dateline";
        	}
        	if($pdt_id=intval($srow['pdt_id']))
        	{
        		$condition.=" AND `pdt_id` = $pdt_id";
        	}
        	if($user_id=intval($srow['user_id']))
        	{
        		$condition.=" AND `user_id` = $user_id";
        	}
        	if($areas=trim($srow['areas']))
        	{
        		$condition.=" AND `areas` = '$areas'";
        	}
        	if($company=trim($srow['company']))
        	{
        		$condition.=" AND `company` = '$company'";
        	}
        	if($link_man=trim($srow['link_man']))
        	{
        		$condition.=" AND `link_man` = '$link_man'";
        	}
        
        }
 /*       
        $this->_tableName='cmt_product_agent cpa';
        //$leftjoin="left join search_ggs sg ON sgp.sgp_id=sg.sgp_id ";
        $leftjoin=array('cmt_comments cc'=>'cc.cmt_id=cpa.cmt_id');
        $data=$this->getList($condition,"cc.*,cpa.pdt_id",array('cmt_id'=>'desc'),$page,10,true,$leftjoin);
        $this->_tableName='cmt_comments';
        return $data;
 */       
       return $this->getList($condition,array('*'),array('cmt_id'=>'desc'),$page,$limit=10,true);
       
        
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