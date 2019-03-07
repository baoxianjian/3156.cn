<?php

	/**
	 * 规格Model
	 * @author Administrator
	 *
	 */
	class Model_ggstandard_ggstandard extends Base_model{
	    //@override
	    public function setPrimaryKey() {
	        $this->_primaryKey = 'ggs_id';
	    }
	    //@override
	    public function setTableName() {
	        $this->_tableName = 'gg_standard';
	        
	    }
	   	public function setCache($status=false){
	    	$this->_cache = $status;
	    }
	    
	    /**
	     * 获取编辑所需字段方法
	     */
	    public function getOneById($id){   
	    	return $this->getOne("ggs_id=".$id, array('ggs_name','ggs_type','width','height','length','remark'));
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
	    		
	    		$args[2] = $condition;
	    		return $this->getOne($condition.$args[1]."'{$args[0]}'", $args[2]);
	    		
	    	}else{
	    		
	    		return $this->getList($condition.$args[1]."'{$args[0]}'", $args[2], array('ggs_id'), $args[3]);
	    		
	    	}
	    
	    	
	    }
	    
	    public function getName($data){

	    	if ( preg_match('/^\d{1,}$/s',$data) != NULL ){
	    		
	    		return $this->getOne('ggs_id='.$data,'ggs_name');
	    		
	    	}else{

	    		return $this->getOne("ggs_name='{$data}'",'ggs_name');
	    		
	    	}
	    	
	    }
	    
	    /**
	     * 获取搜索个数
	     */
	    public function getSeekCount($condition){
	    	
	    	foreach ( $condition as $k=>$v ){
	    	
	    		if ( is_array($v) ){
	    				
	    			$sqlStr .= $k.$v[0].$v[1]." and ";
	    				
	    		}elseif( $k == 'ggs_name' ){
	    			
	    			$sqlStr .= $k." like '%{$v}%' and ";
	    			
	    		}else{
	    				
	    			$sqlStr .= $k."='".$v."' and ";
	    				
	    		}
	    	
	    	
	    	}
	    	//die(substr($sqlStr, 0, strrpos($sqlStr, ' and ')));
	    	return $this->getCount(substr($sqlStr, 0, strrpos($sqlStr, ' and ')));
	    	
	    }
	    
	    
	    /**
	     * 搜索查询处理方法
	     * @param array $condition
	     * @param int $page
	     * @return boolean
	     */
	    public function getSeek(array $condition, $page=''){
	    	
	    	if ( !is_array($condition) ) return false;
			if ( $page ){
				
				foreach ( $condition as $k=>$v ){
				
					if ( is_array($v) ){
		    				
		    			$sqlStr .= $k.$v[0].$v[1]." and ";
		    				
		    		}elseif( $k == 'ggs_name' ){
		    			
		    			$sqlStr .= $k." like '%{$v}%' and ";
		    			
		    		}else{
		    				
		    			$sqlStr .= $k."='".$v."' and ";
		    				
		    		}
					
				
				}
				//die(substr($sqlStr, 0, strrpos($sqlStr, ' and ')));
				return $this->getList(substr($sqlStr, 0, strrpos($sqlStr, ' and ')), array('ggs_id','ggs_name','ggs_type','width','height','length'), array('ggs_id'), $page, 10 ,true);
					
				
				
				//return $this->getList($condition, array('ggs_id','ggs_name','ggs_type','width','height','length'), array('ggs_id'), $page, 10, true);
				
			}else{
				
				return $this->getList($condition, array('ggs_id','ggs_name','ggs_type','width','height','length'), array('ggs_id'));
				
			}
	    	
	    }
	    
	}
	/**
	    End file,Don't add ?> after this.
	*/
?>