<?php
 /**
* @name: 搜索页广告的数据模型 
* @author: baoxianjian
* @date: 17:08 2015/3/21
*/
class Model_search_ads extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'sg_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'search_ggs'; //设置表名
        
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
        $condition="sg.`is_del`='0'";
        if(count($srow)>0)
        {
            if($id=intval($srow['id']))
            {
               $condition.=" AND sg.sg_id={$id}"; 
            }
            if($title=trim($srow['title']))
            {
                $condition.=" AND sg.`title` LIKE '%{$title}%'";
            }
            if(intval($srow['state'])==1) //正常
            {
                $condition.=" AND ".NOW."<= sg.`end_time`";
            }
            else if(intval($srow['state'])==2) //过期
            {
            	$condition.=" AND ".NOW."> sg.`end_time`";
            } 
        }

        
        $this->_tableName='search_ggs sg';
        //$leftjoin="left join search_ggs sg ON sgp.sgp_id=sg.sgp_id ";
        $leftjoin=array('search_gg_position sgp'=>'sg.sgp_id=sgp.sgp_id');
        $data=$this->getList($condition,"sg.*,sgp.title AS sgp_title",array('sg_id'=>'desc'),$page,10,true,$leftjoin);          
        $this->_tableName='search_ggs';    
        return $data;
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
    * 添加一个广告 
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
    

    
}
