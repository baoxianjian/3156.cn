<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_pdt_mainProducts extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'pdt_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'pdt_products'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    
    /**
    * 得到广告列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page='',$seek='')
    {
        
    	$this->_tableName='pdt_products as P';//产品表
     	$leftjoin=',cmp_company as C,pdt_types as T';
    	
    	//条件
    	$condition = "P.cmp_id=C.cmp_id and P.type1_id=T.pt_id and P.is_del!=1 and P.audit_state=2 and C.end_time>".time();
    	
    	//字段
    	$field = "P.pdt_id,P.name,P.img,P.medicament_type,P.medicare_type,P.spec,P.area,P.zb_type,P.type2_id,T.pt_id,T.pt_name,C.cmp_name";
    	
    	return $this->getList($seek.$condition,$field,' ORDER BY P.order DESC,P.pdt_id DESC',$page,10,true,$leftjoin);

       
    }
    
    /**
     * 获取一条产品记录 通过id
     * @param unknown $id
     * @return Ambigous <multitype:, false, boolean>
     */
    public function getProductById($id){
        if(!intval($id)){return false;}
    	//条件
    	$condition = "pdt_id=".intval($id);

    	//字段
    	$field = array(
    			
    			'pdt_id',
    			'name',
    			'type1_id',
    			'type2_id',
    			'img',
    			'medicare_type',
    			'spec',
    			'component',
    			'`usage`',
    			'function',
    			'producer',
    			'area',
    			'medicament_type',
    			'supply_term',
    			'offer',
    			'patent_code',
    			'confirm_code'
    			
    	);
    	
    	
    	return $this->getOne($condition,$field);
    	
    }
    
	/**
	 * 临时表数据导入
	 * @param unknown $sqlStr
	 * @return Ambigous <multitype:, boolean>
	 */
    public function copy1($sqlStr){
    	 
    	return $this->query('pdt_products', "replace into pdt_products() select * from pdt_products_temp where pdt_id in (".$sqlStr.")");
    //	return $this->query('pdt_products', "replace into pdt_products() select * from pdt_products_temp where pdt_id in (".$sqlStr.")");
    	 
    }
	
    /**
     * 临时表数据导入
     * @param unknown $sqlStr
     * @return Ambigous <multitype:, boolean>
     */
    public function copy2($sqlStr){
    
    	return $this->query('pdt_products', "replace into pdt_products() select * from pdt_products_temp where pdt_id=".$sqlStr);
    
    }
    
	public function batchInset($id,$data){
		if(!intval($id)){return false;}
		if ( $this->getOne($this->_PK.'='.$id) != NULL ){
			
			unset($data['pdt_id']);
			return $this->update($this->_PK."=".$id, $data);
			
		}else{
			
			return $this->insert($data);
			
		}
		
	}

   
	/**
	 * 获取公司关联产品数据
	 * @return Ambigous <multitype:, false, multitype:NULL multitype: >
	 */
	public function associatedData($id){
		if(!intval($id)){return false;}
		$this->_tableName = 'cmp_company as C'; //设置表名
		
		//默认条件
		$condition = "C.cmp_id=P.cmp_id and P.pdt_id=".$id;
		$leftjoin = array('pdt_products as P'=>$condition);
		
		//字段
		$field = array(
				 
				'P.name',
				'P.img',
				'P.confirm_code',
				'P.patent_code',
				'P.selling_points',
				'P.medicare_type',
				'P.medicament_type',
				'P.type1_id',
				'P.type2_id',
				'P.area',
				'P.zb_type',
				'P.spec',
				'P.offer',
				'P.supply_term',
				'P.producer',
				'P.remark',
				'P.component',
				'P.usage',
				'P.function',
				'P.link_man',
				'P.link_tel',
				'P.link_mp',
				'P.link_qq',
				'P.link_email',
				'P.link_address',
				'C.cmp_name',
				'C.cmp_addr',
				'C.static_url',
				'C.postcode'
				
				 
				 
		);
		 
		return  $this->getOne('', $field,$leftjoin);
		
		
	}
    
    
    public function getMedicamentTypes()
    {
        $mdl_pdt = new Model_pdt_products();
        return $mdl_pdt->getMedicamentTypes();
    }
    
}