<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_cmp_company extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'cmp_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'cmp_company'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    //通过seller_id 找到相关公司
    
    public function getListAllsellercmp($page,$id){
        
        $id=intval($id);
        $condition = "`seller_id`={$id} ";          
        $field = array('cmp_id');        
        return  $this->getList($condition,$field, null, $page, $limit, true );
        
        
        
    }
      
    
    
      //获取0-50000网站地图网址
     public function getSitemapAll($page, $limit = 50000){      
        //默认条件
        $condition = "`is_del`=0 AND `audit_state` in(2,4) ";          
        $field = array('cmp_id','timestamp');        
        return  $this->getList($condition,$field, array('timestamp'=>'desc'), $page, $limit, true );
    }
    
        //获取0-50000网站地图网址
     public function getzhaoshangAll($page, $limit = 50000){      
        //默认条件
        $condition = "`page_url` is not null ";          
        $field = array('page_url','timestamp');        
        return  $this->getList($condition,$field, array('timestamp'=>'desc'), $page, $limit, true );
    }
    
    /**
     * 获取前端列表
     * @param unknown $page
     * @return Ambigous <multitype:, false, multitype:NULL multitype: >
     */
   public function getPreListAll($page, $srow, $limit = 10){
           
         $condition =1;
         
        if(count($srow)>0){
            if($main_type=trim($srow['main_type'])){               
                    $condition.=" AND `main_type` = '$main_type'";               
            }
            if($city_name=trim($srow['city_name'])){
                $condition.=" AND `city_name` LIKE '%{$city_name}%'";
            }
            if($k=trim($srow['k'])){
                $condition.=" AND `cmp_name` like '%$k%'";
            }   
            if($srow['audit_state']){   
               $condition.=" AND audit_state in (2,4)";
            }
        }                
        //字段
        $field = array(
        
                'link_man',
                'telephone',
                'cmp_type',               
                'start_time',
                'cmp_id',
                'cmp_name',
                'cmp_addr',
                'main_products',
                'cmp_lv',
                'cmp_img',
        
        
        );           
        return  $this->getList($condition,array('*'), array('cmp_type'=>'desc','cmp_lv'=>'desc','start_time_temp'=>'desc'), $page, $limit, true ,$leftjoin);
    }
    
    /**
    * 得到广告列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page, $srow='', $limit=30)
    {
    	$this->_tableName = 'cmp_company as C'; //设置表名
    	$leftjoin=',user_users as U';
    	
        //默认条件
        $condition = "C.user_id=U.user_id";
        
        //字段
        $field = array(	
        		'U.user_name',
        		'U.can_login',
        		'U.login_ect',
                'U.reg_time',
        		'C.cmp_id',
        		'C.cmp_name',
        		'C.main_products',
        		'C.main_type',        		
        		'C.start_time',
        		'C.end_time',
        		'C.cmp_type',
        		'C.money',
        		'C.phone_api_id',
        		'C.seller_id',
        		'C.static_url',
        		'C.audit_state',
        		'C.cmp_intro',
        		'C.cmp_addr',
        		'C.logo_src',
        		'C.licence_pic',
        		'C.link_man',
        		'C.city_name',
        		'C.postcode',
        		'C.telephone',
        		'C.mobile',
        		'C.fax',
				'C.is_check',
				'cmp_lv',
        		'web_url',
        		'cmp_kind',
        		'enrol_fund',
        		'build_date',
        		'cmp_img',
        		'cmp_integral',

        		
        );
        
        if ( $srow != '' ){
        	
        	//组合查询字符串
        	foreach ( $srow as $k=>$v ){
        		if ( $k == 'way' ){
        		       		        		
        		}elseif ( $k == 'real_name' ){
        				
        			$sqlStr .= 'C.seller_id='.$v.' and ';
        				
        		}elseif ( $k == 'start_time' ){
        				
        			$sqlStr .= 'C.end_time>='.$v.' and ';
        				
        		}elseif ( $k == 'end_time' ){
        				
        			$sqlStr .= 'C.end_time<='.$v.' and ';
        				
        		}elseif ($k == 'cmp_type' && $v == 1){
        			$sqlStr .= 'C.cmp_type !=6 and ';
        				
        		}elseif ($k == 'cmp_lv'){
        			$sqlStr .= 'C.cmp_lv ='.$v.' and ';
        				
        		}elseif ($k == 'audit_state'){
        			$sqlStr .= 'C.audit_state ='.$v.' and ';
        				
        		}elseif ($k == 'phone_api_status'){
        			$sqlStr .= 'C.phone_api_status ='.$v.' and ';
        				
        		}elseif ($k == 'ischeck'){
					if($v == 2){
						$sqlStr .= 'C.is_check = 0 and ';
					}else if($v){
        				$sqlStr .= 'C.is_check ='.$v.' and ';
					}
        				
        		}elseif( $k == 'C|user_name' || $k == 'C|cmp_name' ){
        	
        			$sqlStr .= str_replace('|', '.', $k)." like '%{$v}%' and ";
        	
        		}elseif ( $k == 'money_grade' ){
        			
	        		// ============================ 费用等级 =================================//
						if ( $v == 1 ){
							
							$sqlStr .= 'C.money=0 and ';
							
						}elseif ( $v == 2 ){
							
							$sqlStr .= 'C.money>=1 and C.money<=4999 and ';
							
						}elseif ( $v == 3 ){
							
							$sqlStr .= 'C.money>4999 and C.money<=9999 and ';
							
						}elseif ( $v == 4 ){
							
							$sqlStr .= 'C.money>9999 and C.money<=19999 and ';
							
						}elseif ( $v == 5 ){
							
							$sqlStr .= 'C.money>=20000 and ';
							
						}else{
							
							$v['money'] = '';
							
						}
					// ============================ 费用等级 =================================//
        		}else{
        				
        			$sqlStr .= str_replace('|', '.', $k)."='{$v}' and ";
        				
        		}
        	
        	
        	}
        	//die($sqlStr.$condition);
        	//return  $this->getList($sqlStr.$condition,array('*'), array('cmp_id'=>'desc'), $page, $limit, true ,$leftjoin);
        	if($srow['way']==1){
        		return  $this->getList($sqlStr.$condition,$field, array('seller_id'=>'asc','cmp_id'=>'asc'), $page, $limit, true ,$leftjoin);
        	}elseif($srow['way']==2){
        		return  $this->getList($sqlStr.$condition,$field, array('seller_id'=>'asc','cmp_id'=>'desc'), $page, $limit, true ,$leftjoin);
        	}else{
        	    return  $this->getList($sqlStr.$condition,$field, array('seller_id'=>'asc','cmp_id'=>'desc'), $page, $limit, true ,$leftjoin);
        	}       	       
        	
        }else{
        	
        	//return $this->getList($condition,array('*'), array('cmp_id'=>'desc'), $page, $limit, true ,$leftjoin);
        	return $this->getList($condition,$field, array('cmp_id'=>'desc'), $page, $limit, true ,$leftjoin);
        	
        }
    }
    
    
    public function updateReTable($id, $data)
    {
        if(!$data){return false;}
        if(!$id=intval($id)){return false;}

        unset($row[$this->_PK]);
        return $this->update(array($this->_PK=>$id),$row);
        //return $this->delete(array($this->_PK=>$id));
    }
    
    /*
    public function updateReTable($id, $data){
    	
    	$this->_tableName = 'cmp_company as C,user_users as U'; //设置表名
    	
    	//条件
    	$condition = 'C.user_id=U.user_id and C.cmp_id='.$id;
    	
    	return $this->update($condition, $data);
    	
    }
    */
    

    public function getRowById($id,$fields='*')
    {
    	if(!$id=intval($id)){return false;}
    	return $this->getOne(array($this->_PK=>$id),$fields);
    }
    
    
    
    
    
    /**
    * 得到一条记录（根据企业名称）
    * 
    * @param int $id
    * @return array|false
    */
    public function getRowByCmpName($cname)
    {
        if(!$cname=trim($cname)){return false;}
        return $this->getOne("cmp_name='{$cname}'");
    }
    
    /**
    * 得到一条记录（根据用户id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getRowByUserId($uid)
    {
        if(!$uid=intval($uid)){return false;}
        return $this->getOne("user_id='{$uid}'");
    }
    
    
    /**
    * 得到公司性质
    */
    public function getKinds()
    {
        return array(1=>'个体',2=>'国有',3=>'合资',4=>'外资',5=>'私营',6=>'股份公司');
    }
    
    /**
    * 得到企业性质(根据下标/键)
    * 
    * @param int $k
    * @return string
    */
    public function getKindByKey($k)
    {
        $ks=$this->getKinds();
        return $ks[$k];
    }
    
    

    /**
     * 审核处理方法
     */
    public function updateAudit($data,$audit=''){
    		
    	if ( is_array($data) ){//多选
    			
    		$sqlStr = implode($data, ',');
    			
    		//条件
    		$condition = $this->_PK." in(".$sqlStr.")";
    			
    		return $this->update($condition, 'audit_state='.$audit);
    			
    	}else{
    			
    		//条件
    		$condition = $this->_PK.'='.$data;
    			
    		return $this->update($condition, 'audit_state='.$audit);
    	}
    		
    }

    /**
    * 得到企业名称（根据企业id）
    * 
    * @param int $cid
    * @return array|false
    */
    public function getNameById($cid)
    {
        if(!$cid=intval($cid)){return false;}
        $row= $this->getOne("{$this->_PK}={$cid}",'cmp_name');
        return $row['cmp_name']; 
    }

    
    /**
     * 查询公司
     * @param unknown $id
     * @return Ambigous <multitype:, false, boolean>
     */
    public function checkCmp($id)
    {
        if(!$id=intval($id)){return false;}
    	return $this->getOne("cmp_id=".$id);
    }
    
    /**
    * 得到数据集
    * add by baoxianjian 21:55 2015/4/28
    * @param array $srow bgid
    * @return array
    */
    public function getRowsetAll($srow=array(),$count=10)
    {
        $condition='is_del=0 AND audit_state IN(2,4)';
        $order = " cmp_id DESC";
        
        if($srow['fee']) //收费
        {
            $condition.=" AND cmp_type=6 "; 
        }
        if($srow['new']) //最新
        {
            //$order=" cmp_id DESC";    
        }
        if($srow['hot'])  //最热
        {
           $order = "click_count DESC, ".$order;
        }
        if($ids=trim($srow['ids'],','))
        {
            $condition.=" AND cmp_id IN ({$ids})";
            $count=100;  //指定id后 count失效
            $order=''; //指定id后 排序无效    
        }
        
        $fields="cmp_id,cmp_name,slogan,static_url,cmp_type";
        
        if($order)
        {
            $order_str=' ORDER BY '.$order;
        }
        $data=$this->getList($condition,$fields,$order_str,1,$count,0); 
        
        return $data['list']; 
    }
	
    
	public function getLvdata(){
		//day 多少天以前数据 0:不能查看
		//period 范围 0:不能查看
		$data = array(
			'free' => array(
				//普通厂商
				1 =>array('day'=>0, 'scope'=>0),
				//铜牌
				2 =>array('day'=>0, 'scope'=>0),
				//银牌
				3 =>array('day'=>0, 'scope'=>0),
				//金牌
				4 =>array('day'=>0, 'scope'=>0),
				//钻石
				5 =>array('day'=>0, 'scope'=>0)
			),
			'vip' => array(
				//普通收费厂商
				1 =>array('day'=>10, 'scope'=>3),
				//铜牌
				2 =>array('day'=>5, 'scope'=>3),
				//银牌
				3 =>array('day'=>3, 'scope'=>3),
				//金牌
				4 =>array('day'=>2, 'scope'=>3),
				//钻石
				5 =>array('day'=>1, 'scope'=>3)
			)
		);
		return $data;
	}
   
   /**
   * 得到（收费）厂商的等级列表
   *  
   */
   public function getCmpLvlist()
   {
       return array("1"=>"普通","2"=>"铜牌","3"=>"银牌","4"=>"金牌","5"=>"钻石");
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
        
        $condition="cmp_id IN({$ids})";
        
         
        $fields="cmp_id,cmp_name";
        $data=$this->getList($condition,$fields,' ORDER BY '.$this->_PK.' ASC',1,1000,0); 
        return $data['list'];
    }
}
