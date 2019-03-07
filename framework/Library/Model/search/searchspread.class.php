<?php
class Model_search__searchspread extends Base_model{
	//@override
	public function setPrimaryKey() {
		$this->_PK = 'ss_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'search_spread';

	}
	public function setCache($status=false){
		$this->_cache = $status;
	}
	
	public function getListAll($page,$srow){
        $tn_old = $this->_tableName;
        $this->_tableName=$this->_tableName.' s';
		$condition ="s.`is_del`=0"; //查询条件
		if(count($srow)>0)
		{
			if($ss_type=intval($srow['ss_type']))
			{
				$condition.=" AND s.`ss_type` = $ss_type";
			}
			if($cmp_id=intval($srow['cmp_id']))
			{
				$condition.=" AND s.`cmp_id` = $cmp_id";
			}
			if($pdt_id=intval($srow['pdt_id']))
			{
				$condition.=" AND s.`pdt_id` = $pdt_id";
			}
			if($keywords=trim($srow['keywords']))
			{
				$condition.=" AND s.`keywords` = '$keywords'";
			}
			if($dateline=trim(strtotime($srow['dateline'])))
			{
				$condition.=" AND s.`dateline` = '$dateline'";
			}
			if($start_time=strtotime($srow['start_time']))
			{
				$condition.=" AND s.`start_time` = '$start_time'";
			}
			if($end_time=strtotime($srow['end_time']))
			{
				$condition.=" AND s.`end_time` = '$end_time'";
			}
			if($recommend=intval($srow['recommend']))
			{
				$condition.=" AND s.`recommend` = $recommend";
			}
			if($ss_state=intval($srow['ss_state']))
			{
				$condition.=" AND s.`ss_state` = $ss_state";
			}
            if(intval($srow['overdue'])==1) //正常
            {
                $condition.=" AND ".NOW."<= s.`end_time`";
            }
            else if(intval($srow['overdue'])==2) //过期
            {
                $condition.=" AND ".NOW."> s.`end_time`";
            }
            if(intval($srow['ps'])==1) //正常
            {
                $condition.=" AND p.is_del=0 AND p.audit_state IN(2,4)";
            }
            else if(intval($srow['ps'])==2) //404
            {
                $condition.=" AND (p.is_del=1 OR p.audit_state IN(1,3))";
            }
		}
        
        $fields=array('s.ss_id','s.cmp_id','s.pdt_id','s.ss_type','s.keywords','s.start_time','s.end_time','s.`order`','s.recommend','s.ss_state','s.dateline','p.is_del as pdt_is_del','p.audit_state as pdt_audit_state');
		$leftjoin=array('pdt_products_temp p'=>'s.pdt_id=p.pdt_id');
		$data= $this->getList($condition,$fields, array('s.order'), $page, 10, true,$leftjoin);//获取未删除数据
        $this->_tableName = $tn_old;
        return $data;
	}
	
	public function getOneData(){
		return $this->getOne("ss_id=".htmlspecialchars($_GET['id'],ENT_QUOTES),array('cmp_id','ss_id','pdt_id','keywords','dateline','start_time','end_time','`order`','recommend','ss_state','ss_type','x__img_url'));
	}
    
    /**
     * 根据主键id删除一行数据
     * add by baoxianjian 18:16 2015/3/28 
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
    * 自动更推广状态（根据当前时间，看是否过期）
    * add by baoxianjian 11:04 2015/4/15
    * 
    */
    public function autoUpdateStateAll()
    {
        $condition=NOW."> `end_time`";
        return $this->update($condition, array('ss_state=0'));   
    }
    
	
}
/**
 End file,Don't add ?> after this.
 */