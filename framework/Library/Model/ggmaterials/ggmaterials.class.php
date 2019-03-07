<?php

class Model_ggmaterials_ggmaterials extends Base_model{
	//@override
	public function setPrimaryKey() {
		$this->_PK = 'ggm_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'gg_materials';

	}
	public function setCache($status=false){
		$this->_cache = $status;
	}
	
	/**
	 * 添加数据处理方法
	 * @param unknown $data
	 * @return Ambigous <number, false, boolean, multitype:>
	 */
	public function upAdd($data){
		
		return $this->insert($data);
		
	}
	
	/**
	 * 编辑数据
	 */
	public function updateById($id, $data){
		
		return $this->update('ggm_id='.$id, $data);
		
	}
	
	/**
	 * 查询列表页
	 */
	public function getListALl($page, $condition=''){

        $sqlStr=' gm.is_del!=1';
	    if(is_array($condition))
        {
             $sqlStr.=' and ';
			foreach ( $condition as $k=>$v ){

                /*
                if($k == 'mid_or_pid')
                {   
                    $cmp_id=intval($v['mod_id']);
                    $pdt_id=intval($v['pdt_id']);
                    if($cmp_id && $pdt_id)
                    {
                         $sqlStr .= "(mod_id = {$cmp_id} OR pdt_id = {$pdt_id}) and "; 
                    }
                    else
                    {
                        if($cmp_id)
                        {
                            $sqlStr .="mod_id = {$cmp_id} and ";
                        }
                        if($pdt_id)
                        {
                            $sqlStr .="pdt_id = {$pdt_id} and ";
                        }
                    }  
                }
				else */if ( is_array($v) ){
					

                    if($v[1])
                    {
					    $sqlStr .= $k.$v[0].$v[1]." and ";
                    }
					
				}elseif($k == 'title'){
					
					$sqlStr .= 'gm.'.$k." like '%{$v}%' and ";
				}
                elseif($k == 'audit_state'){
                    
                    $sqlStr .= 'gm.'.$k." ={$v} and ";
                }
                else
                {
					if($v)
                    {
					    $sqlStr .= $k."=".$v." and ";
                    }
					
				}
			}                                                                          
             $condition = substr($sqlStr, 0, strrpos($sqlStr, ' and '));    
        }
        else if(!$condition)
        {
            $condition = $sqlStr;
        }
        else
        {
             $condition .= ' AND '. $sqlStr;          
        }

            $old_tn = $this->_tableName;
            $this->_tableName='gg_materials gm';
            //$leftjoin="left join search_ggs sg ON sgp.sgp_id=sg.sgp_id ";
            $leftjoin=array('cmp_company c'=>'gm.mod_id=c.cmp_id');
            $data=$this->getList($condition,"gm.*,c.cmp_name",array('ggm_id'=>'desc'),$page,20,true,$leftjoin);          
            $this->_tableName=$old_tn;    
            return $data;
            
            
            


		//	return $this->getList($condition, array('*'), array('ggm_id'=>'DESC'), $page, 10 ,true);
			
		}

	
	/**
	 * 删除数据处理方法
	 */
	public function del($id, $is_all=false){
		
		if ( !$is_all ){
			
			return $this->update('ggm_id='.$id, 'is_del=1');
			
		}else{
			//die($id);
			return $this->update("ggm_id in ($id)", 'is_del=1');
			
		}
		
	}
	
	/**
	 * 查询分页总数
	 */
	public function getCountList($condition){
		
	if ( $condition == '' ){
			
			return $this->getCount('is_del!=1');
			
		}else{
			
			foreach ( $condition as $k=>$v ){
				
				if ( is_array($v) ){
					
					$sqlStr .= $k.$v[0].$v[1].' and ';
					
				}else{
					
					$sqlStr .= $k.'='.$v.' and ';
					
				}
				
				
			}
			//die(substr($sqlStr, 0, strrpos($sqlStr, ' and ')));
			return $this->getCount(substr($sqlStr, 0, strrpos($sqlStr, ' and ')));
			
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
	    		
	    		return $this->getList($condition.$args[1]."'{$args[0]}'", $args[2], array('ggm_id'), $args[3], 10, true);
	    		
	    	}
	    
	    	
	    }
        
        
        function getTypeList()
        {
            //1文字2图片3flash4.flv视频5代码
            return array(1=>'文字',2=>'图片');
        }
	
}
/**
 End file,Don't add ?> after this.
 */