<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/

class Model_pdt_products extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'pdt_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'pdt_products_temp'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    //获取已经刷新的数据
    
    public function getListAllfre($page,$cid,$cvip){
        
        if(!$cid=intval($cid)){return false;}
        $time=NOW;       
        if($cvip){
              $condition = "`is_del`=0 AND `cmp_id`={$cid} AND `audit_state`=2 And `recruit_state`=1 And `pdt_date`>`dateline` And '{$time}'<`pdt_date`+3600 ";               
        }else{
              $condition = "`is_del`=0 AND `cmp_id`={$cid} AND `audit_state`=2 And `recruit_state`=1 And `pdt_date`>`dateline` And '{$time}'<`pdt_date`+3600*5 ";   
        }
             
        $field = array('pdt_id','pdt_date','dateline');        
        return  $this->getList($condition,$field, array('pdt_id'=>'desc'), $page, $limit, true );
                
    }
      
    
    public function searchListAll($page,$srow,$limit=10)
    {
        $q='audit_state:2';
       if(count($srow)>0)
       {
            if($type2_id=trim($srow['t2']))
            {
               $q.=" AND type2_id:{$type2_id}";
            }
            elseif($type1_id=trim($srow['t1']))
            {
                 $q.=" AND type1_id:{$type1_id}";
            }
                       
             if($area=trim($srow['area']))
             {
               $q.=" AND area:{$area}";
             }
             if($medicament_type=trim($srow['mdt']))
             {
                  $q.=" AND medicament_type:{$medicament_type}";
             }
             if($k=trim($srow['k']))
             {
                 $q.=" AND name:{$k}";
             }

             if($f=intval($srow['f']))
             {
                  if($f==1){
                      $fq.=" AND medicare_type:1";
                  }elseif($f==2){
                      $q.=" AND zb_type:1 ";
                  }elseif ($f==3){
                      $q.=" AND  base_medicine:1";    //无
                  }elseif ($f==4){
                      $q.=" AND  tcm_protection:1";      //无
                  }elseif ($f==5){
                      $fq.=" AND patent_code:1";
                  }                                                
              }

            $q=ltrim($q,' AND');
            $fq=ltrim($fq,' AND');
        }
        if(!$q)
        {
            $q="*:*";
        }

        $sort="cmp_type desc, pdt_date desc";
        
        $q=urlencode($q);
        $fq=urlencode($fq);
        $sort=urlencode($sort);
        
        $qstr="q={$q}&fq={$fq}&sort={$sort}";

        return $this->searchList($page,'productall/select',$qstr,12);
    }
    
      
      //获取0-50000网站地图网址
     public function getSitemapAll($page, $limit = 50000){      
        //默认条件
        $condition = "`is_del`=0 AND `audit_state` in(2,4)";          
        $field = array('pdt_id','timestamp');        
        return  $this->getList($condition,$field, array('timestamp'=>'desc'), $page, $limit, true );
    }
    
    /**
     * 得到广告列表所有
     *
     * @param mixed $page
     * @return array|false
     */
   
   public function getListAllpdt($page,$srow)
   {
    //	define('DEBUG',1);
    	
   // 	$this->_tableName = ' pdt_products_temp as P,cmp_company as C '; //设置表名    
   //    $condition ="P.cmp_id=C.cmp_id"; //查询条件
    	$condition =1;
       if(count($srow)>0)
       {
       	   if($type2_id=intval($srow['type2_id'])){
       		$condition.=" AND `type2_id` = '$type2_id'";
         	}elseif($type1_id=intval($srow['type1_id'])){
         		$condition.=" AND `type1_id` = '$type1_id'";
         	}
         	      	
         	if($area=trim($srow['area'])){
       		$condition.=" AND `area` ='$area'";
       	   }
       	   if($medicament_type=$srow['medicament_type']){
       	   	$condition.=" AND `medicament_type` ='$medicament_type'";
       	   }
       	   if($k=trim($srow['k'])){
       	   	$condition.=" AND `name` like '%$k%'";
       	   }
       	   if($f=$srow['f']){
       	   	    if($f==1){
       	   	    	$condition.=" AND `medicare_type` !=1 ";
       	   	    }elseif($f==2){
       	   	    	$condition.=" AND `zb_type` =1 ";
       	   	    }elseif ($f==3){
       	   	    	$condition.=" AND `base_medicine` =1 ";
       	   	    }elseif ($f==4){
       	   	    	$condition.=" AND `tcm_protection` =1 ";
       	   	    }elseif ($f==5){
       	   	    	$condition.=" AND `patent_code` !='' ";
       	   	    } 
       	   	         	   	    	   	       	   
       	   }
       	  
       	   
       	   
       }
  
     //  $this->_tableName="pdt_products_temp pp";
     //  $leftjoin="left join cmp_company cc ON pp.cmp_id=cc.cmp_id ";
    //   $leftjoin=array('cmt_comments cc'=>'cc.cmp_id=pp.cmp_id');
      
    //   $data=$this->getList($condition,"cc.*,pp.*",array('cmp_type'=>'desc','cmp_lv'=>'desc','pdt_date'=>'desc'),$page,10,true,$leftjoin);
      
    //   return $data;
    
     
  //  return $this->getList($condition,array('*'),array('cmp_type'=>'desc','cmp_lv'=>'desc','pdt_date'=>'desc'),$page,$limit=12,true);
    return $this->getList($condition,array('*'),array('dateline'=>'desc'),$page,$limit=10,true);
    }
    	
    /**
     * 
     * 得到公司下面的热门产品
     * @param mixed $page
     * @return array|false
     */
     
    public function getListAllcmppdt($page,$id){
    	
        if(!$id=intval($id)) {return false;}
    	$condition ="`cmp_id`={$id} AND `is_del`=0 AND `audit_state` in(2,4) "; //查询条件
    	
    	return $this->getList($condition,array('*'),array('click_count'=>'desc'),$page,$limit=4,true);
    }
    
    
    /**
     *
     * 得到公司下面的产品展示
     * @param mixed $page
     * @return array|false
     */
     
    public function getListAllpdtpaly($page,$id){
    	if(!$id=intval($id)){return false;}
                 
    	$condition ="`cmp_id`={$id} AND `audit_state` in(2,4) AND `is_del`=0 "; //查询条件
    	 
    	return $this->getList($condition,array('*'),array('order'=>'desc','pdt_id'=>'desc'),$page,$limit=9,true);
    }
    
    /**
     *
     * 得到公司下面的最新产品
     * @param mixed $page
     * @return array|false
     */
     
    public function getListAllnewpdt($page,$cmp_id,$pdt_id){
    	if(!$cmp_id=intval($cmp_id)){return false;} 
        //if(!$pdt_id=intval($pdt_id)){return false;}  
         
    	//$condition ="`cmp_id`={$cmp_id} AND `pdt_id`!='$pdt_id'"; //查询条件
        $condition ="`cmp_id`={$cmp_id}"; //查询条件
    	 
    	return $this->getList($condition,array('*'),array('pdt_id'=>'desc'),$page,$limit=11,true);
    }
    
    /**
     *
     * 得到公司下面相同类型的14款产品
     * @param mixed $page
     * @return array|false
     */
     
    public function getListAlltypepdt($page,$type,$cmp_id){
        if(!intval($type)){return false;}
        if(!intval($cmp_id)){return false;}
    	$condition ="`type1_id`={$type} AND `cmp_id`={$cmp_id}"; //查询条件
    
    	return $this->getList($condition,array('*'),array('pdt_id'=>'desc'),$page,$limit=14,true);
    }
    /**
     *
     * 得到产品列表下的产品
     * @param mixed $page
     * @return array|false
     */
   public function getListAlllistpdt($page,$srow){
    	 
   		$this->_tableName = ' pdt_products_temp P'; //设置表名

        $fields="P.pdt_id,P.cmp_id,P.`name`,P.`pdt_date`,P.`is_del`,P.`audit_state`,P.`recruit_state`,P.`order`,C.`cmp_name`,C.`cmp_type`";
      // pt.pt_name AS type_name1,pt2.pt_name AS type_name2";
        
        $leftjoin="LEFT JOIN cmp_company C ON P.cmp_id=C.cmp_id";
      
   //	$condition =1; //查询条件
    	if(count($srow)>0)
    	{
    		if($pdt_id=intval($srow['pdt_id'])){
    			$condition.=" AND `pdt_id` = '$pdt_id'";
    		}
    		if($name=trim($srow['name'])){
    			$condition.=" AND `name` like '%$name%'";
    		}   		    		 
    		if($start_time=strtotime($srow['start_time'])){
    			$condition.=" AND `pdt_date` >='$start_time'";
    		}
    		if($end_time=strtotime($srow['end_time'])){
    			$condition.=" AND `pdt_date` <='$end_time'";
    		}
    		if($cmp_id=intval($srow['cmp_id'])){
    			$condition.=" AND C.`cmp_id`='$cmp_id'";
    		}
    		if($cmp_name=trim($srow['cmp_name'])){
    			$condition.=" AND C.`cmp_name` like '%$cmp_name%'";
    		}
    		if($audit_state=intval($srow['audit_state'])){
    			$condition.=" AND P.`audit_state`='$audit_state'";
    		}
    		if($is_del=intval($srow['is_del'])){
    			if($is_del==1){
    			  $condition.=" AND P.`is_del`=0 AND P.`recruit_state`=1 ";
    			}elseif($is_del==2){
    			  $condition.=" AND P.`is_del`=0 AND P.`recruit_state`=2 ";
    			}elseif($is_del==3){
    			  $condition.=" AND P.`is_del`=1";
    			}
    		}
    		
      }
      

      
      $condition=ltrim($condition,' AND');
      $condition=trim($condition,' ');
    	 	
    	return $this->getList($condition,$fields,array('pdt_id'=>'desc'),$page,$limit=30,true,$leftjoin);
     // return $this->getList($condition,array('*'),array('pdt_date'=>'desc'),$page,$limit=30,true);
    } 
      
    /**
     *
     * 得到厂商会员中心产品列表下的产品
     * @param mixed $page
     * @return array|false
     */
    public function getListAllcmplist($page,$srow,$cmp_id){
    
        
    	$this->_tableName = ' pdt_products_temp as P,cmp_company as C '; //设置表名
    	$condition ="P.cmp_id=C.cmp_id AND P.is_del=0 ";
    	$fields="P.pdt_id,P.cmp_id,P.`name`,P.`pdt_date`,P.`dateline`,P.`is_del`,P.`click_count`,P.`leave_msg`,P.`audit_state`,P.`recruit_state`,P.`order`,C.`cmp_name`,C.`cmp_type`";
    	// pt.pt_name AS type_name1,pt2.pt_name AS type_name2";
    	//	$condition =1; //查询条件
    	if(count($srow)>0)
    	{
    		if($pdt_name=trim($srow['pdt_name'])){
    			$condition.=" AND `name` like '%$pdt_name%'";
    		}    		   		
    		if($audit_state=intval($srow['audit_state'])){
    			$condition.=" AND P.`audit_state`='$audit_state'";
    		} 
    		if($recruit_state=intval($srow['recruit_state'])){
    			$condition.=" AND `recruit_state`='$recruit_state'";
    		}   		

    
    	}
        if($cmp_id=intval($cmp_id)){
                $condition.=" AND C.cmp_id={$cmp_id}";  
            } 
        
    	 
    	return $this->getList($condition,$fields,array('pdt_date'=>'desc'),$page,$limit=14,true,$leftjoin);
    	// return $this->getList($condition,array('*'),array('pdt_date'=>'desc'),$page,$limit=30,true);
    }
    /**
    * 得到广告列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page='',$seek='', $limit = 30, $order = ' ORDER BY PRC.order DESC,PRC.pdt_id DESC')
    {
        if(!$limit = intval($limit))
        {
             $limit = 30;
        }
    	
    	$this->_tableName='pdt_products_temp as PRC';//产品表
		//$this->_tableName = 'pdt_products';
    	$leftjoin=',cmp_company as CMP';
    	
    	//条件
    	$condition = "PRC.cmp_id=CMP.cmp_id";
    	
    	//字段
    	$field = "PRC.pdt_id,PRC.cmp_id,PRC.name,PRC.pdt_date,CMP.cmp_name,PRC.audit_state,PRC.slogan,PRC.is_del,PRC.order,PRC.img,CMP.audit_state as CMP_audit,CMP.cmp_type,PRC.static_url";
		//$field = "*";
    	
    	if ( $seek == '' ){
			//die($condition);
    		return $this->getList($condition,$field, $order, $page, $limit, true, $leftjoin);
    		//return $this->getList($condition,$field, '', $page, $limit, true);
    	}else{
			
    	//	die(SUtil::P($seek));
    		//组合查询字符串
    		foreach ( $seek as $k=>$v ){
    			 
    			if ( $k == 'PRC|start_time' ){
    				 
    				$sqlStr .= 'PRC.pdt_date>='.$v.' and ';
    				 
    			}elseif ( $k == 'PRC|end_time' ){
    				 
    				$sqlStr .= 'PRC.pdt_date<='.$v.' and ';
    				 
    			}elseif( $k == 'PRC|name' || $k == 'CMP|cmp_name' ){
    				
    				$sqlStr .= str_replace('|', '.', $k)." like '%{$v}%' and ";
    				
    			}else{
    				 
    				$sqlStr .= str_replace('|', '.', $k)."='{$v}' and ";
    				 
    			}
    			
    /*			
    			if($seek['PRC|is_del']==1){
    				$sqlStr .= 'PRC.is_del=1 and ';
    			}elseif($seek['PRC|is_del']==2){
    				$sqlStr .= 'PRC.is_del=0 and ';
    			}
    			
    */			
    			 
    			 
    		}
    		
    		return $this->getList($sqlStr.$condition,$field,' ORDER BY PRC.pdt_id DESC',$page, $limit,true,$leftjoin);
			//return $this->getList($sqlStr.$condition,$field,'',$page,10,true,$leftjoin);
    	
    	}
       
    }
    
  
    
    /**
     * 得到广告列表所有
     *
     * @param mixed $page
     * @return array|false
     */
    public function getFrontList($id, $page='', $seek='')
    {
    	$this->_tableName='pdt_products_temp as PRC';//产品表
    	$leftjoin=',cmp_company as CMP';
    
    	//条件
    	$condition = "PRC.cmp_id=CMP.cmp_id and PRC.is_del=0 and PRC.cmp_id=".$id;
     	//字段
    	$field = "PRC.pdt_id,PRC.cmp_id,PRC.name,PRC.pdt_date,CMP.cmp_name,PRC.audit_state,PRC.recruit_state,PRC.slogan,PRC.is_del,PRC.order,CMP.audit_state as CMP_audit,CMP.cmp_type,PRC.static_url,PRC.leave_msg,PRC.click_count";
    
        //$leftjoin
    
    	if ( $seek == '' ){
    		
    		return $this->getList($condition,$field,' ORDER BY PRC.order DESC,PRC.pdt_id DESC',$page,14,true,$leftjoin);
    
    	}else{
    
    		//组合查询字符串
    		foreach ( $seek as $k=>$v ){
    
    			if ( $k == 'PRC|start_time' ){
    
    				$sqlStr .= 'PRC.pdt_date>='.strtotime($v).' and ';
    
    			}elseif ( $k == 'PRC|end_time' ){
    
    				$sqlStr .= 'PRC.pdt_date<='.strtotime($v).' and ';
    
    			}elseif( $k == 'PRC|is_del' ){
    				
    				$sqlStr .= str_replace('|', '.', $k)."='{$v}' and PRC.audit_state=2 and ";
    				
    				
    			}elseif ( $k == 'PRC|name' ){
    
    				$sqlStr .= str_replace('|', '.', $k)." like '%{$v}%' and ";
    
    			}else{
    				
    				$sqlStr .= str_replace('|', '.', $k)."='{$v}' and ";
    				
    			}
    
    
    		}
    	//	die($sqlStr);
    
    		return $this->getList($sqlStr.$condition,$field,'ORDER BY PRC.pdt_date DESC',$page,14,true,$leftjoin);
    
    	}
   
    }
    
    /**
     * 获取编辑信息
     * @param unknown $id
     * @return Ambigous <multitype:, false, boolean>
     */
	public function getOneById($id){
		if(!intval($id)){return false;}
		$this->_tableName='pdt_products_temp as PRC';//产品表
    	
    	//条件
    	$condition = "PRC.pdt_id=".$id;
    	
    	//字段
    	$field = "PRC.order,PRC.type1_id,PRC.type2_id,PRC.pdt_id,PRC.cmp_id,PRC.img,PRC.name,PRC.selling_points,PRC.web_url,PRC.small_img,PRC.medicare_type,PRC.zb_type,PRC.confirm_code,PRC.patent_code,PRC.area,PRC.medicament_type,PRC.spec,PRC.component,PRC.usage,PRC.function,PRC.producer,PRC.supply_term,PRC.offer,PRC.remark,PRC.link_man,PRC.link_tel,PRC.link_mp,PRC.link_qq,PRC.link_email,PRC.link_address,PRC.link_fax,PRC.link_post,PRC.zb_area,PRC.label,PRC.slogan,PRC.link_cmp_name";
    	
    	return $this->getOne($condition,$field);
		
	}
	
	/**
	 * 编辑处理
	 * @param unknown $id
	 * @param unknown $data
	 * @return Ambigous <number, false, boolean>
	 */
	public function updateById($id, $data){
		
        	if(!intval($id)){return false;}
		$this->_tableName='pdt_products_temp';
		
		$condition = $this->_PK."=".$id;
		
		return $this->update($condition, $data);
		
	}
	
	/**
	 * 发布产品处理
	 * @param unknown $data
	 * @return Ambigous <number, false, boolean, multitype:>
	 */
	public function add($data){
		
		$this->_tableName='pdt_products_temp';
		
		return $this->insert($data);
		
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
	
	/**
	 * 恢复处理方法
	 * @param unknown $data
	 * @return Ambigous <number, false, boolean>
	 */
	public function renew($data){
			
		if ( is_array($data) ){//多选
	
			$delStr = implode($data, ',');
			//die($delStr);
			return $this->update($this->_PK." in (".$delStr.")", 'is_del=0');
	
		}else{//单选
	
			return $this->update($this->_PK."=".$data, "is_del=0");
	
		}
	}
	
	
	
	/**
	 * 审核处理方法
	 */
	public function audit($data,$audit=''){
		 
		if ( is_array($data) ){//多选
			
			$this->_tableName='pdt_products_temp as PRC,cmp_company as CMP';//产品表
			
			$sqlStr = implode($data, ',');
			
			//条件
			$condition = "PRC.pdt_id in(".$sqlStr.") and PRC.cmp_id=CMP.cmp_id and CMP.audit_state=2";
			
			return $this->update($condition, 'PRC.audit_state='.$audit);
			
		}else{
			
			//条件
			$condition = $this->_PK.'='.$data;
			
			return $this->update($condition, 'audit_state='.$audit);
		}
		 
	}
	
	/**
	 * 获取公司审核状态
	 */
	public function cmpAudit($id){

		$this->_tableName='pdt_products_temp as PRC';//产品表
		$leftjoin=',cmp_company as CMP';
		
		if ( is_array($id) ){
			
			$this->_tableName='pdt_products_temp as PRC,cmp_company as CMP';//产品表
			
			$sqlStr = implode( ',',$id);
			//条件
			$condition = "PRC.pdt_id in (".$sqlStr.") and PRC.cmp_id=CMP.cmp_id and CMP.audit_state=2";
			
			$field = 'CMP.cmp_id,CMP.audit_state,PRC.pdt_id';
			
		
			
			return $this->getList($condition,$field);
			
		}else{
				
			//条件
			$condition = "PRC.pdt_id=".$id." and PRC.cmp_id=CMP.cmp_id and CMP.audit_state=2";
			
			$field = 'CMP.cmp_id,CMP.audit_state,PRC.pdt_id';
			
			return $this->getOne($condition,$field,$leftjoin);
			
		}
		
	}
	
	/**
	 * 获取公司联系人
	 * @param unknown $id
	 * @return Ambigous <multitype:, false, boolean>
	 */
	public function getCmpLink($id){
		
		$this->_tableName='pdt_products_temp as PRC';//产品表
		$leftjoin=',cmp_company as CMP';
		
		//条件
		$condition = "PRC.pdt_id=".$id." and PRC.cmp_id=CMP.cmp_id";
		
		$field = 'CMP.link_man,CMP.mobile,CMP.fax,CMP.web_url,CMP.cmp_addr,CMP.cmp_email,CMP.cmp_name';
		
		return $this->getOne($condition,$field,$leftjoin);
		
	}
	

	
	
	/**
	 * 查询公司
	 * @param unknown $id
	 * @return Ambigous <multitype:, false, boolean>
	 */
	public function checkCmp($id){
		
		return $this->getOne("cmp_id=".$id);
		
	}
    
	
	public function refData($data){
		
		if ( is_array($data) ){//多选
		
			$delStr = implode($data, ',');
			//die($delStr);
			return $this->update($this->_PK." in (".$delStr.") and audit_state=2", 'pdt_date='.time());
		
		}else{//单选
		
			return $this->update($this->_PK."=".$data.' and audit_state=2', "pdt_date=".time());
		
		}
		
	}
	
	/**
	 * 开始招商
	 * @param unknown $data
	 */
	public function attractStart($data){
	
		if ( is_array($data) ){//多选
	
			$delStr = implode($data, ',');
			//die($delStr);
			return $this->update($this->_PK." in (".$delStr.") and audit_state=2", 'is_del=2');
	
		}else{//单选
	
			return $this->update($this->_PK."=".$data.' and audit_state=2', "is_del=2");
	
		}
	
	}
	
	/**
	 * 结束招商
	 * @param unknown $data
	 * @return Ambigous <number, false, boolean>
	 */
	public function attractEnd($data){
	
		if ( is_array($data) ){//多选
	
			$delStr = implode($data, ',');
			//die($delStr);
			return $this->update($this->_PK." in (".$delStr.") and audit_state=2", 'is_del=3');
	
		}else{//单选
	
			return $this->update($this->_PK."=".$data.' and audit_state=2', "is_del=3");
	
		}
	
	}
    
    
    
    /**
    * 得到企业名称（根据企业id）
    * 
    * @param int $cid
    * @return array|false
    */
    public function getNameById($pid)
    {
        if(!$pid=intval($pid)){return false;}
        $row= $this->getOne("{$this->_PK}={$pid}",'name');
        return $row['name'];  
    }
/*    
    public function getMedicamentTypes()
    {
    	
    	return array(
    			
    			1=>'胶囊',
    			2=>'栓剂',
    			3=>'冲剂',
    			4=>'片剂',
    			5=>'丸剂',
    			6=>'油剂',
    			7=>'糖浆剂',
    			8=>'口服液',
    			9=>'注射剂',
    			10=>'膏剂',
    			11=>'凝胶剂',
    			12=>'散剂',
    			13=>'贴敷剂',
    			14=>'霜剂',
    			15=>'粉剂',
    			16=>'洗液',
    			17=>'喷剂',
    			18=>'搽剂',
    			19=>'干混悬剂',
    			20=>'走珠器',
    			21=>'滴剂'
    			
    	);
    }
    */
    
 
    /**
    * 得到产品数据集 (根据企业id)
    * 
    * @param int $cid
    * @param array $srow
    * @param int $count
    * @return array
    */
    public function getRowsetByCmpId($cid,$srow=array(),$count=5)
    {
        if(!$cid=intval($cid)){return false;}
        
        $condition.=" is_del=0 AND cmp_id={$cid} ";
        $fields="pdt_id,static_url,img,`name`";
        
        $data=$this->getList($condition,$fields," ORDER BY `order` desc,pdt_id desc",1,$count,0,$leftjoin);               
        return $data['list'];   
    }
    
    /**
    * 得到数据集
    * 
    * @param array $srow
    * @param int $count
    * @return mixed
    */
    public function getRowsetAll($srow=array(),$count=10)
    {
        $condition='p.is_del=0 AND p.audit_state IN(2,4)';  
        $order = " p.pdt_id DESC"; 
        
        $old_tn=$this->_tableName;
        $this->_tableName="pdt_products p";
        
        $fields="p.pdt_id,p.cmp_id,p.`name`, p.static_url, p.img, p.type1_id, p.type2_id, p.type3_id, p.click_count, p.dateline,
                 pt.pt_name AS type_name1,pt2.pt_name AS type_name2";
        
        $leftjoin="LEFT JOIN pdt_types pt ON p.type2_id=pt.pt_id
                   LEFT JOIN pdt_types pt2 ON p.type3_id=pt2.pt_id";

        if($srow['fee']) //收费
        {
            $leftjoin.=" INNER JOIN cmp_company c ON p.cmp_id=c.cmp_id AND c.cmp_type=6 ";
        }
        if($srow['new']) //最新
        {
            //$order=" cmp_id DESC";    
        }
        if($srow['hot'])  //最热
        {
           $order = "p.click_count DESC, ".$order;   
        }
        
        if($tid1=intval($srow['tid1'])) //区块中指定的分类1 对应 产品中的分类2
        {
            $condition.=" AND p.type1_id={$tid1}"; //产品中的分类1没用 分类1级
        }
        
        if($tid2=intval($srow['tid2']))
        {
            $condition.=" AND p.type2_id={$tid2}";  //分类2级       
        }
        
        if($ids=trim($srow['ids'],','))
        {
            $condition.=" AND p.pdt_id IN ({$ids})";
            $count=100;  //指定id后 count失效
            $order=''; //指定id后 排序无效    
        }
        if($order)
        {
            $order_str=' ORDER BY '.$order;
        }
        $data=$this->getList($condition,$fields,$order_str,1,$count,0,$leftjoin);               
        return $data['list'];   
    } 
    
    
    
    /**
    * 得到产品名称数据集（根据多个ID）
    * add by baoxianjian 9:51 2015/4/27
    * @param string $ids
    * @return mixed
    */
    public function getNamesByIds($ids)
    {
        $ids=$this->parseIds($ids);
        if(!$ids){return false;}
        
        $condition="pdt_id IN({$ids})";
        
         
        $fields="pdt_id,name";
        $data=$this->getList($condition,$fields,' ORDER BY pdt_id ASC',1,1000,0); 
        return $data['list'];
    }
    
    /**
     * 根据主键id刷新一行数据
     *
     * @param string $ids
     * @return int|false
     */
    public function freshRowByIds($ids,$destroy=false)
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
    
    	return $this->update($this->_PK." in ($ids)", array('pdt_date'=>NOW));
    	//return $this->delete(array($this->_PK=>$id));
    }
    /**
     * 开始招商
     *
     * @param string $ids
     * @return int|false
     */
    public function startRowByIds($ids,$destroy=false)
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
    
    	return $this->update($this->_PK." in ($ids)", array('recruit_state=1'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    
    /**
     * 
     * 停止招商
     * @param string $ids
     * @return int|false
     */
    public function endRowByIds($ids,$destroy=false)
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
    
    	return $this->update($this->_PK." in ($ids)", array('recruit_state=2'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    
        
    /**
     * 获取药剂类型  add by baoxianjian 16:04 2015/5/28
     * @return array
     */
    public function getMedicamentTypes(){
        return array(
        1=>'胶囊',
        2=>'栓剂',
        3=>'冲剂',
        4=>'颗粒',
        5=>'片剂',
        6=>'贴剂',
        7=>'丸剂',
        8=>'微丸剂',
        9=>'油剂',
        10=>'糖浆剂',
        11=>'口服液',
        12=>'注射剂',
        13=>'膏剂',
        14=>'凝胶剂',
        15=>'散剂',
        16=>'气雾剂',
        17=>'液体制剂',
        18=>'贴敷剂',
        19=>'软胶囊剂',
        20=>'霜剂',
        21=>'粉剂',
        22=>'注射液',
        23=>'洗液',
        24=>'枇杷膏',
        25=>'喷剂',
        26=>'搽剂',
        27=>'软膏',
        28=>'干混悬剂',
        29=>'医疗器械',
        30=>'走珠器',
        31=>'乳膏',
        32=>'胶剂',
        33=>'滴剂',
        34=>'滴眼液',
        35=>'阿胶糕',
        36=>'薄膜衣',
        37=>'凝露',
        38=>'其它',
        );

    }
    
    public function getZbTypes()
    {
        //1中标2不中3不显示
        return array(
            1=>'中标',
            2=>'不中标',
            3=>'不显示'
        );
    }
    
    public function getMedicareTypes()
    {
        //1非医保 2医保甲类型 3医保乙类型
        return array(
            1=>'非医保',
            2=>'医保甲类型',
            3=>'医保乙类型'
        );
    }
    
    
}