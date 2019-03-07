<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_shuju_ypglp extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'data_yaopinglp'; //设置表名
        
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
	    	   	 if($name=trim($srow['name'])){
	    	   		$condition.=" AND `name` like '%$name%'";
	    	   	 }
	    	   	 if($syxm=trim($srow['syxm'])){
	    	   		$condition.=" AND `syxm` like '%$syxm%'";
	    	   	 }
	    	   	 if($gonggaohao=trim($srow['gonggaohao'])){
	    	   	 	$condition.=" AND `gonggaohao` like '%$gonggaohao%'";
	    	   	 }
	    	  
	    		    		
    	     }
    	
    	      
      return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 6, true);
    		
     }
    
   
     
    
}