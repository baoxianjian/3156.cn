<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_user_user extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'user_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'user_users'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
   
   /**
     * 更新二级密码
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
    * 得到广告列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page,$srow)
    {
        
        $condition = array('is_del'=>'0'); //查询条件
        return $this->getList($condition,array('*'),array('sg_id'=>'desc'),$page);
    }
    /**
     * 
     *得到厂商会员中心列表
     * @param mixed $page
     * @return array|false
     */
    public function getListAllip($page)
    {
    
    	$condition = array('is_del'=>'0'); //查询条件
    	return $this->getList($condition,array('*'),array('user_id'=>'desc'),$page,$limit=15,true);
    }
    
    
    /**
    * 得到session（根据sid）
    * 
    * @param string $sid
    * @return array|false
    */
    public function getSessionBySid($sid)
    {
        if(!$sid=trim($sid)){return flalse;}
        return $this->getOne(array('session_id'=>$sid),'user_id,session_agent,login_ip,user_name,user_mods'); 
    }
    

    /**
    * 得到一条记录（根据id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getRowByUserName($uname)
    {
        if(!$uname=trim($uname)){return false;}
        return $this->getOne("user_name='{$uname}'");
    }

    
    /**
     * 得到一条记录（根据id）
     *
     * @param int $id
     * @return array|false
     */
    public function getRowByEmail($email,$uname='')
    {
    	if(!$email=trim($email)){return false;}
    	
        $condition="email='{$email}'";
        if($uname=trim($uname))
        {
            $condition.=" and user_name='{$uname}'";
        } 

        return $this->getOne($condition,'*');
        
        
    }
}
