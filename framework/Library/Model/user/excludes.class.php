<?php
echo 13539020/1000;
exit;
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_user_excludes extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'ue_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'user_excludes'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    public function getRowByUT($uid,$type)
    {
        if(!$uid=intval($uid)){return false;}
        if(!$type=intval($type)){return false;}
        $condition="user_id={$uid} AND ue_type={$type} ";
        return $this->getOne($condition,'*',false);
    }
    
    public function getIdsByUT($uid,$type)
    {
         $row=$this->getRowByUT($uid,$type);
         return $row['ue_ids'];
    }
}
