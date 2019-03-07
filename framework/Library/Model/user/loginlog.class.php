<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_user_loginlog extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'ull_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'user_login_log'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
     
    /**
     * 
     *得到厂商会员中心列表
     * @param mixed $page
     * @return array|false
     */
    public function getListAllip($page,$id)
    {
    
    	//查询条件
    	   $user_id=intval(($id));
    	   $condition=" `user_id` =$user_id ";
    	
    	return $this->getList($condition,array('*'),array('ull_id'=>'desc'),$page,$limit=15,true);
    }
    

    
    
}