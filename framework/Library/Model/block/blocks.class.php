<?php
 /**
* @name: 区块数据模型 
* @author: baoxianjian
* @date: 20:44 2015/4/25
*/
class Model_block_blocks extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'bb_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'block_blocks'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    
    /**
    * 得到区块列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page,$srow)
    {
        $condition="b.`is_del`=0";
        
        $old_fn = $this->_tableName;
        $this->_tableName="block_blocks b";
        
        if(count($srow)>0)
        {
            if($id=intval($srow['id']))
            {
               $condition.=" AND b.{$this->_PK}={$id}"; 
            }
            if($bn=trim($srow['bn']))
            {
               $condition.=" AND b.bb_name LIKE '%{$bn}%' "; 
            }
            if($bgid=intval($srow['bgid']))
            {
               $condition.=" AND b.bg_id={$bgid} ";
            }
            if($type=intval($srow['type']))
            {
               $condition.=" AND b.bb_type={$type} "; 
            }
        }
        


        $fields="b.bb_id,b.bg_id,b.bb_type,b.bb_name,b.bt_id,b.bb_standard,b.cache_time,b.ds_way,b.ds_count,b.dateline,
                 t.name AS bt_name,
                 g.bg_name
                 ";
        $leftjoin="LEFT JOIN block_templates t ON b.bt_id=t.bt_id
                   LEFT JOIN block_groups g ON b.bg_id=g.bg_id";           
        

        
        return $this->getList($condition,$fields,array($this->_PK=>'desc'),$page,20,1,$leftjoin);
    }
    
    /**
    * 得到类型列表
    * 
    */
    public function getTypes()
    {
        $a=array(1=>'文字',2=>'图片',6=>'非主流');
        return $a;    
    }
    
    /**
    * 得到数据来源方式列表
    * 
    */
    public function getDsWays()
    {
        return array('1'=>'手动','2'=>'自动','3'=>'代码块');
    }
    
    /**
    * 得到数据提取规则列表
    * 
    */
    public function getDsRules()
    {
        return array('2'=>'最新','3'=>'最热','4'=>'收费','1'=>'指定ID');
    }
    

    /**
    * 得到数据集
    *  
    * @param array $srow bgid
    * @return array
    */
    public function getRowsetAll($srow=array())
    {
        $condition='is_del=0';

        if($bgid=intval($srow['bgid']))
        {
            $condition.=" AND bg_id={$bgid} "; 
        }
        
        $fields="bb_id,bb_name,bb_type";
        $data=$this->getList($condition,$fields,' ORDER BY bb_id desc',1,1000,0); 
        
        return $data['list']; 
    }

    
    
    /**
    * 得到一条完整记录数据（根据id）
    * @param int $id
    * @return array|false
    */
    public function getRowDataById($id)
    {
        if(!$id=intval($id)){return false;}
        
        $old_fn = $this->_tableName;
        $this->_tableName="block_blocks b";
        $fields="b.bb_id,b.bg_id,b.bb_type,b.bb_name,b.bt_id,b.bb_standard,b.bb_title_length,b.bb_abs_length,b.ds_way,b.ds_mod_type,b.ds_mod_tid1,b.ds_mod_tid2,ds_mod_tid3,b.ds_rule,b.ds_count,b.ds_mod_ids,b.ds_html,
                 t.`code`,t.bt_type";
        $leftjoin='LEFT JOIN block_templates t ON b.bt_id=t.bt_id';   
        $row = $this->getOne(array($this->_PK=>$id),$fields,$leftjoin);
        $this->_tableName=$old_fn;
        return $row;
    }
    
    
    
    
    
    
    
}