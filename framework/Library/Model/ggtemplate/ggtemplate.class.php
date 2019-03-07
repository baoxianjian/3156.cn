<?php

	/**
	 * 规格Model
	 * @author Administrator
	 *
	 */

	class Model_ggtemplate_ggtemplate extends Base_model{
	    //@override
	    public function setPrimaryKey() {
	        $this->_primaryKey = 'ggt_id';
	    }
	    //@override
	    public function setTableName() {
	        $this->_tableName = 'gg_template';
	        
	    }
	   	public function setCache($status=true){
	    	$this->_cache = $status;
	    }
	    
	    
    /**
     * 查询list页数据
     * @param unknown $condition
     * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
     */
	 public function getListAll($page='', $data=''){
	    	
	    	if ( $page != '' ){
	    		
	    		if ( $data != NULL  ){//搜索
	    			 
	    			foreach ( $data as $k=>$v ){
	    			
	    				if ( is_array($v) ){
	    						
	    					$sqlStr .= $k.$v[0].$v[1].' and ';
	    						
	    				}else{
	    						
	    					$sqlStr .= $k.'='.$v.' and ';
	    						
	    				}
	    			
	    			
	    			}
	    			
	    			//die(substr($sqlStr, 0, strrpos($sqlStr, ' and ')));
	    			
	    			return $this->getList(substr($sqlStr, 0, strrpos($sqlStr, ' and ')), array('ggt_id','ggt_type','name','remark','dateline'), array('ggt_id'=>'DESC'), $page, 10 ,true);
	    			 
	    		}else{
	    			 
	    			return $this->getList("is_del!=1",array('ggt_id','ggt_type','name','remark','dateline'), array('ggt_id'=>'DESC'), $page, 10, true);
	    			 
	    		}
	    		
	    	}else{
	    		
	    		if ( $data != NULL  ){//搜索
	    			 
	    			return $this->getList($data,array('ggt_id','ggt_type','name','remark','dateline'), array('ggt_id'=>'DESC'));
	    			 
	    		}else{
	    			 
	    			return $this->getList("is_del!=1",array('ggt_id','ggt_type','name','remark','dateline'), array('ggt_id'=>'DESC'));
	    			 
	    		}
	    		
	    		
	    	}
	    	
	    	
	    	
	    }
	    /**
	     * 获取编辑所需字段
	     * @param unknown $id
	     * @return Ambigous <multitype:, false, boolean>
	     */
	    public function getOneById($id){
	    	
	    	return $this->getOne("ggt_id=".$id,array('ggt_id','ggt_type','name','remark','code'));
	    	
	    }
	    
	    /**
	     * 更新编辑处理方法
	     * @param unknown $condition
	     * @param unknown $data
	     * @return Ambigous <number, false, boolean>
	     */
	    public function updateById($condition,$data){
	    	
	    	return $this->update("ggt_id=".$condition, $data);
	    	
	    }
	    
	    public function getCountList($data=''){
	    	
	    	if ( $data != NULL ){
	    		
	    		return $this->getCount($data);
	    		
	    	}else{
	    		
	    		return $this->getCount("is_del!=1");
	    		
	    	}
	    	
	    }
	    
	    /**
	     * 删除处理方法
	     */
	    public function del($data){
	    	
	    	if ( is_array($data) ){//多选
	    		
	    		$delStr = implode($data, ',');
	    		//die($delStr);
	    		return $this->update("ggt_id in (".$delStr.")", 'is_del=1');
	    		
	    	}else{//单选
	    		
	    		return $this->update("ggt_id=".$data, "is_del=1");
	    		
	    	}
	    	
	    }
	    
	    
		/**
	     * 自动加载查询方法
	     * @param unknown $method
	     * @param unknown $args
	     */
	    public function __call($method, $args){
	    	
	    	$condition = preg_replace('/^findBy(\w{1,})$/', '$1', $method);//获取条件字段
	    	return $this->getCondition($condition, $args);
	    	
	    }
	    
	    /**
	     * 获取条件并查询方法
	     * @param unknown $condition
	     * @param unknown $args
	     */
	    public function getCondition($condition, $args=array()){
	    	
	    	$args[1] == NULL && $args[1] = "=";
	    	if ( !is_array($args[2]) ){
	    		
	    		if ( $args[2] == NULL ) $args[2] = $condition;
	    		return $this->getOne($condition.$args[1]."'{$args[0]}'", $args[2]);
	    		
	    	}else{
	    		
	    		return $this->getList($condition.$args[1]."'{$args[0]}'", $args[2], array('ggt_id'), $args[3], 10, true);
	    		
	    	}
	    
	    	
	    }
	    
	}
	/**
	* End file,Don't add ?> after this.
	*/
?>