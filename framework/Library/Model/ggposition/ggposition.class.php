<?php

	/**
	 * 广告位Model
	 * @author Administrator
	 *
	 */
	class Model_ggposition_ggposition extends Base_model{
	    //@override
	    public function setPrimaryKey() {
	        $this->_PK = 'ggp_id';
	    }
	    //@override
	    public function setTableName() {
	        $this->_tableName = 'gg_position';
	        
	    }
	   	public function setCache($status=false){
	    	$this->_cache = $status;
	    }
	    
	    /**
	     * 获取列表数量
	     * @return Ambigous <number, false>
	     */
	    public function count($seek=''){
	    	
	    	$this->_tableName='gg_position as gp,gg_template as ggt';
	    	if ( $seek == '' ){
	    		
	    		return $this->getCount("gp.gg_tpl_id=ggt.ggt_id and gp.is_del!=1");
	    		
	    	}else{
	    	//	die($seek);
	    		return $this->getCount($seek."gp.gg_tpl_id=ggt.ggt_id and gp.is_del!=1");
	    		
	    	}
	    	
	    	
	    }
	    
	    /**
	     * 获取列表数据
	     * @param unknown $page
	     * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
	     */
	    public function getListAll($page,$seek='',$order='DESC'){
	    	$this->_tableName='gg_position as gp';
	    	$leftjoin=',gg_template as gte';
	    	
	    	if ( $seek == '' ){
	    		
	    		return $this->getList("gp.gg_tpl_id=gte.ggt_id and gp.is_del!=1","gp.add_red,gp.order,gp.gg_sale_state,gp.click_count,gte.ggt_type,gp.ggp_id,gp.title,gp.ggpg_id,gp.ggpg_id2"," ORDER BY gp.click_count $order,gp.ggp_id",$page,20,true,$leftjoin);
	    		
	    	}else{
	    	//	die($seek);
	    		return $this->getList($seek."gp.gg_tpl_id=gte.ggt_id and gp.is_del!=1","gp.add_red,gp.order,gp.gg_sale_state,gp.click_count,ggt_type,gp.ggp_id,gp.title,gp.ggpg_id,gp.ggpg_id2"," ORDER BY gp.click_count $order,gp.ggp_id",$page,20,true,$leftjoin);
	    		
	    	}
	    	
        	
	    	
	    }
	    
	    /**
	     * 获取编辑数据
	     * @param unknown $id
	     * @return Ambigous <multitype:, false, boolean>
	     */
	    public function getOneById($id){
	    	
	    	return $this->getOne("ggp_id=".$id,array('ggp_id','title','ggpg_id','ggpg_id2','gg_tpl_id','price_level','add_red','`order`','gg_sale_state','remark','refresh_time'));
	    	
	    }
	    
	    
	    /**
	     * 删除处理方法
	     */
	    public function del($data){
	    
	    	if ( is_array($data) ){//多选
	    		 
	    		$delStr = implode($data, ',');
	    		//die($delStr);
	    		return $this->update("ggp_id in (".$delStr.")", 'is_del=1');
	    		 
	    	}else{//单选
	    		 
	    		return $this->update("ggp_id=".$data, "is_del=1");
	    		 
	    	}
	    
	    }
	    
	    /**
	     * 校验广告位名称是否存在
	     * @param unknown $name
	     * @return Ambigous <multitype:, false, boolean>
	     */
	   public function exist_Name($name){
	   	
	   		return $this->getOne("title='{$name}' and is_del!=1",'title');
	   	
	   }
	   
	   /**
	    * 查询广告位名称
	    * @param unknown $id
	    * @return Ambigous <multitype:, false, boolean>
	    */
	   public function getName($id){
	   	
	   		return $this->getOne('ggp_id='.$id,'title');
	   	
	   }
	   
	   /**
	    * 添加更新数据
	    * @param unknown $data
	    * @param string $id
	    * @return Ambigous <number, false, boolean, multitype:>|Ambigous <number, false, boolean>
	    */
	   public function edit_add($data, $id=false){
	   		
	   		//添加
	   		if ( $id == false ){
	   			
	   			return $this->insert($data);
	   			
	   		}else{
	   			
	   			return $this->update("ggp_id=".$id, $data);
	   			
	   		}
	   	
	   }
	   
	   /**
	    * 一级分组获取广告位
	    * @param unknown $id
	    * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
	    */
	   public function getParentId1($id){
	   	
	   		return $this->getList("ggpg_id=".$id,'ggp_id');
	   	
	   }
	   
	   /**
	    * 二级分组获取广告位
	    * @param unknown $id
	    * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
	    */
	   public function getParentId2($id){
	   	
	   		return $this->getList("ggpg_id2=".$id,'ggp_id');
	   	
	   }
	  
	   public function ggQueue($id,$sqlStr=''){
	   		
	   		$field = array(
	   				'ggp_id',
	   				'title'
	   		);
	   		if ( $sqlStr != NULL ){
	   			//$sqlStr = '0,'.$sqlStr;
	   			//die(var_dump($id));
	   		//	die("((ggpg_id=".$id." or ggpg_id2=".$id.") and ggp_id not in ('".$sqlStr."')) and is_del!=1");
	   			return $this->getList("((ggpg_id=".$id." or ggpg_id2=".$id.") and ggp_id not in (".$sqlStr.")) and is_del!=1",$field);
	   			
	   		}else{
	   			
	   			return $this->getList("ggpg_id=".$id." or ggpg_id2=".$id,$field);
	   			
	   		}
	   		
	   	
	   }
	   
	   public function ggQueue2($id,$sqlStr){
	   	
		   	$field = array(
		   			'ggp_id',
		   			'title'
		   	);
		   	return $this->getList("ggpg_id=".$id." and ggpg_id2=-1 and ggp_id not in ('".$sqlStr."')",$field);
		   		 
	   }
	   
	    
	   
	   public function ggresource($page,$seek='',$order=''){
	   	
		   	$this->_tableName='gg_queue as gq';
		   	$leftjoin=',gg_position as gp,gg_template as gt';
		   	
		   	//条件
		   	$condition = "gp.gg_tpl_id=gt.ggt_id and gp.is_del!=1 and gp.ggp_id=gq.ggp_id";
		   	
		   	//字段
		   	$field = "gq.start_time,gq.end_time,gp.add_red,gp.order,gp.gg_sale_state,gp.click_count,gt.ggt_type,gp.ggp_id,gp.title,gp.ggpg_id,gp.ggpg_id2";
		   	
		   	if ( $seek == '' ){
		   	
		   		return $this->getList($condition,$field,' ORDER BY GQ.end_time ASC',$page,10,true,$leftjoin);
		   	
		   	}else{
		   	
		   		 
		   		 
		   		return $this->getList($seek.$condition,$field,' ORDER BY GQ.end_time ASC',$page,10,true,$leftjoin);
		   	
		   	}
	   	
	   }
	   

    
    
        /**
        * 得到某个页面下的广告
        *  add by baoxianjian 13:20 2015/4/16
        * @param int $page  id
        * @return array|false
       
        public function getRowsetAdsByPage($page)
        {
            if(!$page=intval($page)){return false;}
            $odl_tn=$this->_tableName;
            
            $fields="ggp.ggp_id,ggp.title,ggp.ggpg_id, ggp.ggpg_id2, ggp.order,
                     ggq.ggq_id,ggq.start_time,ggq.end_time,  
                     ggt.`code`,  
                     ggm.ggm_id,ggm.src, ggm.slogan AS title, ggm.link_url,
                     ggs.width,ggs.height,ggs.length";
            
            $this->_tableName='gg_position ggp';
            
            $leftjoin="
                LEFT JOIN gg_queue ggq     ON ggp.ggp_id     =ggq.ggp_id  AND  ". NOW." >= ggq.start_time AND ".NOW." <= ggq.end_time  AND ggq.audit_status=2
                LEFT JOIN gg_template ggt  ON ggp.gg_tpl_id  =ggt.ggt_id
                LEFT JOIN gg_materials ggm ON ggq.ggm_id     =ggm.ggm_id
                LEFT JOIN gg_standard ggs  ON ggm.ggs_id     =ggs.ggs_id
                ";
            $condition=" ggp.ggpg_id={$page}";
            
            $data=$this->getList($condition,$fields,' ORDER BY ggp.ggpg_id2 asc, ggp.`order` desc   , ggp.ggp_id desc',1,1000,0,$leftjoin);          
            
            $this->_tableName=$odl_tn;
            return $data['list']; 
        }
         */ 
        
        
        
        /**
        * 得到某个组下的广告
        * 19:40 2015/4/19 add by baoxianjian
        * @param int $gid
        * @return array|false
        */
        public function getRowsetAdsAll($srow)
        {
    /*
    [pid] => 1
    [gid] => Array
        (
            [0] => 2
            [1] => 3
        )

    [id] => Array
        (
            [0] => 3
            [1] => 5
        )

    [not] => Array
        (
            [0] => 1
            [1] => 2
            [2] => 3
        )
    */
            
            if(is_array($id))
            {
                foreach ($id as $k=>$v)
                {
                    $id[$k]=intval($v);
                }
                $ids=implode(',',$id);
                $condition=" ggp.ggp_id IN ({$ids})";
                
            }
            else if($id=intval($srow['id']))
            {
                $condition=" ggp.ggp_id={$id}";
            }
            else
            {
                $pid=intval($srow['pid']);
                $gid=intval($srow['gid']);

                if(!$pid && !$gid && !$id){return false;}
                                
                if($pid)
                {
                    $condition=" ggp.ggpg_id={$pid}";
                }
                
                if($gid)
                {
                    if($condition){$condition.=" AND ";}
                    $condition=" ggp.ggpg_id2={$gid}";
                }
            }
            
            $odl_tn=$this->_tableName;
            
            $fields="ggp.ggp_id,ggp.title AS ggp_title,ggp.ggpg_id, ggp.ggpg_id2, ggp.order, ggp.add_red, ggp.refresh_time,
                     ggq.ggq_id,ggq.start_time,ggq.end_time,  
                     ggt.`code`,  
                     ggm.ggm_id,ggm.src, ggm.slogan, ggm.gg_title AS title, ggm.link_url, ggm.ggm_type,
                     ggs.width,ggs.height,ggs.length";
            
            $this->_tableName='gg_position ggp';
            
            $leftjoin="
                LEFT JOIN gg_queue ggq     ON ggp.ggp_id     =ggq.ggp_id  AND  ". NOW." >= ggq.start_time AND ".NOW." <= ggq.end_time  AND ggq.audit_status=2
                LEFT JOIN gg_template ggt  ON ggp.gg_tpl_id  =ggt.ggt_id
                LEFT JOIN gg_materials ggm ON ggq.ggm_id     =ggm.ggm_id
                LEFT JOIN gg_standard ggs  ON ggm.ggs_id     =ggs.ggs_id
                ";

            $data=$this->getList($condition,$fields,' ORDER BY ggp.ggpg_id2 asc, ggp.`order` desc   , ggp.ggp_id desc',1,1000,0,$leftjoin);          
            
            $this->_tableName=$odl_tn;
            return $data['list']; 
        }
        
        
        

        /**
        * 得到一些广告位过期中最近的广告信息
        * 19:26 2015/4/16
        * @param mixed $ids 广告位ids
        * @return array|false
        */
        public function getRowsetAdsLast($ids)
        {
            if(!$ids){return false;}
            $odl_tn=$this->_tableName;
            $fields="ggp.ggp_id,ggp.title AS ggp_title,ggp.ggpg_id, ggp.ggpg_id2, ggp.order, ggp.add_red, ggp.ggp_type, ggp.refresh_time,
                     ggq.ggq_id,ggq.start_time,ggq.end_time,  
                     ggt.`code`,  
                     ggm.ggm_id,ggm.src, ggm.slogan, ggm.gg_title AS title, ggm.link_url, ggm.ggm_type, 
                     ggs.width,ggs.height,ggs.length";
            
            $this->_tableName='gg_position ggp';
            
            $leftjoin="
                LEFT JOIN gg_queue ggq     ON ggp.ggp_id     =ggq.ggp_id AND ggq.ggq_id =
                                        (SELECT ggq2.ggq_id FROM gg_queue ggq2
                                        INNER JOIN gg_position ggp2 ON ggp2.ggp_id =ggq2.ggp_id 
                                        WHERE ggp2.ggp_id=ggp.ggp_id AND ".NOW." >= ggq2.end_time AND ggq.audit_status=2
                                        ORDER BY ggq2.end_time DESC LIMIT 0,1) AND ggq.audit_status=2
                LEFT JOIN gg_template ggt  ON ggp.gg_tpl_id  =ggt.ggt_id
                LEFT JOIN gg_materials ggm ON ggq.ggm_id     =ggm.ggm_id
                LEFT JOIN gg_standard ggs  ON ggm.ggs_id     =ggs.ggs_id
                ";
            $condition=" ggp.ggp_id in({$ids})";
            
            $data=$this->getList($condition,$fields,' GROUP BY ggp.ggp_id ORDER BY ggp.ggpg_id2 asc, ggq.end_time desc, ggp.ggp_id desc ',1,1000,0,$leftjoin);          
            
            $this->_tableName=$odl_tn; 
            
            return $data['list']; 
        }
        
        public function getRowsetRefresh($cmp_id, $gids)
        {
             if(!$cmp_id=intval($cmp_id)){return false;}
             if(!$gids) {return false;}

             $condition="ggq.cmp_id={$cmp_id} AND ggp.ggpg_id2 IN ({$gids})";
             
            $odl_tn=$this->_tableName;
            
            $fields="ggp.ggp_id,ggp.title AS ggp_title,ggp.ggpg_id, ggp.ggpg_id2, ggp.order, ggp.add_red, ggp.refresh_time,
                     ggq.ggq_id,ggq.start_time,ggq.end_time, ggq.audit_status,  
                     ggt.`code`,  
                     ggm.ggm_id,ggm.src, ggm.slogan, ggm.gg_title AS title, ggm.link_url, ggm.ggm_type,
                     ggs.width,ggs.height,ggs.length";
            
            $this->_tableName='gg_position ggp';
            
            $leftjoin="
                LEFT JOIN gg_queue ggq     ON ggp.ggp_id     =ggq.ggp_id
                LEFT JOIN gg_template ggt  ON ggp.gg_tpl_id  =ggt.ggt_id
                LEFT JOIN gg_materials ggm ON ggq.ggm_id     =ggm.ggm_id
                LEFT JOIN gg_standard ggs  ON ggm.ggs_id     =ggs.ggs_id
                ";

            $data=$this->getList($condition,$fields,' ORDER BY ggp.ggpg_id2 asc, ggp.`order` desc   , ggp.ggp_id desc',1,1000,0,$leftjoin);          
            
            $this->_tableName=$odl_tn;
            return $data['list']; 
             
        }
        
        
        
    }    
    
    
	/**
	* End file,Don't add ?> after this.
	*/
?>