<?php
 /**
* @name: 用户的数据模型 
* @author: makamuguo
* @date: 12:03 2015/3/29
*/
class Model_shuju_jkypsjk extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'data_yaopingjjk'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
       
    /**
     * 获取进口生产企业列表数据
     * @param string $page
     * @param string $seek
     * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
     */
    public function getListAll($page,$srow){    	
    	//条件
    	$condition ="`type`='进口药品数据库'";  
    	    	
    	    if(count($srow)>0)
    	      {
	    	   	 if($name=trim($srow['names'])){
	    	   		$condition.=" AND `names` like '%$name%'";
	    	   	 }
	    	   	 if($company=trim($srow['company'])){
	    	   		$condition.=" AND `company` like '%$company%'";
	    	   	 }	    		
    	     }   	      
      return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 20, true);
    		
     }   
}