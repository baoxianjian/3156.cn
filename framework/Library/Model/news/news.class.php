<?php
 /**
* @name: 资讯的数据模型 
* @author: wukai
* @date: 12:03 2015/4/13
*/
class Model_news_news extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'news_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'news_news'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
	
	/**
    * 获取列表数量
    * 
    * @return array|false
    */
    public function count($srow){
        
        $condition = $srow; //查询条件
        return $this->getCount($condition);
    }
    
    
      
      //获取0-50000网站地图网址
    public function getSitemapAll($page, $limit = 50000){      
        //默认条件
        $condition = "`audit_state` in(2,4) ";          
        $field = array('news_id','timestamp','admin_id');        
        return  $this->getList($condition,$field, array('timestamp'=>'desc'), $page, $limit, true );
    }
    
    /**
    * 得到列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page,$condition, $limit=30){
        return $this->getList($condition,array('*'),array($this->_PK=>'desc'), $page , $limit,true);
    }
    
    
    /**
    * 根据主键id删除一行数据
    * 
    * @param int $id
    * @return int|false
    */
    public function deleteRowById($id, $row = array()){
		if (is_array($id) ){//多选
			$delStr = implode($id, ',');
			return $this->update($this->_PK." in (".$delStr.")", $row);
		}else{
			if(!$id=intval($id)) return 0;
			return $this->update(array($this->_PK=>$id), array('is_del=1'));
		}
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
    public function updateRowById($row = array(),$id =0)
    {
        if(!$row) return false;
		if(!$id=intval($id)){return false;}
		unset($row[$this->_PK]);
		return $this->update(array($this->_PK=>$id),$row);
        //return $this->delete(array($this->_PK=>$id));
    }
    
    /**
    * 得到数据集
    * add by baoxianjian 21:55 2015/4/29
    * @param array $srow
    * @return array
    */
    public function getRowsetAll($srow=array(),$count=10)
    {
        $condition='n.is_del=0 AND n.audit_state IN(2,4)';   
        $order = " news_id DESC";
        
        $old_tn=$this->_tableName;
        $this->_tableName="news_news n";
        
        /*        
        if($srow['fee']) //收费
        {
            $condition.=" AND cmp_type=6 "; 
        }
        */
        if($srow['new']) //最新
        {
            //$order=" cmp_id DESC";    
        }
        if($srow['hot'])  //最热
        {
           $order = "click_count DESC, ".$order;
        }
        
        if($tid1=intval($srow['tid1'])) //一级分类
        {
            $condition.=" AND n.type_id1={$tid1}"; //二级分类
        }
        if($tid2=intval($srow['tid2']))
        {
            $condition.=" AND n.type_id2={$tid2}";  //三级分类
        }
        if($tid3=intval($srow['tid3']))
        {
            $condition.=" AND n.type_id3={$tid3}";  //三级分类
        }
        
        if($ids=trim($srow['ids'],',')) //指定ids
        {
            $condition.=" AND news_id IN ({$ids})";
            $count=100;  //指定id后 count失效
            $order=''; //指定id后 排序无效
        }
        
        $fields="n.news_id,n.admin_id,n.title,n.static_url, n.pic, n.description, n.type_id1,n.type_id2,n.type_id3,n.dateline,
                 t1.name AS type_name1, t1.en_name AS en_name1, 
                 t2.name AS type_name2, t2.en_name AS en_name2, 
                 t3.name AS type_name3, t3.en_name AS en_name3 
                 ";
        
        $leftjoin="LEFT JOIN news_type t1 ON n.type_id1=t1.nt_id
                   LEFT JOIN news_type t2 ON n.type_id2=t2.nt_id
                   LEFT JOIN news_type t3 ON n.type_id3=t3.nt_id";
        
        if($order)
        {
            $order_str=' ORDER BY '.$order;
        }
        
        $data=$this->getList($condition,$fields,$order_str,1,$count,0,$leftjoin); 
        
        return $data['list']; 
    }
    
    
    /**
    * 得到新闻名称数据集（根据多个ID）
    * add by baoxianjian 16:04 2015/5/11
    * @param string $ids
    * @return array
    */
    public function getNamesByIds($ids)
    {
        if(!$ids=$this->parseIds($ids)){return false;}
        
        $condition="is_del=0 AND news_id IN({$ids})";
        
        $fields="news_id,title";
        $data=$this->getList($condition,$fields,' ORDER BY news_id ASC',1,1000,0); 
        return $data['list'];
    }
    
}