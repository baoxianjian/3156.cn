<?php
 /**
* @name: 关键字管理的数据模型 
* @author: zhanghao
* @date: 17:37 2015/4/13
*/
class Model_agentcomments_agentcomments extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'cmt_id';
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'cmt_comments';
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    /**
    * 得到关键字列表所有
    * 
    */
    public function getListAll($page,$srow)
     { 
       	 $condition ="is_del=0"; //查询条件
       	// $condition='1';
    if(count($srow)>0)
        {   
        	if($online_state=intval($srow['online_state']))
        	{
        	$condition.=" AND `online_state` = $online_state";
        	}
        	if($dateline=trim($srow['dateline']))
        	{
        		$condition.=" AND `dateline` = $dateline";
        	}
        	if($pdt_id=intval($srow['pdt_id']))
        	{
        		$condition.=" AND `pdt_id` = $pdt_id";
        	}
        	if($user_id=intval($srow['user_id']))
        	{
        		$condition.=" AND `user_id` = $user_id";
        	}
        	if($areas=trim($srow['areas']))
        	{
        		$condition.=" AND `areas` = '$areas'";
        	}
        	if($company=trim($srow['company']))
        	{
        		$condition.=" AND `company` = '$company'";
        	}
        	if($link_man=trim($srow['link_man']))
        	{
        		$condition.=" AND `link_man` = '$link_man'";
        	}
        
        }
 /*       
        $this->_tableName='cmt_product_agent cpa';
        //$leftjoin="left join search_ggs sg ON sgp.sgp_id=sg.sgp_id ";
        $leftjoin=array('cmt_comments cc'=>'cc.cmt_id=cpa.cmt_id');
        $data=$this->getList($condition,"cc.*,cpa.pdt_id",array('cmt_id'=>'desc'),$page,10,true,$leftjoin);
        $this->_tableName='cmt_comments';
        return $data;
 */       
       return $this->getList($condition,array('*'),array('cmt_id'=>'desc'),$page,$limit=10,true);
       
        
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
    * 根据关键字名称得到一条数据
    * 
    * @param mixed $name
    * @return array|false
    */
    public function getRowByName($name)
    {
        if(!$name){return false;}
        return $this->getone("kw_name='$name'");
    }
    
    /**
     * 视图查询数据
     *
     * @param string
     * @return int|false
     */
    public function getListAgentcomments($page,$srow)
    {
    	$old_pk=$this->_PK;
    	$old_tablename=$this->_tableName;
    	$this->_PK = 'id';
    	$this->_tableName = 'v_agentcomments';
    	 
    	$condition ="is_del=0";  //查询条件
    	//  print_r($srow);
    if(count($srow)>0)
        {   
        	if($online_state=intval($srow['online_state']))
        	{
        	$condition.=" AND `online_state` = $online_state";
        	}
        	if($dateline=strtotime($srow['dateline']))
        	{
        		$condition.=" AND `dateline`-$dateline<86400 and `dateline`-$dateline>0";
        	}
        	if($pdt_name=trim($srow['pdt_name']))
        	{
        		$condition.=" AND `pdt_name` like '%$pdt_name%'";
        	}
        	if($user_id=intval($srow['user_id']))
        	{
        		 if($user_id==1)
        		 {
        		  $condition.=" AND `user_id`!=0 and `sa_id`=0 ";
        		 }
        		 if($user_id==2)
        		 {
        		 	$condition.=" AND `sa_id`!=0";
        		 }
        		 if($user_id==3)
        		 {
        		 	$condition.=" AND `user_id`=0 and `sa_id`=0";
        		 }        		         		 
        		 
        	}
        	if($areas=trim($srow['areas']))
        	{
        		$condition.=" AND `areas` = '$areas'";
        	}
        	if($company=trim($srow['company']))
        	{
        		$condition.=" AND `company` like '%$company%'";
        	}
        	if($link_man=trim($srow['link_man']))
        	{
        		$condition.=" AND `link_man` = '$link_man'";
        	}
        	if($tel=trim($srow['tel']))
        	{
        		$condition.=" AND `tel` = '$tel'";
        	}
        
        }
    	 
    	$data=$this->getList($condition,array('*'),array('dateline'=>'desc'),$page,$limit=10,true);
    	$this->_PK=$old_pk;
    	$this->_tableName=$old_tablename;
    	return $data;
    }
    
    
    /**
    * 得到数据集
    * add by baoxianjian 14:34 2015/5/13
    * @param array $srow
    * @param int $count
    * @return mixed
    */
    public function getRowsetAll($srow=array(),$count=10)
    {
        $condition="id, type,pdt_name,company,areas,link_man,tel,dateline ";
        $order = " dateline DESC"; 
        
        $old_tn=$this->_tableName;
        $this->_tableName="v_agentcomments";
        
        $fields="id, type,pdt_name,company,areas,link_man,tel,dateline";

        if($srow['fee']) //收费
        {
           // $leftjoin.=" INNER JOIN cmp_company c ON p.cmp_id=c.cmp_id AND c.cmp_type=6 ";
        }
        if($srow['new']) //最新
        {
            //$order=" cmp_id DESC";    
        }
        if($srow['hot'])  //最热
        {
           //$order = "p.click_count DESC, ".$order;   
        }
        
        if($tid1=intval($srow['tid1'])) //区块中指定的分类1 对应 产品中的分类2
        {
            $condition.=" AND type={$tid1}"; //产品中的分类1没用 分类2是1级
        }
        
        /*
        if($tid2=intval($srow['tid2']))
        {
            $condition.=" AND p.type3_id={$tid2}";  //分类3是2级       
        }
        
        if($ids=trim($srow['ids'],','))
        {
            $condition.=" AND p.pdt_id IN ({$ids})";
            $count=100;  //指定id后 count失效
        }
        */
     
        $data=$this->getList($condition,$fields," ORDER BY ".$order,1,$count,0,$leftjoin);               
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
        
        $old_tn=$this->_tableName;
        $this->_tableName="v_agentcomments";
        
        $condition="is_del=0 AND v_id IN({$ids})";
        
        $fields="v_id,pdt_name";
        $data=$this->getList($condition,$fields,' ORDER BY v_id ASC',1,1000,0);
        
        $this->_tableName=$old_tn;
        return $data['list'];
    }
    
}
/**
    End file,Don't add ?> after this.
*/