<?php
 /**
* @name: 消毒药品数据库
* @author: yulei
* @date: 14:43 2015/6/12
*/
class Model_shuju_ypxd extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'data_yaopinxiaodu'; //设置表名
        
    }
    //@override
       public function setCache($status=false){
        $this->_cache = $status;
    }
       
    /**
     * 获取药包材生产企业列表数据
     * @param string $page
     * @param string $seek
     * @return Ambigous <multitype:, false, boolean, multitype:NULL multitype: >
     */
    public function getListAll($page,$srow){        
        //条件
        $condition =1;  
                                                
            if(count($srow)>0)
              {
                   if($name=trim($srow['name'])){
                       $condition.=" AND `name` like '%$name%'";
                   }

                    if($compname=trim($srow['compname'])){
                      $condition.=" AND `compname` like '%$compname%'";
                    }
                    if($type=trim($srow['type'])){
                      $condition.=" AND `type` like '%$type%'";
                    }
                            
             }
        
              
      return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 20, true);
            
     }
    
   
     
    
}