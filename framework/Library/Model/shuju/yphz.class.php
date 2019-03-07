<?php
 /**
* @name: 化妆品数据库
* @author: yulei
* @date: 14:43 2015/6/12
*/
class Model_shuju_yphz extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'data_yaopinhuazhuang'; //设置表名
        
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
                   if($pronamechina=trim($srow['pronamechina'])){
                       $condition.=" AND `pronamechina` like '%$pronamechina%'";
                   }

                    if($chanpinleibie=trim($srow['chanpinleibie'])){
                      $condition.=" AND `chanpinleibie` like '%$chanpinleibie%'";
                    }
                    if($compnamechina=trim($srow['compnamechina'])){
                      $condition.=" AND `compnamechina` like '%$compnamechina%'";
                    }
                            
             }
        
              
      return $this->getList($condition,array('*'), array('id'=>'DESC'), $page, 20, true);
            
     }
    
   
     
    
}