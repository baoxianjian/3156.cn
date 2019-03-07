<?php
 /**
* @name: 关键字管理的数据模型 
* @author: zhanghao
* @date: 11:08 2015/3/22
*/
class Model_agent_books extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'ab_id';
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'agent_books';
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    /**
    * 得到关键字列表所有
    * 
    */
    public function getListAll($page,$user_id)
    {
        if(!$user_id=intval($user_id)){return false;}    
        
        $condition ="`is_del`=0 And `user_id`={$user_id} "; //查询条件                                      
        return $this->getList($condition,array('*'),array('dateline'=>'desc'),$page,$limit=10,true);
    }
    /**
     * 得到关键字列表所有
     *
     */
    public function getListAllDel($page,$user_id)
    {       
        if(!$user_id=intval($user_id)){return false;}    
    	$condition ="`is_del`=1 And `user_id`={$user_id} "; //查询条件
    	return $this->getList($condition,array('*'),array('dateline'=>'desc' ),$page,$limit=10,true);
    }
    
     
    public function searchListAll($page,$srow,$limit=10)
    {
      //  $q="audit_state:2";
        if(count($srow)>0)
        {    
            if($time=$srow['time'])
            {  
                $today = strtotime(date('Y-m-d',time()));   
                $oneday = 86400;
                
 
                switch($time)
                {
                    case 'to':
                    {
                        $start = $today;
                        $end = $today + $oneday;
                        break;
                    }
                    case 'ye':
                    {
                         $start = $today -$oneday;
                         $end = $today;
                         break;
                    }
                    case 'week':
                    {
                      	$start = $today-(7*$oneday);
                      	$end = $today+ $oneday;  
                         break;
                    }
                    case 'om':
                    {                        
                         $start = $today-(30*$oneday);
                         $end = $today+ $oneday;  
                         break;
                    }
                    case 'tm':
                    {                        
                         $start = $today-(3*30*$oneday);
                         $end = $today+ $oneday;  
                         break;
                    }
                    case 'sm':
                    {                        
                         $start = $today-(6*30*$oneday);
                         $end = $today+ $oneday;  
                         break;
                    }
                    default:
                    {
                          break;
                    }
                }
                
                $q.=" AND dateline:[{$start} TO {$end}]";
            }
            
            if($pdt_type1=intval($srow['pt1']))
            {
                 //(pdt_type:1 OR pdt_type:*\[1\]*) 
                 $q.=" AND (pdt_type:{$pdt_type1} OR pdt_type:*\\[{$pdt_type1}\\]*)";
                  
            }
            if($areas=trim($srow['areas'])){
                $q.=" AND areas:{$areas}";
            }
            if($agent_type=intval($srow['at'])){
                $q.=" AND agent_type:{$agent_type}";
            }                       
            if($k=trim($srow['k'])){
                $q.=" AND pdt_name1:*{$k}*";
            }

            $q=ltrim($q,' AND');
        }
        if(!$q)
        {
            $q="*:*";
        }

        $sort="dateline desc";
        
        $q=urlencode($q);
        $sort=urlencode($sort);
        
        $qstr="q={$q}&sort={$sort}";

        return $this->searchList($page,'agentcmt/select',$qstr,$limit);
    }
    
    
    
    /**
     * 得到关键字列表所有
     *
     */
    public function getListMedicagent($page,$srow)
    { 
    	
    	$old_pk=$this->_PK;
    	$old_tablename=$this->_tableName;
    	$this->_PK = 'id';
    	$this->_tableName = 'v_agentcomments';
    	
    	$condition ="`is_del`=0"; 
    	if(count($srow)>0)
    	{    
    		if($time=$srow['time'])
    		{     
    			     if($time=='to'){
    			     	 $time=strtotime($srow['today']);
        		         $condition.=" AND `dateline`-$time<86400 and `dateline`-$time>=0" ;
    		         }elseif($time=='ye'){
    		           	 $time=strtotime($srow['today']);
        		         $condition.=" AND $time-`dateline`<=86400 and $time-`dateline`>0";
    		         }elseif($time=='week'){
    		         	 $time=strtotime($srow['today']);
        		         $condition.=" AND $time-`dateline`<=86400*7";
    		         }elseif($time=='om'){
    		         	 $time=strtotime($srow['today']);
        		         $condition.=" AND $time-`dateline`<=86400*30";
    		         }elseif($time=='tm'){
    		         	 $time=strtotime($srow['today']);
        		         $condition.=" AND $time-`dateline`<=86400*30*3";
    		         }elseif($time=='sm'){
    		         	 $time=strtotime($srow['today']);
        		         $condition.=" AND $time-`dateline`<=86400*30*6";
    		         }
    		}
    		if($pdt_type1=intval($srow['pdt_type1'])){
    			   
    				  $condition.=" AND `pdt_type` = '$pdt_type1'";
    			  
    		}
    		if($areas=trim($srow['areas'])){
    			$condition.=" AND `areas` = '$areas'";
    		}
    		if($agent_type=intval($srow['agent_type'])){
    			$condition.=" AND `agent_type` = '$agent_type'";
    		}   		    		
    		if($k=trim($srow['k'])){
    			$condition.=" AND `pdt_name` like '%$k%'";
    		}
    		
    	}
    	//查询条件
    	//return $this->getList($condition,array('*'),array('dateline'=>'desc'),$page,$limit=25,true);
    	$data=$this->getList($condition,array('*'),array('dateline'=>'desc'),$page,$limit=25,true);
    	$this->_PK=$old_pk;
    	$this->_tableName=$old_tablename;
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
     * 根据主键id删除多行数据
     *
     * @param string $ids
     * @return int|false
     */
    public function deleteRowByIds($ids,$destroy=false)
    {
    	$id_list=$ids;
    	if(!is_array($ids))
    	{
    		$id_list=explode(',', $ids);
    	}
    	
    	
    	foreach ($id_list as $k=>$v)
    	{
    		$id_list[$k]=intval($v);
    	}
    	$ids=implode(',', $id_list);
  

    	if(!$ids){return false;}
    	return $this->update($this->_PK." in ($ids)", array('is_del=1'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    /**
     * 根据主键id审核多行数据
     *
     * @param string $ids
     * @return int|false
     */
    public function examineRowByIds($ids,$destroy=false)
    {
    	$id_list=$ids;
    	if(!is_array($ids))
    	{
    		$id_list=explode(',', $ids);
    	}
    	 
    	 
    	foreach ($id_list as $k=>$v)
    	{
    		$id_list[$k]=intval($v);
    	}
    	$ids=implode(',', $id_list);
    
    
    	if(!$ids){return false;}
    	return $this->update($this->_PK." in ($ids)", array('audit_state=2'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    /**
     * 根据主键id取消审核多行数据
     *
     * @param string $ids
     * @return int|false
     */
    public function unexamineRowByIds($ids,$destroy=false)
    {
    	$id_list=$ids;
    	if(!is_array($ids))
    	{
    		$id_list=explode(',', $ids);
    	}
    
    
    	foreach ($id_list as $k=>$v)
    	{
    		$id_list[$k]=intval($v);
    	}
    	$ids=implode(',', $id_list);
    
    
    	if(!$ids){return false;}
    	return $this->update($this->_PK." in ($ids)", array('audit_state=3'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    /**
     * 根据主键id恢复多行数据
     *
     * @param string $ids
     * @return int|false
     */
    public function recoveryRowByIds($ids,$destroy=false)
    {
    	$id_list=$ids;
    	if(!is_array($ids))
    	{
    		$id_list=explode(',', $ids);
    	}
    
    
    	foreach ($id_list as $k=>$v)
    	{
    		$id_list[$k]=intval($v);
    	}
    	$ids=implode(',', $id_list);
    
    
    	if(!$ids){return false;}
    	return $this->update($this->_PK." in ($ids)", array('is_del=0'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    /**
     * 根据主键id收藏多行数据
     *
     * @param string $ids
     * @return int|false
     */
    public function collectallRowByIds($ids,$destroy=false)
    {
    	$id_list=$ids;
    	if(!is_array($ids))
    	{
    		$id_list=explode(',', $ids);
    	}
    
    
    	foreach ($id_list as $k=>$v)
    	{
    		$id_list[$k]=intval($v);
    	}
    	$ids=implode(',', $id_list);
    
    
    	if(!$ids){return false;}
    	return $this->update($this->_PK." in ($ids)", array('favorite=1'));
    	//return $this->delete(array($this->_PK=>$id));
    }

    /**
     * 根据主键id刷新一行数据
     *
     * @param string $ids
     * @return int|false
     */
    public function freshRowByIds($ids,$destroy=false)
    {
    	$id_list=$ids;
    	if(!is_array($ids))
    	{
    		$id_list=explode(',', $ids);
    	}
    	 
    	 
    	foreach ($id_list as $k=>$v)
    	{
    		$id_list[$k]=intval($v);
    	}
    	$ids=implode(',', $id_list);
    
    
    	if(!$ids){return false;}

    	return $this->update($this->_PK." in ($ids)", array('dateline'=>NOW));
    	//return $this->delete(array($this->_PK=>$id));
    }
    /**
     * 
     * 根据主键id审核多条数据
     * @param string $ids
     * @return int|false
     */
    public function examRowByIds($ids,$destroy=false)
    {
    	$id_list=$ids;
    	if(!is_array($ids))
    	{
    		$id_list=explode(',', $ids);
    	}
    
    
    	foreach ($id_list as $k=>$v)
    	{
    		$id_list[$k]=intval($v);
    	}
    	$ids=implode(',', $id_list);
    
    
    	if(!$ids){return false;}
    
    	return $this->update($this->_PK." in ($ids)", array('audit_state=2'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    
    /**
     * 添加
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
     * 根据ip和产品名得到一条记录（根据id）
     *
     * @param int $id
     * @return array|false
     */
    public function getRowByIpandName($ip,$pdt_name)
    {
    	//if(!$ip=intval($ip)){return false;}
        $pdt_name=trim($pdt_name);
        if(!$pdt_name){return false;}
    	return $this->getOne(array("ip='{$ip}'","pdt_name='{$pdt_name}'"));
    }
    
    /**
     * 得到一条记录（根据id）
     *
     * @param int $id
     * @return array|false
     */
    public function getRowByUserName($uname)
    {
    	if(!$uname=trim($uname)){return false;}
    	return $this->getOne("user_name='{$uname}'");
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
     * 根据关ip和company得到一条数据
     *
     * @param mixed $name
     * @return array|false
     
    public function getCountByUIdAndIp($uid,$ip);
    {
    	if(!$uid){return false;}
    	if(!$ip){return false;}
    	
    	return $this->getone("`ip`='$ip' And `user_id`='$uid'");
    }
    */
     /**
     * 根据多个id进行删除
     * 
     * @param mixed $ids
     * @return int|false
    
    public function deleteByIds($ids){
    	 
    	if ( is_array($ids) ){//多选
    
    		$delStr = implode($ids, ',');
    		//die($delStr);
    		return $this->update("ab_id in (".$delStr.")", 'is_del=1');
    
    	}else{//单选
    
    		return $this->update("ab_id=".$ids, "is_del=1");
    
    	}
    }
      */ 
    public function getListFree()
    {
        $old_pk=$this->_PK;
        $old_tablename=$this->_tableName;
        $this->_PK = 'id'; 
        $this->_tableName = 'v';
        $condition ="1"; //查询条件                                      
        $data=$this->getList($condition,array('*'),array('dateline'=>'desc'),$page,$limit=10,true);
        $this->_PK=$old_pk;
        $this->_tableName=$old_tablename;
        return $data;
    }
    
    /**
     * 查询数据
     *
     * @param string
     * @return int|false
     */
    public function getListAgentlist($page,$srow)
    {   
         if(intval($srow['is_del'])==2){
    	      $condition ="is_del=1";
         }else{
         	  $condition ="is_del=0"; 
         }
    	
    	if(count($srow)>0){
    		if($today=strtotime($srow['dateline']))
    		{  
                $end_time=$today+86400;
                
    			$condition.=" AND `dateline`>{$today} and `dateline`<{$end_time}";
    		}
    		if($pdt_name=trim($srow['pdt_name']))
    		{
    			$condition.=" AND `pdt_name` like '%$pdt_name%'";
    		}
    		if($areas=trim($srow['areas']))
    		{
    			$condition.=" AND `areas` = '$areas'";
    		}
    	    if($user_id=intval($srow['user_id']))
	    	{
	    		if($user_id==1)
	    		{
	    			$condition.=" AND `user_id`!=0 and `sa_id`=0 ";
	    		}
	    		if($user_id==2)
	    		{
	    			$condition.=" AND `sa_id`!=0 AND `user_id`=0";
	    		}
	    		if($user_id==3)
	    		{
	    			$condition.=" AND `user_id`=0 and `sa_id`=0";
	    		}
	    		 
	    	}
    		
    		if($online_state=intval($srow['online_state']))
    		{
    			$condition.=" AND `online_state` = $online_state";
    		}   		
    		if($tel=trim($srow['tel']))
    		{
    			$condition.=" AND `tel` = '$tel'";
    		}
    		if($mp=trim($srow['mp']))
    		{
    			$condition.=" AND `mp` = '$mp'";
    		}
    		if($link_man=trim($srow['link_man']))
    		{
    			$condition.=" AND `link_man` like '%$link_man%'";
    		}   		
    		if($audit_state=trim($srow['audit_state']))
    		{
    			$condition.=" AND `audit_state` = '$audit_state'";
    		}
    		
    	}
    	    	    	
    return  $this->getList($condition,array('*'),array('dateline'=>'desc'),$page,$limit=30,true);
          
    }
    
    
    
}
/**
    End file,Don't add ?> after this.
*/