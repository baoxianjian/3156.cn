<?php
 /**
* @name: product模型 
* @author: sunyouhong
* @date: 10:38 2015/6/15
*/
class Model_shuju_yaonewproduct extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'data_yaonewproduct'; //设置表名
        
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
	    	   	 if($names=trim($srow['names'])){
	    	   		$condition.=" AND `names` like '%{$names}%'";
	    	   	 }
				 if($englishnames=trim($srow['englishnames'])){
	    	   		$condition.=" AND `englishnames` like '%{$englishnames}%'";
	    	   	 }
				 if($ytype=trim($srow['ytype'])){
	    	   		$condition.=" AND `ytype` = '{$ytype}'";
	    	   	 }
	    	   	 if($agenttype=trim($srow['agenttype'])){
	    	   		$condition.=" AND `agenttype` like '%{$agenttype}%'";
	    	   	 }
				 
    	     }
      return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 20, true);
    		
     }
    
   
     
    
}