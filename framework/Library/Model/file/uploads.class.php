<?php
 /**
* @name: 文件上传的数据模型 
* @author: baoxianjian
* @date: 22:56 2015/4/6
*/
class Model_file_uploads extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'up_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'file_uploads'; //设置表名
        
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
        
        $condition = array('is_del'=>'0'); //查询条件
        return $this->getList($condition,array('*'),array($this->_PK=>'desc'),$page);
    }
    
    /**
    * 得到所有数据
    * 
    * @param int $page
    * @param array $srow
    * @param int $limit
    * @return array
    */
    public function getRowsetAll($page=1,$srow=array(),$limit=50)
    {
        //$condition="1";
        if(!isset($srow['is_del']))
        {
            $srow['is_del']=0;
        }
        $data=$this->getList($srow,'*','',1,50,false);
        return $data['list'];
    }
    
    
    /**
    * 根据主键id删除一行数据
    * 
    * @param int $id
    * @return int|false
    */
    public function deleteRowById($id,$destroy=false)
    {
        if(!$id=intval($id)) return 0;
        
        return $this->update(array($this->_PK=>$id), array('is_del=1'));
        //return $this->delete(array($this->_PK=>$id));
    }
    

    /**
    * 添加一个用户 
    * 
    * @param array $row
    * @return int|false
    */
    public function addRow($row)
    {
        if(!$row) return false;
        return $this->insert($row);
        //return $this->delete(array($this->_PK=>$id));
    }
    
    /**
    * 修改一条记录（根据Id）
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
    * 得到一条记录（根据企业名称）
    * 
    * @param int $id
    * @return array|false
    */
    public function getRowByCmpName($cname)
    {
        if(!$cname=trim($cname)){return false;}
        return $this->getOne("cmp_name='{$cname}'");
    }
    
    /**
    * 得到一条记录（根据用户id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getRowByUserId($uid)
    {
        if(!$uid=intval($uid)){return false;}
        return $this->getOne("user_id='{$uid}'");
    }
    
    
    
}