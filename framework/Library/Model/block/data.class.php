<?php
 /**
* @name: 区块数据的数据模型 
* @author: baoxianjian
* @date: 22:19 2015/4/27
*/
class Model_block_data extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'bd_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'block_data'; //设置表名
        
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
    public function getListAll($page,$srow)
    {
        $condition="`is_del`=0";
        if(count($srow)>0)
        {
            if($id=intval($srow['id']))
            {
               $condition.=" AND {$this->_PK}={$id}"; 
            }
            if($title=trim($srow['title']))
            {
               $condition.=" AND `title` LIKE '%{$title}%'"; 
            }
            if($bbid=trim($srow['bbid']))
            {
               $condition.=" AND `bb_id`={$bbid}"; 
            }

        }
        
        $fields='bd_id,bb_id,mod_type,title,link_url,img_url,`order`,dateline';
        return $this->getList($condition,$fields,array($this->_PK=>'desc'),$page,20,1);
    }
   
    /**
    * 得到数据集
    * 
    * @param array $srow bbid 必须
    * @return array
    */
    public function getRowsetAll($srow=array(),$count=10)
    {
        $condition='is_del=0';
        $bbid=intval($srow['bbid']);
        if(!$bbid){return false;}  //bbid必填
        
        $condition.=" AND bb_id={$bbid}";
        
        $fileds="bd_id,bg_id,bb_id,title,link_url,img_url,click_count,abstract,dateline";
        
        $data=$this->getList($condition,$fileds,' ORDER BY `order` DESC, bd_id DESC',1,$count,0); 
        return $data['list']; 
    }
    
    /**
    * 得到一条记录（根据id）
    * @param int $id
    * @return array|false
    */
    public function getRowById($id,$fields="*")
    {
        if(!$id=intval($id)){return false;}
        
        $old_fn = $this->_tableName;
        $this->_tableName="block_data d";
        
        $leftjoin='LEFT JOIN block_blocks b ON d.bb_id=b.bb_id';
        $fields="d.*, b.bb_type";
        
        $row = $this->getOne(array($this->_PK=>$id),$fields,$leftjoin);
        
        $this->_tableName=$old_fn;
        return $row;
    }
    
    
    
}