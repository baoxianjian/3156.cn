<?php
 /**
* @name: 药器械标据模型 
* @author: sunyouhong
* @date: 14:20 2015/6/15
*/

class Model_shuju_yaoqxfl extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'data_yaoqxfl'; //设置表名
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
       
    /**
     * 获取药器械分类列表数据
     * @param string $page
     * @param string $seek
     * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
     */
    public function getListAll($page,$srow){    	
    	//条件
    		$condition =1;  
    	    if(count($srow)>0)
    	      {
    	      	 //医疗机械名称
				  if($ba_code=trim($srow['ba_code'])){
	    	   		$condition.=" AND `ba_code` like '%{$ba_code}%'";
	    	   	 }
				
				//分类名称
    	      	 if($names=trim($srow['names'])){
	    	   		$condition.=" AND `names` like '%{$names}%'";
	    	   	 }
				  
				  //分类编号
	    	   	 if($confirmcode=trim($srow['confirmcode'])){
	    	   		$condition.=" AND `confirmcode` like '%{$confirmcode}%'";
	    	   	 }
    	     }

	      return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 20, true);
     }
    
   
     
    
}