<?php
/**
* @name: 静态区块的数据模型 
* @author: baoxianjian
* @date: 10:39 2015/5/8
*/
class Model_static_blocks extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'sb_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'static_blocks'; //设置表名 
    }
    //@override
    public function setCache($status=false){
        $this->_cache = $status;
    }
    
    
    /**
    * 得到区块调用列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page,$srow)
    {
        
        /*
        $condition="`is_del`=0";
        if(count($srow)>0)
        {
            if($id=intval($srow['id']))
            {
               $condition.=" AND {$this->_PK}={$id}"; 
            }
            
            if($bkwn=trim($srow['bkwn']))
            {
                $condition.=" AND `bkw_name` LIKE '%{$bkwn}%'";
            }
            if($type=intval($srow['type'])) //正常
            {
                $condition.=" AND `bkw_type`={$type}";
            }
        }
        
        return $this->getList($condition,array('*'),array($this->_PK=>'desc'),$page,10,1);
        */
    }

    public function getRowsetAll($srow=array())
    {
        /*
        $condition='is_del=0';
        $fields="bkw_id,bkw_name,bkw_mark,mod_type,bkw_type,`order`";
        $data=$this->getList($condition,$fields,' ORDER BY `order` desc, bkw_id desc',1,1000,0); 
        
        $this->_tableName=$odl_tn; 
        
        return $data['list']; 
        */
    }
    
    /**
    * 修改一条记录（根据Id）
    * add by baoxianjian 10:20 2015/4/25  
    * @param array $row
    * @param int $id
    * @return int|false
    */
    public function updateRowByCmd($row,$cmd)
    {
        //die(SUtil::P($row));
        if(!$row) return false;
        if(!$cmd=trim($cmd)){return false;}
        unset($row[$this->_PK]);
        return $this->update('sb_cmd='.$cmd,$row);
        //return $this->delete(array($this->_PK=>$id));
    }
    
    /**
    * 修改一条记录（根据Id）
    * add by baoxianjian 10:20 2015/4/25  
    * @param array $row
    * @param int $id
    * @return int|false
    */
    public function updateRowBySBKey($row,$key)
    {
        //die(SUtil::P($row));
        if(!$row) return false;
        if(!$key=trim($key)){return false;}
        unset($row[$this->_PK]);
        return $this->update('sb_key='.$key,$row);
        //return $this->delete(array($this->_PK=>$id));
    }
    
    
    /**
    * 得到一条记录（根据key）
    * add by baoxianjian 10:20 2015/4/25  
    * @param int $id
    * @return array|false
    */
    public function getIdBySBKey($key)
    {
        if(!$key=trim($key)){return false;}
        $row = $this->getOne(array('sb_key'=>$key),array($this->_PK));
        return intval($row[$this->_PK]);
    }
    
     
    
    
}