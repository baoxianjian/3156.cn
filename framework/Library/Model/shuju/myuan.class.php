<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_shuju_myuan extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'hid';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'data_hospital'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
       
    /**
     * 获取药包材生产企业列表数据
     * @param string $page
     * @param string $seek
     * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
     */
    public function getListAll($page,$srow){    	
    	//条件
    	$condition =1;  
    	    	
    	    if(count($srow)>0)
    	      {
	    	   	 if($hospitalname=trim($srow['hospitalname'])){
	    	   		$condition.=" AND `hospitalname` like '%$hospitalname%'";
	    	   	 }
	    	   	 if($city=trim($srow['city'])){
	    	   	 	$condition.=" AND `city` = '$city'";
	    	   	 }
	    	   	     		    		
    	     }    	   	      
      return $this->getList($condition,array('*'), array('hid'=>'DESC'), $page, 20, true);
    		
     }
    
   
     
    
}