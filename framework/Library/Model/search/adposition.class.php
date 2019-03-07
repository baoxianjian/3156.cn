<?php
 /**
* @name: 搜索页广告的数据模型 
* @author: baoxianjian
* @date: 17:08 2015/3/21
*/
class Model_search_adposition extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'sgp_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'search_gg_position'; //设置表名   
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
    
    public function getListAll($page)
    {
        $condition = array('is_del'=>'0'); //查询条件
        
        
        //return $this->getList($condition,array(' sgp.*,sg.sg_id AS sg_id1'),array('sgp_page'=>'asc','sgp_area'=>'asc','order'=>'asc'),$page,10,);
        //$this->query($this->_tableName,"SELECT sgp.*,sg.sg_id AS sg_id1 FROM search_gg_position sgp  LEFT JOIN search_ggs sg ON sgp.sgp_id=sg.sgp_id WHERE sgp.`is_del`='0' ORDER BY `sgp_page` asc,`sgp_area` asc,`order` asc LIMIT 0,10");

        $condition="sgp.`is_del`='0'"; 
        $this->_tableName='search_gg_position sgp';
        //$leftjoin="left join search_ggs sg ON sgp.sgp_id=sg.sgp_id ";
        $leftjoin=array('search_ggs sg'=>'sgp.sgp_id=sg.sgp_id');
        $data=$this->getList($condition,"sgp.*,sg.sg_id AS sg_id1",array('sgp_page'=>'asc','sgp_area'=>'asc','order'=>'asc'),$page,0,false,$leftjoin);          
        $this->_tableName='search_gg_position';    
        $count=$this->getCount('is_del=0');
        $data['count']=$count;
        return $data;
    }
    */ 
    
    /**
    * 得到广告列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page,$srow)
    {
      $condition ="`is_del`=0"; //查询条件
      if(count($srow)>0)
        {   
        	if($sgp_area=intval($srow['sgp_area']))
        	{
        	$condition.=" AND `sgp_area` = $sgp_area";
        	}
        	if($title=trim($srow['title']))
        	{
        		$condition.=" AND `title` = '$title'";
        	}
        	if($sgp_page=intval($srow['sgp_page']))
        	{
        		$condition.=" AND `sgp_page` = $sgp_page";
        	}
        	if($sg_id=intval($srow['sg_id']))
        	{
        		$condition.=" AND `sg_id` = $sg_id";
        	}
        
        }
        return $this->getList($condition,array('*'),array('sgp_page'=>'asc','sgp_area'=>'asc','order'=>'asc'),$page);
 
    }
    
    /**
    * 得到所有数据
    * 
    */
    public function getRowsetAll()
    {
        $condition = array('is_del'=>'0'); //查询条件  

        return $this->getList($condition,array('*'),array('sgp_page'=>'asc','sgp_area'=>'asc','order'=>'desc'),0,0,false);  
    }
    
    /**
    * 得到某个页面下的广告
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getRowsetAdsByPage($page)
    {
        $condition="sgp.`is_del`='0' AND sgp.sgp_page={$page}"; 
        $this->_tableName='search_gg_position sgp';
       // $leftjoin=array('search_ggs sg'=>'sgp.sg_id=sg.sg_id'); //
        
        $leftjoin='LEFT JOIN search_ggs sg ON sgp.sg_id=sg.sg_id';
        $data=$this->getList($condition,"sg.*,sgp.title AS sgp_title,sgp.sgp_id AS sgp_id1,width,height,sgp_area",' ORDER BY sgp_area asc, sgp.`order` desc, sgp.sg_id desc',1,10,true,$leftjoin);          
       // $this->_tableName='search_ggs';    
        return $data; 

        /*
        $condition = array('is_del'=>'0','sgp_page'=>$page); //查询条件  

        return $this->getList($condition,array('*'),array('sgp_page'=>'asc','sgp_area'=>'asc','order'=>'asc'),$page,0,false);      
        */
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
    
    /**
    * 得到广告页面配置列表
    * @return mixed
    */ 
    public function getPageList()
    {
        return array('1'=>'首页','2'=>'产品搜索页','3'=>'企业搜索页');
    }
    
    /**
    * 得到页面名称
    * 
    * @param mixed $id 页面类型下标
    * @return string
    */
    public function getPageName($id)
    {
        $a= $this->getPageList();
        return $a[$id];
    }
    
    
    /**
    * 得到广告区域配置列表
    * @param int $i
    * @return mixed
    */
    function getAreaList()
    {
       return array('1'=>'上侧','2'=>'下侧','3'=>'左侧','4'=>'右侧'); 
    }
    
    /**
    * 得到区域名称
    * 
    * @param mixed $id 区域类型下标
    * @return string
    */
    public function getAreaName($id)
    {
        $a= $this->getAreaList();
        return $a[$id];
    }
    
    
}