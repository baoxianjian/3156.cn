<?php
 /**
* @name: 关键字管理的数据模型 
* @author: zhanghao
* @date: 17:37 2015/4/13
*/
class Model_agent_agent extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'cmt_id';
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'cmt_comments';
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    /**
    * 得到关键字列表所有
    * 
    */
    public function getListAll($page,$srow)
     { 
       	 $condition ="is_del=0"; //查询条件
       	// $condition='1';
    if(count($srow)>0)
        {   
        	if($online_state=intval($srow['online_state']))
        	{
        	$condition.=" AND `online_state` = $online_state";
        	}
        	if($dateline=trim($srow['dateline']))
        	{
        		$condition.=" AND `dateline` = $dateline";
        	}
        	if($pdt_id=intval($srow['pdt_id']))
        	{
        		$condition.=" AND `pdt_id` = $pdt_id";
        	}
        	if($user_id=intval($srow['user_id']))
        	{
        		$condition.=" AND `user_id` = $user_id";
        	}
        	if($areas=trim($srow['areas']))
        	{
        		$condition.=" AND `areas` = '$areas'";
        	}
        	if($company=trim($srow['company']))
        	{
        		$condition.=" AND `company` = '$company'";
        	}
        	if($link_man=trim($srow['link_man']))
        	{
        		$condition.=" AND `link_man` = '$link_man'";
        	}
			
        
        }
		print_r($condition);
 /*       
        $this->_tableName='cmt_product_agent cpa';
        //$leftjoin="left join search_ggs sg ON sgp.sgp_id=sg.sgp_id ";
        $leftjoin=array('cmt_comments cc'=>'cc.cmt_id=cpa.cmt_id');
        $data=$this->getList($condition,"cc.*,cpa.pdt_id",array('cmt_id'=>'desc'),$page,10,true,$leftjoin);
        $this->_tableName='cmt_comments';
        return $data;
 */       
       return $this->getList($condition,array('*'),array('cmt_id'=>'desc'),$page,$limit=10,true);
       
        
    }
    
    /**
     * 得到
     *
     * @param mixed $page
     * @return array|false
     */
 /*   public function getRowsetAdsByPage($page)
    {
    	
    	$this->_tableName='cmt_comments cc';
    	$condition="cc.`is_del`='0'";
    			// $leftjoin=array('search_ggs sg'=>'sgp.sg_id=sg.sg_id'); //
    
    	$leftjoin='LEFT JOIN cmt_product_agent cpa ON cc.mod_id=cpa.pdt_id';
        $data=$this->getList($condition,"cpa.*",' ORDER BY  cc.cmt_id desc',1,10,true,$leftjoin);
            		// $this->_tableName='search_ggs';
    
    	return $data; 
    
    	
    	/*
    	$condition = array('is_del'=>'0','sgp_page'=>$page); //查询条件
    
    	return $this->getList($condition,array('*'),array('sgp_page'=>'asc','sgp_area'=>'asc','order'=>'asc'),$page,0,false);
    	
    	}    
              */
  
    /**
     * 根据主键id删除一行数据
     *
     * @param int $id
     * @return int|false
     */
    
    public function deleteRowById($id,$destroy=false)
    {
    	if(!$id=intval($id)) return false;
    
    	return $this->update(array($this->_PK=>$id), array('is_del=1'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    
    /**
     * 添加
     *
     * @param array $row
     * @return int|false
     */
    public function addRow($row)
    {
    	if(!$row) return false;
    	return $this->insert($row);
    	//return $this->delete(array($this->_PK=>$id));
    }
    
    
    /**
     * 修改一条记录（根据Id）
     *
     * @param array $row
     * @param int $id
     * @return int|false
     */
    public function updateRowById($row,$id=0)
    {
    	if(!$row) return false;
    	if(!$id=intval($id)){return false;}
    
    	unset($row[$this->_PK]);
    	return $this->update(array($this->_PK=>$id),$row);
    	//return $this->delete(array($this->_PK=>$id));
    }
    /**
     *
     * 根据主键id审核多条数据
     * @param string $ids
     * @return int|false
     */
    public function examRowByIds($ids,$destroy=false)
    {
    	$id_list=$ids;
    	if(!is_array($ids))
    	{
    		$id_list=explode(',', $ids);
    	}
    
    
    	foreach ($id_list as $k=>$v)
    	{
    		$id_list[$k]=intval($v);
    	}
    	$ids=implode(',', $id_list);
    
    
    	if(!$ids){return false;}
    
    	return $this->update($this->_PK." in ($ids)", array('audit_state=2'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    
    
    /**
    * 根据关键字名称得到一条数据
    * 
    * @param mixed $name
    * @return array|false
    */
    public function getRowByName($name)
    {
        if(!$name){return false;}
        return $this->getone("kw_name='$name'");
    }
    
 /**
     * 查询数据
     *
     * @param string
     * @return int|false
     */
    public function getListAgentcomments($page,$srow,$ids)
    {
             
    	
        if(intval($srow['is_del'])==2){
    	      $condition ="is_del=1";
         }else{
         	  $condition ="is_del=0"; 
         }
         
         
        $ids=$this->parseIds($ids);
        if($ids)
        {
            $condition.=" AND `cmp_id` in($ids) ";
            
        } 
         
         
         
         if(count($srow)>0){
            if($today=strtotime($srow['dateline']))
            {  
                $end_time=$today+86400;
                
                $condition.=" AND `dateline`>{$today} and `dateline`<{$end_time}";
            }
         	if($pdt_name=trim($srow['pdt_name']))
         	{
         		$condition.=" AND `pdt_names` like '%$pdt_name%'";
         	}
         	if($areas=trim($srow['areas']))
         	{
         		$condition.=" AND `areas` = '$areas'";
         	}
         	if($cmp_name=trim($srow['cmp_name']))
         	{
         		$condition.=" AND `cmp_name` like '%$cmp_name%'";
         	}
	        if($user_id=intval($srow['user_id']))
	    	{
	    		if($user_id==1)
	    		{
	    			$condition.=" AND `user_id`!=0 and `sa_id`=0 ";
	    		}	    		
	    		if($user_id==3)
	    		{
	    			$condition.=" AND `user_id`=0 ";
	    		}
	    		 
	    	}
	    	if($tel=trim($srow['tel']))
	    	{
	    		$condition.=" AND `tel` = '$tel'";
	    	}
	    	if($mp=trim($srow['mp']))
	    	{
	    		$condition.=" AND `mp` = '$mp'";
	    	}
	    	if($link_man=trim($srow['link_man']))
	    	{
	    		$condition.=" AND `link_man` like '%$link_man%'";
	    	}
         	if($online_state=intval($srow['online_state']))
         	{
         		$condition.=" AND `online_state` = $online_state";
         	}
         	
         	if($audit_state=trim($srow['audit_state']))
         	{
         		$condition.=" AND `audit_state` = '$audit_state'";
         	}
         	if($siteid=trim($srow['siteid']))
         	{
         		$condition.=" AND `siteid` = '$site_id'";
         	}
         	if($web_id=trim($srow['web_id']))
         	{
         		$condition.=" AND `webid` = '$web_id'";
         	}
         	if($mod_type=trim($srow['mod_type']))
         	{
         		$condition.=" AND `mod_type` = '$mod_type'";
         	}
         	if($mod_id=trim($srow['mod_id']))
         	{
         		$condition.=" AND `mod_id` = '$mod_id'";
         	}
         } 
         
         
         
         
 /*   	
    
    	if($user_id=intval($srow['user_id']))
    	{
    		if($user_id==1)
    		{
    			$condition.=" AND `user_id`!=0 and `sa_id`=0 ";
    		}
    		if($user_id==2)
    		{
    			$condition.=" AND `sa_id`!=0";
    		}
    		if($user_id==3)
    		{
    			$condition.=" AND `user_id`=0 and `sa_id`=0";
    		}
    		 
    	}
    
    	if($cmp_name=trim($srow['cmp_name']))
    	{
    		$condition.=" AND `cmp_name` like '%$cmp_name%'";
    	}    	
    	{
    		$condition.=" AND `siteid` = '$site_id'";
    	}
    	if($web_id=trim($srow['web_id']))
    	{
    		$condition.=" AND `webid` = '$web_id'";
    	}
    	if($mod_type=trim($srow['mod_type']))
    	{
    		$condition.=" AND `mod_type` = '$mod_type'";
    	}
    	if($mod_id=trim($srow['mod_id']))
    	{
    		$condition.=" AND `mod_id` = '$mod_id'";
    	}    	    
    }
    
    */
    	
    return  $this->getList($condition,array('*'),array('dateline'=>'desc'),$page,$limit=30,true);
          
    }
    /**
     * 
     * 视图查询已删除数据
     * @param string
     * @return int|false
     */
    
    public function getListDel($page,$srow)
    {
    	$old_pk=$this->_PK;
    	$old_tablename=$this->_tableName;
        
    	$this->_PK = 'id';
    	$this->_tableName = 'v_agentcomments';
    
    	$condition ="is_del=1";  //查询条件
    	//  print_r($srow);
    	if(count($srow)>0)
    	{
    		
    		if($notice_type=intval($srow['notice_type']))
    		{
    			$condition.=" AND `type` = $notice_type";
    		}else{
    			$condition.=" AND `type` = 2 ";
    		}
    		if($online_state=intval($srow['online_state']))
    		{
    			$condition.=" AND `online_state` = $online_state";
    		}
    		if($dateline=strtotime($srow['dateline']))
    		{
    			$condition.=" AND `dateline`-$dateline<86400 and `dateline`-$dateline>0";
    		}
    		if($pdt_name=trim($srow['pdt_name']))
    		{
    			$condition.=" AND `pdt_name` like '%$pdt_name%'";
    		}
    		if($user_id=intval($srow['user_id']))
    		{
    			if($user_id==1)
    			{
    				$condition.=" AND `user_id`!=0 and `sa_id`=0 ";
    			}
    			if($user_id==2)
    			{
    				$condition.=" AND `sa_id`!=0";
    			}
    			if($user_id==3)
    			{
    				$condition.=" AND `user_id`=0 and `sa_id`=0";
    			}
    			 
    		}
    		if($areas=trim($srow['areas']))
    		{
    			$condition.=" AND `areas` = '$areas'";
    		}
    		if($cmp_name=trim($srow['cmp_name']))
    		{
    			$condition.=" AND `cmp_name` like '%$cmp_name%'";
    		}
    		if($link_man=trim($srow['link_man']))
    		{
    			$condition.=" AND `link_man` = '$link_man'";
    		}
    		if($tel=trim($srow['tel']))
    		{
    			$condition.=" AND `tel` = '$tel'";
    		}
    		if($audit_state=trim($srow['audit_state']))
    		{
    			$condition.=" AND `audit_state` = '$audit_state'";
    		}
    
    	}
    
    	$data=$this->getList($condition,array('*'),array('dateline'=>'desc'),$page,$limit=30,true);
    	$this->_PK=$old_pk;
    	$this->_tableName=$old_tablename;
    	return $data;
    }
    
    /**
     *
     * 视图查询审核数据
     * @param string
     * @return int|false
     */
    
    public function getListCheck($page,$id)
    {
    	$old_pk=$this->_PK;
    	$old_tablename=$this->_tableName;
    	$this->_PK = 'id';
    	$this->_tableName = 'v_agentcomments';
    
    	$condition ="`id`=$id";  //查询条件
    	
    
    	$data=$this->getList($condition,array('*'),array('dateline'=>'desc'),$page,$limit=10,true);
    	$this->_PK=$old_pk;
    	$this->_tableName=$old_tablename;
    	return $data;
    }
    
    
    
    
}
/**
    End file,Don't add ?> after this.
*/
