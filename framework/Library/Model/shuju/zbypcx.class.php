<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_shuju_zbypcx extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'data_zbyaopinchaxun'; //设置表名
        
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
	    	   	 if($name=$srow['name']){
	    	   		$condition.=" AND `name` like '%$name%'";
	    	   	 }
	    	   	 if($shengshi=$srow['shengshi']){
	    	   		$condition.=" AND `shengshi` like '%$shengshi%'";
	    	   	 }
	    	  
	    		    		
    	     }
    	
    	      
      return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 20, true);
    		
     }
    
   
     
    
}