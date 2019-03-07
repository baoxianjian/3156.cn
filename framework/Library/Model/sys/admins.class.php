<?php
 /**
* @name: 系统管理员的数据模型 
* @author: baoxianjian
* @date: 20:09 2015/4/8
*/
class Model_sys_admins extends Base_model{
    private $_saId;
    
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'sa_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'sys_admins'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
        
    //初始化，必填said
    function __construct($said=0){
        parent::__construct();
        if(!$said=intval($said))
        {
          //  exit('admins:sa id can not be null.');
        }
        $this->_saId=$said;
    }

    /**
    * 得到广告列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page,$srow)
    {
        //查询条件
        $condition = "sa.is_del=0";
        
        $condition.=$this->_getMoreCondition();   

        $condition=str_replace('sg_id','sa.sg_id',$condition);
        $condition=str_replace('sa_id','sa.sa_id',$condition); 
        
        if($id=intval($srow['id']))
        {
            $condition.=" AND sa.{$this->_PK} ={$id}"; 
        }
        if($gid=intval($srow['gid']))
        {
            $condition.=" AND sa.sg_id ={$gid}"; 
        }
        if($aname=trim($srow['aname']))
        {
            $condition.=" AND sa.sa_name LIKE '%{$aname}%'";
        }
        
        
        $this->_tableName='sys_admins sa';
        //$leftjoin="left join search_ggs sg ON sgp.sgp_id=sg.sgp_id ";
        $leftjoin=array('sys_groups sg'=>'sa.sg_id=sg.sg_id');
        $data=$this->getList($condition,"sa.*,sg.sg_name","ORDER BY sa.sa_id DESC",$page,10,true,$leftjoin);          
        $this->_tableName='sys_admins';    
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
        
        $condition="{$this->_PK}={$id}"; 
       
        $condition.=$this->_getMoreCondition();  
        
        return $this->update($condition, array('is_del=1'));
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

        $condition="{$this->_PK}={$id}"; 
        $condition.=$this->_getMoreCondition();   
        
        unset($row[$this->_PK]);
        return $this->update($condition,$row);
    }
    
    /**
    * 得到管理名称（根据id）
    * 
    * @param int $id
    * @return string
    */
    public function getSaNameById($id)
    {
       if(!$id=intval($id)){return false;}  
       $row=$this->getOne(array($this->_PK=>$id),'sa_name');
       return $row['sa_name'];       
    }
    
    /**
    * 得到等级（根据id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getLevelById($id)
    {
        if(!$id=intval($id)){return false;}
        $row=$this->getOne(array($this->_PK=>$id),'sa_level');
        return $row['sa_level'];
    }
    
    /**
    * 得到session_id（根据id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getSidById($id)
    {
        if(!$id=intval($id)){return false;}
        $row=$this->getOne(array($this->_PK=>$id),'session_id');
        return $row['session_id'];
    }
    
    /**
    * 得到session（根据sid）
    * 
    * @param string $sid
    * @return array|false
    */
    public function getSessionBySid($sid)
    {
        if(!$sid=trim($sid)){return flalse;}
        return $this->getOne(array('session_id'=>$sid),'sa_id,session_agent,login_ip,sa_name'); 
    }
    
    /**
    * 得到权限（根据id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getPermissionsAndLevelById($id)
    {
        if(!$id=intval($id)){return false;}
        return $this->getOne(array($this->_PK=>$id),'sa_permissions,sa_level');
    }
    
    /**
    * 得到一条记录（根据id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getRowByAdminName($saname)
    {
        if(!$saname=trim($saname)){return false;}
        return $this->getOne("sa_name='{$saname}'");
    }
    
    /**
    * 得到一条记录（根据id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getRowsetBySysMark($sm)
    {
        if(!$sm=trim($sm)){return false;}
      // return $this->getOne("sa_name='{$saname}'");
        
        $old_tn=$this->_tableName;
        $this->_tableName='sys_admins sa';
        //$leftjoin="left join search_ggs sg ON sgp.sgp_id=sg.sgp_id ";
        $leftjoin=array('sys_groups sg'=>'sg.sg_id=sa.sg_id');
        
        $condition=" sg.sys_mark='{$sm}'";
        
        $data=$this->getList($condition,"sa.sa_id,sa.sg_id,sa.sa_name,sa.real_name,sg.sys_mark",array('sa_id'=>'asc'),1,100,0,$leftjoin);          
        $this->_tableName=$old_tn;
        return $data['list'];
    }
    
    
    //得到更多的条件
    private function _getMoreCondition($lev_op="<")
    {       
       // $level=$this->getLevelById($this->_saId);
       
        if(!$this->_saId)
        {
            return '';
        }
       
        $row=$this->getRowById($this->_saId);
        $level=$row['sa_level'];
        $gid=$row['sg_id'];
        $condition='';
        
        //超级管理员可以看到所有用户组（角色）
        if($level!=SYS_ADM_LEVEL_MAX)
        
        {
             $condition=" AND (sa_parent_id={$this->_saId} OR (sg_id={$gid} AND sa_level{$lev_op}{$level}) OR sa_id={$this->_saId})";
        }
        
        return $condition;
    }
    
}