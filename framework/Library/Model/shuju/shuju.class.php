<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_shuju_shuju extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'data_yaopinall'; //设置表名
        
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
     * 获取药包材生产企业列表数据
     * @param string $page
     * @param string $seek
     * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
     */
    public function getListAllclb($page,$srow){    	
    	//条件
    	$condition = '`comptype`="药包材生产企业"';  
    	    	
    	    if(count($srow)>0)
    	      {
	    	   	if($compname=$srow['compname']){
	    	   		$condition.=" AND `compname` like '%$compname%'";
	    	   	}
	    	   	if($zcaddress=$srow['zcaddress']){
	    	   		$condition.=" AND `zcaddress` ='$zcaddress'";
	    	   	}
	  //  	   	if($num=$srow['num']){
	  //  	   		$condition.=" AND `num` ='$num'";
	  //  	   	}
	    		    		
    	     }
    	
    	      
      return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 10, true);
    		
     }
     /**
      * 获取医疗器械经营企业列表数据
      * @param string $page
      * @param string $seek
      * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
      */
     public function getListAllylxq($page,$srow){
     	//条件
     	$condition = '`comptype`="医疗器械经营企业"';
     
     	if(count($srow)>0)
     	{
     		if($compname=$srow['compname']){
     			$condition.=" AND `compname` like '%$compname%'";
     		}
     		if($ck_address=$srow['ck_address']){
     			$condition.=" AND `ck_address` ='$ck_address'";
     		}
      	   	if($businessscope=$srow['businessscope']){
     		  	   	$condition.=" AND `businessscope` like '%$businessscope%'";
     	 	}
     			
     	}
     	 
     	 
     	return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 10, true);
     
     }
     /**
      * 获取gap列表数据
      * @param string $page
      * @param string $seek
      * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
      */
     public function getListAllgap($page,$srow){
     	//条件
     	$condition = '`comptype`="GAP认证"';
     	 
     	if(count($srow)>0)
     	{
     		if($compname=$srow['compname']){
     			$condition.=" AND `compname` like '%$compname%'";
     		}
     		if($zcaddress=$srow['zcaddress']){
     			$condition.=" AND `zcaddress` ='$zcaddress'";
     		}
     		//  	   	if($num=$srow['num']){
     		//  	   		$condition.=" AND `num` ='$num'";
     		//  	   	}
     
     	}
     	 
     	 
     	return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 10, true);
     	 
     }
   
     /**
      * 获取gmp列表数据
      * @param string $page
      * @param string $seek
      * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
      */
     public function getListAllgmp($page,$srow){
     	//条件
     	$condition = '`comptype`="GMP认证"';

     	 
     	if(count($srow)>0)
     	{
     		if($compname=$srow['compname']){
     			$condition.=" AND `compname` like '%$compname%'";
     		}
     		if($city=$srow['city']){
     			$condition.=" AND `city` = '$city'";
     		}
     	 	if($businessscope=$srow['businessscope']){
     		  	$condition.=" AND `businessscope` like '%$businessscope%'";
     		 }
     
     	}
     	 
     	 
     	return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 10, true);
     	 
     }
     /**
      * 获取生产企业列表数据
      * @param string $page
      * @param string $seek
      * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
      */
     public function getListAllscqy($page,$srow){
     	//条件
     	$condition = '`comptype`="生产企业"';
     	 
     	if(count($srow)>0)
     	{
     		if($compname=$srow['compname']){
     			$condition.=" AND `compname` like '%$compname%'";
     		}
     		if($zcaddress=$srow['zcaddress']){
     			$condition.=" AND `zcaddress` = '$zcaddress'";
     		}
     		//  	   	if($num=$srow['num']){
     		//  	   		$condition.=" AND `num` ='$num'";
     		//  	   	}
     		 
     	}
     	 
     	 
     	return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 10, true);
     	 
     }
     /**
      * 获取药品经营列表数据
      * @param string $page
      * @param string $seek
      * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
      */
     public function getListAllypjy($page,$srow){
     	//条件
     	$condition = '`comptype`="药品经营"';
     	 
     	if(count($srow)>0)
     	{
     		if($compname=$srow['compname']){
     			$condition.=" AND `compname` like '%$compname%'";
     		}
     		if($zcaddress=$srow['zcaddress']){
     			$condition.=" AND `zcaddress` = '$zcaddress'";
     		}
     		//  	   	if($num=$srow['num']){
     		//  	   		$condition.=" AND `num` ='$num'";
     		//  	   	}
     
     	}
     	 
     	 
     	return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 10, true);
     	 
     }
     /**
      * 经获glp列表数据
      * @param string $page
      * @param string $seek
      * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
      */
     public function getListAllglp($page,$srow){
     	//条件
     	$condition = '`comptype`="GLP认证"';
     	 
     	if(count($srow)>0)
     	{
     		if($compname=$srow['compname']){
     			$condition.=" AND `compname` like '%$compname%'";
     		}
     		if($zcaddress=$srow['zcaddress']){
     			$condition.=" AND `zcaddress` = '$zcaddress'";
     		}
     		//  	   	if($num=$srow['num']){
     		//  	   		$condition.=" AND `num` ='$num'";
     		//  	   	}
     		 
     	}
     	 
     	 
     	return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 10, true);
     	 
     }
     /**
      * gsp列表数据
      * @param string $page
      * @param string $seek
      * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
      */
     public function getListAllgsp($page,$srow){
     	//条件
     	$condition = '`comptype`="GSP认证"';
     	 
     	if(count($srow)>0)
     	{
     		if($compname=$srow['compname']){
     			$condition.=" AND `compname` like '%$compname%'";
     		}
     		if($city=$srow['city']){
     			$condition.=" AND `city` = '$city'";
     		}
     		if($businessscope=$srow['businessscope']){
     			 		$condition.=" AND `businessscope` like '%{$businessscope}%'";
     		}
     		 
     	}
     	 
     	 
     	return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 10, true);
     	 
     }
     /**
      * 医疗器械生产企业列表数据
      * @param string $page
      * @param string $seek
      * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
      */
     public function getListAllyl($page,$srow){
     	//条件
     	$condition = '`comptype`="医疗器械生产企业"';
     	 
     	if(count($srow)>0)
     	{
     		if($compname=$srow['compname']){
     			$condition.=" AND `compname` like '%$compname%'";
     		}
     		if($zcaddress=$srow['zcaddress']){
     			$condition.=" AND `zcaddress` = '$zcaddress'";
     		}
     		//  	   	if($num=$srow['num']){
     		//  	   		$condition.=" AND `num` ='$num'";
     		//  	   	}
     
     	}
     	 
     	 
     	return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 10, true);
     	 
     }
   
     
    
}