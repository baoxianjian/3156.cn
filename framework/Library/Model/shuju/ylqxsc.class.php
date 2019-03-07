<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_shuju_ylqxsc extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'data_yaopinall'; //设置表名
        
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
    	$condition ="`comptype`='医疗器械生产企业'"; 
    	    	
    	    if(count($srow)>0)
    	      {
	    	   	 if($compname=trim($srow['compname'])){
	    	   		$condition.=" AND `compname` like '%$compname%'";
	    	   	 }
	    	   	 if($ck_address=trim($srow['ck_address'])){
	    	   		$condition.=" AND `ck_address` like '%$ck_address%'";
	    	   	 }
	    	   	 if($businessscope=trim($srow['businessscope'])){
	    	   		$condition.=" AND `businessscope` like '%$businessscope%'";
	    	   	 }
	    		    		
    	     }
    	
    	      
      return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 20, true);
    		
     }
    
   
     
    
}