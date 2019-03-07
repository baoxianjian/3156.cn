<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_shuju_xyotc extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'data_drug_west_otc'; //设置表名
        
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
	    	   	 if($drugname=trim($srow['drugname'])){
	    	   		$condition.=" AND `drugname` like '%$drugname%'";
	    	   	 }
	    	   	 if($drugname_en=trim($srow['drugname_en'])){
	    	   	 	$condition.=" AND `drugname_en` like '%$drugname_en%'";
	    	   	 }
	    	   	 if($drug_class=trim($srow['drug_class'])){
	    	   		$condition.=" AND `drug_class` ='$drug_class'";
	    	   	 }
	    	   	 if($drug_type=trim($srow['drug_type'])){
	    	   		$condition.=" AND `drug_type` ='$drug_type'";
	    	   	 }
	    		    		
    	     }
    	
    	      
      return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 20, true);
    		
     }
    
   
     
    
}