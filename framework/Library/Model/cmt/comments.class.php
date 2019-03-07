<?php
 /**
* @name: 关键字管理的数据模型 
* @author: zhanghao
* @date: 11:08 2015/3/22
*/

class Model_cmt_comments extends Base_model{
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
    public function getListAll($page, $srow, $limit=15)
     { 
       $condition ="`is_del`=0 AND `audit_state`=2 "; //查询条件
     //  print_r($srow);       
     if(count($srow)>0)
        {   
        	if($srow['start_time']!="" && $end_time=$srow['end_time']!="")
        	{     
        		$start_time=strtotime($srow['start_time']);
        		$end_time=strtotime($srow['end_time']); //2015-1-10
        		$condition.=" AND `dateline`>$start_time and `dateline`<$end_time+86400";
        	}
            if($cmp_id=intval($srow['cmp_id']))
            {
                $condition.=" AND `cmp_id` = {$cmp_id}";
            }
            
        	if($is_read=intval($srow['is_read']))
        	{
        		$condition.=" AND `is_read` = $is_read";
        	}
        	if($srow['today'])
        	{    
        		$time=strtotime($srow['today']+1);
        		$condition.=" AND $time-`dateline`<=86400";
        	}
        	if($srow['yester'])
        	{
        		$time=strtotime($srow['yester']);
        		$condition.=" AND $time-`dateline`<=86400 and $time-`dateline`>0";
        	}
        	if($srow['near'])
        	{
        		$time=strtotime($srow['near']);
        		$condition.=" AND $time-`dateline`<=86400*7";
        	}        	
       	
        }
          
        return $this->getList($condition,array('*'),array('cmt_id'=>'desc'),$page,$limit,true);
     //  return $data;
        
    }
    
    /**
     * 高级会员查询
     *
     */
    public function getListAllSen($page,$srow, $limit=10)
    {
    	$condition ="`is_del`=0 AND `online_state`=2"; //查询条件
    	//  print_r($srow);
    	if(count($srow)>0)
    	{
    		if($srow['start_time']!="" && $end_time=$srow['end_time']!="")
        	{     
        		$start_time=strtotime($srow['start_time']);
        		$end_time=strtotime($srow['end_time']); //2015-1-10
        		$condition.=" AND `dateline`>$start_time and `dateline`<$end_time+86400";
        	}
        	 		 
    		if($tel=intval($srow['tel']))
    		{
    			$condition.=" AND `tel` = $tel";
    		} 
    		
    		if($pdt_names=trim($srow['pdt_names']))
    		{
    			$condition.=" AND `pdt_names` like '%$pdt_names%'";
    		}     	
    		if($favorite=intval($srow['favorite']))
    		{
    			$condition.=" AND `favorite` = $favorite";
    		}
         	if($pdt_types=intval($srow['pdt_types']))
    		{
   			   $condition.=" AND `pdt_types` = $pdt_types";
   		    }
			if($srow['areas']){
                $mdl_area=new Model_com_area();  
				$srow['areas'] = explode(',', $srow['areas']);
				if(count($srow['areas']) > 1)
                {
					$condition.=" AND (";
					foreach($srow['areas'] as $val){
						if($val){
                            $a=$mdl_area->getSplitedCodeIds($val);
							$_condition[]=" areas_code1 = ".$a[0];
						}
					}
					$condition .= implode(' or ', $_condition);
					$condition.=" )";
				}else{
                    $srow['areas']= $srow['areas'][0];
                    $str_code=$mdl_area->getLikeStrByCodeId($srow['areas'],'_');
                    $condition.=" AND areas_code LIKE '%{$str_code}%'";
				}
			}
            if($filtertel=intval($srow['filtertel']))
            {
	    	$groupby_field="tel,mp";
                $groupby=" GROUP BY ".$groupby_field;

            }
    
    	}
#    	$list = $this->getList($condition. $groupby,array('*'),array('cmt_id'=>'desc'),$page,$limit,true);

    	$list=$this->getList($condition.$groupby,array('*'),array('cmt_id'=>'desc'),$page,$limit,false);

        $list['count']=$this->getCount($condition,	$groupby);

	return $list;
    	//  return $data;
    
    }
    
    /**
     * 站内来电信息 查询
     *
     */
    public function getListAllCall($page,$srow)
    {
    	$condition ="`is_del`=0"; //查询条件
    	//  print_r($srow);
    	if(count($srow)>0)
    	{
    		if($srow['start_time']!="" && $end_time=$srow['end_time']!="")
    		{
    			$start_time=strtotime($srow['start_time']);
    			$end_time=strtotime($srow['end_time']); //2015-1-10
    			$condition.=" AND `dateline`>$start_time and `dateline`<=$end_time";
    		}
    		
    		     	
    	}
    
    	return $this->getList($condition,array('*'),array('cmt_id'=>'desc'),$page,$limit=10,true);
    	//  return $data;
    
    }
    /**
     * 免费会员代理信息 查询
     *
     */
    public function getListAllOrd($page,$srow, $limit=10)
    {
    	$condition ="`is_del`=0 AND `online_state`=1"; //查询条件
    	//  print_r($srow);
    	if(count($srow)>0)
    	{
    		if($srow['start_time']!="" && $end_time=$srow['end_time']!="")
    		{
    			$start_time=strtotime($srow['start_time']);
    			$end_time=strtotime($srow['end_time']); //2015-1-10
    			$condition.=" AND `dateline`>$start_time and `dateline`<=$end_time";
    		}
    		if($tel=intval($srow['tel']))
    		{
    			$condition.=" AND `tel` = $tel";
    		}
    		
    		if($pdt_names=trim($srow['pdt_names']))
    		{
    			$condition.=" AND `pdt_names` like '%$pdt_names%'";
    		}
    
    	}
    
    	return $this->getList($condition,array('*'),array('cmt_id'=>'desc'),$page,$limit,true);
    	//  return $data;
    
    }
    /**
     * 得到
     *
     * @param mixed $page
     * @return array|false
     */
 /*   public function getRowsetAdsByPage($page)
    {
    	
    	$this->_tableName='cmt_comments cc';
    	$condition="cc.`is_del`='0'";
    			// $leftjoin=array('search_ggs sg'=>'sgp.sg_id=sg.sg_id'); //
    
    	$leftjoin='LEFT JOIN cmt_product_agent cpa ON cc.mod_id=cpa.pdt_id';
        $data=$this->getList($condition,"cpa.*",' ORDER BY  cc.cmt_id desc',1,10,true,$leftjoin);
            		// $this->_tableName='search_ggs';
    
    	return $data; 
    
    	
    	/*
    	$condition = array('is_del'=>'0','sgp_page'=>$page); //查询条件
    
    	return $this->getList($condition,array('*'),array('sgp_page'=>'asc','sgp_area'=>'asc','order'=>'asc'),$page,0,false);
    	
    	}    
              */

    
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
    * 根据自定义语句得到一条数据
    * 
    * @param mixed $name
    * @return array|false
    */
    public function getRowBySrow($srow)
    {
        if(!$srow){return false;}
        return $this->getone($srow);
    }
    /**
     * 根据主键id 标记已读一行数据
     *
     * @param string $ids
     * @return int|false
     */
    public function readRowByIds($ids,$destroy=false)
    {
        $ids=$this->parseIds($ids);   
    	if(!$ids){return false;}
    
    	return $this->update($this->_PK." in ($ids)", array('is_read=1'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    /**
     * 根据主键id 标记未读一行数据
     *
     * @param string $ids
     * @return int|false
     */
    public function noreadRowByIds($ids,$destroy=false)
    {
        $ids=$this->parseIds($ids);   
    	if(!$ids){return false;}
    
    	return $this->update($this->_PK." in ($ids)", array('is_read=2'));
    	//return $this->delete(array($this->_PK=>$id));
    }
           
    /**
     * 视图查询一行数据
     *
     * @param string
     * @return int|false
     */
    public function getListFree($page,$srow, $limit=10)
    {
        $old_pk=$this->_PK;
        $old_tablename=$this->_tableName;
        $this->_PK = 'id';
        $this->_tableName = 'v_agent_free';
        
        $condition ="1"; //查询条件
        //  print_r($srow);
        if(count($srow)>0)
        {
            if($srow['start_time']!="" && $end_time=$srow['end_time']!="")
            {
                $start_time=strtotime($srow['start_time']);
                $end_time=strtotime($srow['end_time']); //2015-1-10
                $condition.=" AND `dateline`>=$start_time and `dateline`-$end_time<86400";
            }
            if($tel=intval($srow['tel']))
            {
                $condition.=" AND `tel` = $tel";
            }
        
            if($pdt_names=trim($srow['pdt_names']))
            {
                $condition.=" AND `pdt_name` like '%$pdt_names%'";
            }
            /*
            if($favorite=intval($srow['favorite']))
            {        if($favorite==1){        
                      $condition.=" AND `favorite` = $favorite";
                    }else{
                      $condition.=" AND `favorite` = 0 ";
                    }
            }
            */
            
            if($pdt_types=intval($srow['pdt_types']))
            {
                $condition.=" AND `pdt_types` = $pdt_types";
            }
            if($srow['areas']){
                $srow['areas'] = explode(',', $srow['areas']);
                if(is_array($srow['areas'])){
                    $condition.=" AND (";
                    foreach($srow['areas'] as $val){
                        if($val){
                            $_condition[]=" areas_code1 = ".$val;
                        }
                    }
                    $condition .= implode(' or ', $_condition);
                    $condition.=" )";
                }else{
                    $condition.=" AND areas_code2 like '%".$srow['areas']."%'";
                }
            }
        }
        
        $data=$this->getList($condition,array('*'),array('dateline'=>'desc'),$page,$limit,true);
        $this->_PK=$old_pk;
        $this->_tableName=$old_tablename;
        return $data;
    }
    
    
    public function searchListFree($page,$srow,$limit=10)
    {
       $q='(online_state:1 OR id:[1 TO 10000000])';
       if(count($srow)>0)
       {
            if($srow['start_time']!="" && $end_time=$srow['end_time']!="")
            {
                $start_time=strtotime($srow['start_time']); //2015-6-6
                $end_time=strtotime($srow['end_time'])+86400; //2015-6-6
               // $condition.=" AND `dateline`>=$start_time and `dateline`-$end_time<86400";
                $q.=" AND dateline:[{$start_time} TO {$end_time}]";    
            }
            if($tel=trim($srow['tel']))
            {
                $q.=" AND tel:{$tel}";
            }
	    /*
            if($uqtel=trim($srow['uqtel']))
            {
                //$condition.=" AND tel:{$tel}";
                $q="&group=true&group.field={$tel}&group.limit=1&group.sort=id desc";
            }
	    */
            if($pdt_names=trim($srow['pdt_names']))
            {
                $q.=" AND pdt_name1:*{$pdt_names}*";   
            //    $q_bf="&defType=edismax&bf=sqrt(log(ms(dateline)))";     
            }
            if($pdt_types=intval($srow['pdt_types']))
            {
                 //(pdt_type:1 OR pdt_type:*\[1\]*) 
                 $q.=" AND (pdt_type:{$pdt_types} OR pdt_type:*\\[{$pdt_types}\\]*)";
            }
            if($filtertel=intval($srow['filtertel']))
            {
                 //(pdt_type:1 OR pdt_type:*\[1\]*) 
                 //$q.=" AND (pdt_type:{$pdt_types} OR pdt_type:*\\[{$pdt_types}\\]*)";
                 $group='&group=true&group.field=telmp&group.limit=1&group.sort=id+desc&group.ngroups=true';
            }
            if($ue_ids=$this->parseIds($srow['ue_ids'],1))
            {
                foreach ($ue_ids as $v)
                {
                    $q.=" AND NOT id:{$v}";    
                }
                
            }
             if($srow['areas']){
	     	
                $srow['areas'] = explode(',', $srow['areas']);
                $mdl_area = new Model_com_area(); 
                
                if(count($srow['areas']) > 1)
                {
                   // $q_area = ' AND (';
                    foreach($srow['areas'] as $val){
                        if($val)
                        {
                            //$_condition[]=" areas_code1:".$val;
                            //$str_code=$mdl_area->getLikeStrByCodeId($val);
                            $a=$mdl_area->getSplitedCodeIds($val);
                            $area_temp.=' OR areas_code1:'.$a[0];
                        }
                    }
                    $area_temp=ltrim($area_temp,' OR');
                        
                    $area_temp=' AND ('.$area_temp.') ';

                    //$q.= implode(' OR ', $_condition);
                   // $q.= 'AND areas:'.$_condition;
                    
                }
                else
                {
                    //$q.=" AND areas_code:".$srow['areas'];
                    //$area_temp = $mdl_area->getPathByCodeId($srow['areas']);  
                    $srow['areas']= $srow['areas'][0];
                    
                    $str_code=$mdl_area->getLikeStrByCodeId($srow['areas']);
		            //$str_code="*{$str_code},";

                   // $srow['areas']=str_replace('0000','*',$srow['areas']);
                   // $srow['areas']=str_replace('00','*',$srow['areas']);
                    $area_temp.=" AND areas_code:*{$str_code},";
                    /*
                    $a = $mdl_area->getPathByCodeId($srow['areas'][0],false);
	                foreach ($a as $v)
                    {
                        $area_temp.=' AND areas:'.$v['name'];   
                    }
                    */
                }
                
               // $area_temp=$mdl_area->clearAreaNamePostfix($area_temp);
                $q.=$area_temp;
            }
            $q=ltrim($q,' AND');

        }
        if(!$q)
        {
            $q="*:*";
        }
        
        $q=urlencode($q);
        
        if($q_bf)
        {
           //$q_bf=urlencode($q_bf);
        }
        else
        {
	        $sort="dateline desc";
            $sort=urlencode($sort);
            $q_sort="&sort={$sort}";
        }
        /*
        if($group)
        {
            $group=urlencode($group);
        }
        */
        $qstr="q={$q}".$group.$q_sort.$q_bf;

        return $this->searchList($page,'agentcmt/select',$qstr,$limit);
    }
    
    public function searchListAllSen($page,$srow,$limit=10)
    {
       $q='online_state:2';
       if(count($srow)>0)
       {
            if($srow['start_time']!="" && $end_time=$srow['end_time']!="")
            {
                $start_time=strtotime($srow['start_time']); //2015-6-6
                $end_time=strtotime($srow['end_time'])+86400; //2015-6-6
               // $condition.=" AND `dateline`>=$start_time and `dateline`-$end_time<86400";
                $q.=" AND dateline:[{$start_time} TO {$end_time}]";    
            }
            if($tel=trim($srow['tel']))
            {
                $q.=" AND tel:{$tel}";
            }
        /*
            if($uqtel=trim($srow['uqtel']))
            {
                //$condition.=" AND tel:{$tel}";
                $q="&group=true&group.field={$tel}&group.limit=1&group.sort=id desc";
            }
        */
            if($pdt_names=trim($srow['pdt_names']))
            {
                $q.=" AND pdt_name1:*{$pdt_names}*";   
               // $q_bf="&defType=edismax&bf=sqrt(log(ms(dateline)))";     
            }
            if($pdt_types=intval($srow['pdt_types']))
            {
                 //(pdt_type:1 OR pdt_type:*\[1\]*) 
                 $q.=" AND (pdt_type:{$pdt_types} OR pdt_type:*\\[{$pdt_types}\\]*)";
            }
            if($filtertel=intval($srow['filtertel']))
            {
                 //(pdt_type:1 OR pdt_type:*\[1\]*) 
                 //$q.=" AND (pdt_type:{$pdt_types} OR pdt_type:*\\[{$pdt_types}\\]*)";
                 $group='&group=true&group.field=telmp&group.limit=1&group.sort=id+desc&group.ngroups=true';
            }
            if($ue_ids=$this->parseIds($srow['ue_ids'],1))
            {
                foreach ($ue_ids as $v)
                {
                    $q.=" AND NOT id:{$v}";    
                }
                
            }

             if($srow['areas']){
             
                $srow['areas'] = explode(',', $srow['areas']);
                $mdl_area = new Model_com_area(); 
                
                if(count($srow['areas']) > 1)
                {
                   // $q_area = ' AND (';
                    foreach($srow['areas'] as $val){
                        if($val)
                        {
                            //$_condition[]=" areas_code1:".$val;
                            //$str_code=$mdl_area->getLikeStrByCodeId($val);
                            $a=$mdl_area->getSplitedCodeIds($val);
                            $area_temp.=' OR areas_code1:'.$a[0];
                        }
                    }
                    $area_temp=ltrim($area_temp,' OR');
                        
                    $area_temp=' AND ('.$area_temp.') ';

                    //$q.= implode(' OR ', $_condition);
                   // $q.= 'AND areas:'.$_condition;
                    
                }
                else
                {
                    //$q.=" AND areas_code:".$srow['areas'];
                    //$area_temp = $mdl_area->getPathByCodeId($srow['areas']);  
                    $srow['areas']= $srow['areas'][0];
                    
                    $str_code=$mdl_area->getLikeStrByCodeId($srow['areas']);
                    //$str_code="*{$str_code},";

                   // $srow['areas']=str_replace('0000','*',$srow['areas']);
                   // $srow['areas']=str_replace('00','*',$srow['areas']);
                    $area_temp.=" AND areas_code:*{$str_code},";
                    /*
                    $a = $mdl_area->getPathByCodeId($srow['areas'][0],false);
                    foreach ($a as $v)
                    {
                        $area_temp.=' AND areas:'.$v['name'];   
                    }
                    */
                }
                
               // $area_temp=$mdl_area->clearAreaNamePostfix($area_temp);
                $q.=$area_temp;
            }
            $q=ltrim($q,' AND');

        }
        if(!$q)
        {
            $q="*:*";
        }
        
        $q=urlencode($q);
        
        if($q_bf)
        {
           //$q_bf=urlencode($q_bf);
        }
        else
        {
            $sort="dateline desc";
            $sort=urlencode($sort);
            $q_sort="&sort={$sort}";
        }
        /*
        if($group)
        {
            $group=urlencode($group);
        }
        */
        $qstr="q={$q}".$group.$q_sort.$q_bf;

        return $this->searchList($page,'agentcmt/select',$qstr,$limit);
    }
    

    /**
     * 根据主键id审核多行数据
     *
     * @param string $ids
     * @return int|false
     */
    public function examineRowByIds($ids,$destroy=false)
    {
        $ids=$this->parseIds($ids);
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
        $ids=$this->parseIds($ids);
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
        $ids=$this->parseIds($ids);
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
        $ids=$this->parseIds($ids);
    
    	if(!$ids){return false;}
    	return $this->update($this->_PK." in ($ids)", array('favorite=1'));
    	//return $this->delete(array($this->_PK=>$id));
    }
    
    
    
}
/**
    End file,Don't add ?> after this.
*/
