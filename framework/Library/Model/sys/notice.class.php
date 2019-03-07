<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_sys_notice extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'sn_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'sys_notices'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    /**
     * 添加
     * @param unknown $data
     * @return Ambigous <number, false, boolean, multitype:>
     */
    public function add($data){
    	
    	return $this->insert($data);
    	
    }
    
    /**
     * 获取列表数据
     * @param string $page
     * @param string $seek
     * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
     */
    public function getListAll($page='',$seek=''){
    	
    	//条件
    	$condition = 'is_del!=1';    	
    	    	    	
    	if (is_array($seek)){
    		
			if($dateline = strtotime($seek['dateline'])){
				$condition .= " AND dateline >={$dateline} and dateline <= ".($dateline + 86400);
			}
			
			if($seek['sn_id']=intval($seek['sn_id'])){
				$condition .= " AND sn_id = ".$seek['sn_id'];
			}
    		
    		return $this->getList($condition, '*', array('sn_id'=>'DESC'), $page, 10, true);
    		
    	}else{
    		
    		return $this->getList($condition, '*', array('sn_id'=>'DESC'), $page, 10, true);
    		
    	}
    	
    	
    	
    }
    /**
     * 获取列表数据
     * @param string $page
     * @param string $seek
     * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
     */
    public function getListAllnotice($page){
    	 
    	//条件
    	$condition = 'is_del!=1';
    	     	  
    	return $this->getList($condition, '*', array('sn_id'=>'DESC'), $page, 10, true);
    
    	 
    }
    /**
     * 获取列表数据
     * @param string $page
     * @param string $seek
     * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
     */
    public function getListone($page){
    
    	//条件
    	$condition = 'is_del!=1';
    	 
    	return $this->getList($condition, '*', array('dateline'=>'DESC'), $page, 1, true);
    
    
    }
    
    public function getEdit($id){
    	
    	
    	if(!$id=intval($id)) return 0;
    	//条件
    	$condition = $this->_PK.'='.$id;
    	
    	return $this->getOne($condition);
    	
    }
    
    
    public function doEdit($id,$data){
    	
    	if(!$id=intval($id)) return 0;
    	//条件
    	$condition = $this->_PK.'='.$id;
    	
    	return $this->update($condition, $data);
    	
    }
    
    /**
     * 删除处理方法
     */
    public function del($data){
    
    	if ( is_array($data) ){//多选
    		 
    		$delStr = implode($data, ',');
    		//die($delStr);
    		return $this->update($this->_PK." in (".$delStr.")", 'is_del=1');
    		 
    	}else{//单选
    		 
    		return $this->update($this->_PK."=".$data, "is_del=1");
    		 
    	}
    
    }
   
    
}