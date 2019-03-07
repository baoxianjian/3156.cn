<?php
 /**
* @name: 区块模版的数据模型 
* @author: baoxianjian
* @date: 15:44 2015/4/25
*/
class Model_block_templates extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'bt_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'block_templates'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    
    /**
    * 得到广告列表所有
    * 
    * @param int $page
    * @return array|false
    */
    public function getListAll($page,$srow)
    {
        $condition="`is_del`=0";
        if(count($srow)>0)
        {
            if($id=intval($srow['id']))
            {
               $condition.=" AND {$this->_PK}={$id}"; 
            }
            if($name=trim($srow['btn']))
            {
               $condition.=" AND `name` LIKE '%{$name}%'"; 
            }
            if($type=trim($srow['type']))
            {
               $condition.=" AND bt_type={$type}"; 
            }
        }
        
        return $this->getList($condition,'bt_id,bt_type,`name`,`code`,dateline',array($this->_PK=>'desc'),$page,10,1);
    }


    /**
    * 得到类型
    * 
    */
    public function getTypes()
    {
        //1文字2图片3flash4.flv视频5代码6非主流
        $a=array(1=>'文字',2=>'图片',6=>'非主流');
        return $a;    
    }
    

     
    public function getRowsetAll($srow=array())
    {
        $condition='is_del=0';
        
        if($type=intval($srow['type']))
        {
            $condition.=" AND bt_type={$type}";
        }
    
        $fields="bt_id,bt_type,name";
        $data=$this->getList($condition,$fields,' ORDER BY bt_id desc',1,1000,0); 
        return $data['list']; 
    }
     
    /**
    * 得到模版名称（根据模版id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getNameById($id)
    {
        if(!$id=intval($id)){return false;}
        $row= $this->getOne("{$this->_PK}={$id}",'name');
        return $row['name']; 
    }
    
    
    
    
    
    
    
}