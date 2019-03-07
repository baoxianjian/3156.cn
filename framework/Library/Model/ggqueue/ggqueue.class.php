<?php

	/**
	 * 规格Model
	 * @author Administrator
	 *
	 */

	class Model_ggqueue_ggqueue extends Base_model{
	    //@override
	    public function setPrimaryKey() {
	        $this->_primaryKey = 'ggq_id';
	    }
	    //@override
	    public function setTableName() {
	        $this->_tableName = 'gg_queue';
	        
	    }
	   	public function setCache($status=true){
	    	$this->_cache = $status;
	    }
	    
	    public function validGposition($startTime=''){
	    	
	    	//die("start_time<".$startTime." and end_time>".$endTime." and is_del!=1");
	    	if ( $startTime != NULL ){
	    	
	    		return $this->getList("end_time>".$startTime." and is_del!=1",'ggp_id,ggq_id');
	    		
	    	}else{
	    		
    			$startTime = time();
    			return $this->getList("end_time>".$startTime." and is_del!=1",'ggp_id,ggq_id');
	    		
	    	}
	    	
	    	
	    }
    	
	    /**
	     * 获取广告单列表
	     * @param unknown $page
	     * @param string $seek
	     * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
	     */
	    public function getListAll($page,$seek=''){
	    	
	    	$this->_tableName='gg_queue as GQ';
	    	$leftjoin=',gg_materials as GM,gg_position as GP,cmp_company as CMP';
	    	
	    	//条件
	    	$condition = "GQ.ggp_id=GP.ggp_id and (GQ.cmp_id=GM.mod_id and GQ.ggm_id=GM.ggm_id) and GQ.is_del!=1 and GM.mod_id=CMP.cmp_id";
	    	
	    	//字段
	    	$field = "GQ.ggq_id,GQ.contractNo_id,GQ.start_time,GQ.end_time,GQ.audit_status,GP.title,GP.ggp_id,GP.gg_sale_state,GM.mod_id,GM.ggm_id,CMP.cmp_name";
	    	
	    	if ( $seek == '' ){
	    		 
	    		return $this->getList($condition,$field,' ORDER BY GQ.end_time ASC',$page,20,true,$leftjoin);
	    		 
	    	}else{
	    		 
	    		
	    		
	    		return $this->getList($seek.$condition,$field,' ORDER BY GQ.end_time ASC',$page,20,true,$leftjoin);
	    		 
	    	}
	    	
	    }
	    
	    
	    /**
	     * 获取列表数据总数
	     * @return Ambigous <number, false>
	     */
	    public function listCount($seek=''){
	    
	    	$this->_tableName='gg_queue as GQ,gg_materials as GM,gg_position as GP,cmp_company as CMP';
	    	
	    	//条件
	    	$condition = "GQ.ggp_id=GP.ggp_id and (GQ.cmp_id=GM.mod_id and GQ.ggm_id=GM.ggm_id) and GQ.is_del!=1 and GM.mod_id=CMP.cmp_id";
	    
	    	return $this->getCount($seek.$condition);
	    
	    }
	    
	    /**
	     * 获取编辑所需数据
	     * @param unknown $id
	     * @return Ambigous <multitype:, false, boolean>
	     */
	    public function getOneById($id){
	    	
	    	return $this->getOne($this->_primaryKey.'='.$id,array('ggq_id','ggm_id','contractNo_id','start_time','end_time','ggp_id','cmp_id','ggm_id','major','issuer','manager','finance','leader','remark','link_man','link_tel'));
	    	
	    }
	    
	    public function doEdit($data){
	    	return $this->update("ggq_id=".$data['ggq_id'],$data);
	    	
	    }
	    
	    /**
	     * 删除处理方法
	     */
	    public function del($data){
	    	 
	    	if ( is_array($data) ){//多选
	    
	    		$delStr = implode($data, ',');
	    		//die($delStr);
	    		return $this->update($this->_primaryKey." in (".$delStr.")", 'is_del=1');
	    
	    	}else{//单选
	    
	    		return $this->update($this->_primaryKey."=".$data, "is_del=1");
	    
	    	}
	    	 
	    }
	    
	    /**
	     * 审核处理方法
	     */
	    public function audit($data,$audit=''){
	    	 
	    	if ( is_array($data) ){//多选
	    	  
	    		$delStr = implode($data, ',');
	    		//die($delStr);
	    		return $this->update($this->_primaryKey." in (".$delStr.")", 'audit_status='.$audit);
	    	  
	    	}else{//单选
	    	  
	    		return $this->update($this->_primaryKey."=".$data, "audit_status=".$audit);
	    	  
	    	}
	    	 
	    }
	    
	    
	    public function ggposition($id){
	    	
	    	//字段
	    	$field = array(
	    			'start_time',
	    			'end_time',
	    	);
	    	
	    	return $this->getOne('ggp_id='.$id.' and is_del!=1',$field);
	    	
	    }
	    
	}
	/**
	* End file,Don't add ?> after this.
	*/
?>