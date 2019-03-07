<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 12:03 2015/3/29
*/
class Model_pdt_pdtTypes extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'pt_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'pdt_types'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    
    /**
     * 获取所有未删除顶级分组列表
     */
    public function getGroupList($page='',$seek=''){    
    	if ( $page != '' ){
    			
    		if ( $seek == '' ){
    
    			return $this->getList("parent_id=0",array('pt_name','pt_id','dateline','parent_id'),array('pt_name'=>'DESC'),$page, 10, true);
    
    		}else{
    
    			return $this->getList("pt_name='{$seek}'",array('pt_name,pt_id,dateline'),array('pt_name'=>'DESC'),$page, 10, true);
    
    		}
    			
    			
    	}else{
    			
    		return $this->getList("parent_id=0",array('pt_name','pt_id','dateline','parent_id'),array('pt_name'=>'DESC'));
    	}
    
    }
    
    /**
     * 获取指定父级分组列表
     */
    public function getGroup($id=''){
        if(!intval($id)){$id=0;}
    	return $this->getList("parent_id=".$id, array('pt_name,pt_id'),array('pt_id'=>'DESC'));
    }
    
    
    /**
     * 获取子分组
     */
    public function getChildren($id){
        if(!intval($id)){return false;}    
    	return $this->getList("parent_id=".$id,array('pt_name,pt_id'));
    
    }
    
	/**
	 * 获取指定父级分组列表
	 */
	public function getType($id=''){
        $id=intval($id);
		return $this->getList("parent_id=".$id, array('pt_name,pt_id'),array('pt_name'=>'DESC'));
	}
	
	/**
	 * 查询分组名称
	 * @param unknown $name
	 * @return Ambigous <multitype:, false, boolean>
	 */
	public function exist_name($name){
	
		return $this->getOne("pt_name='{$name}'",'pt_name');
	
	}
	
	
	/**
	 * 获取分组名称
	 * @param unknown $id
	 * @return Ambigous <multitype:, false, boolean>
	 */
	public function getName($id){
	    if(!intval($id)){return false;}
		return $this->getOne($this->_PK."=".$id,'pt_name');
	
	}
	
	/**
	 * 获取编辑数据
	 * @param unknown $id
	 * @return Ambigous <multitype:, false, boolean>
	 */
	public function getOneById($id){     
	    if(!intval($id)){return false;}
		return $this->getOne($this->_PK."=".$id,array('pt_name,pt_id,parent_id'));
	
	}
	
	/**
	 * 获取父级分组,或查询指定id
	 */
	public function getParent($id){
	    if(!intval($id)){return false;}
		return $this->getOne($this->_PK."=".$id,array('pt_name,pt_id'));
	
	}
	
	/**
	 * 编辑更新处理
	 * @param unknown $id
	 * @param unknown $data
	 * @return Ambigous <number, false, boolean>
	 */
	public function updataById($id,$data){
	    if(!intval($id)){return false;}
		return $this->update($this->_PK."=".$id, $data);
	
	}
    
    
    /**
    * 得到产品类型数据集
    * 
    * @param array $srow pid为父级ID(0为顶级)
    * @return array
    */
    public function getRowsetAll($srow=array())
    {
        $condition='is_del=0';
        $pid=intval($srow['pid']);
        $condition.=" AND parent_id={$pid} ";
        
        $fields="pt_id,pt_name";
        $data=$this->getList($condition,$fields,' ORDER BY pt_id ASC',1,1000,0); 
        return $data['list']; 
    }
    
    /**
    * 得到产品类型数据集(根据id)
    * 
    * @param array $srow pid为父级ID(0为顶级)
    * @return array
    */
    public function getRowsetByIds($ids)
    {
        if(!$ids){return false;}
        $condition='is_del=0';
        
        $condition.=" AND {$this->_PK} IN ({$ids}) ";
        
        $fields="pt_id,pt_name";
        $data=$this->getList($condition,$fields,' ORDER BY pt_id ASC',1,1000,0); 
        return $data['list']; 
    }
    
    /**
     * 得到关键字列表所有
     *
     */
    public function getListAll($page)
    {
    	$condition ="parent_id=0"; //查询条件
    	return $this->getList($condition,array('*'),array('pt_id'=>'desc'),$page,$limit=20,true);
    }
    /**
     * 得到关键字列表所有
     *
     */
    public function getListAlltypeone($page)
    {
    	$condition ="parent_id=0"; //查询条件
    	return $this->getList($condition,array('*'),array('pt_id'=>'asc'),$page,$limit=20,true);
    }
    /**
     * 得到关键字列表所有
     *
     */
    public function getListAlltp()
    {
    	//$condition ="parent_id=0"; //查询条件
    	return $this->getList($condition,array('*'),array('pt_id'=>'asc'),1,$limit=1000,0);
    }
    /**
     * 得到关键字列表所有
     *
     */
    public function getListAllrow($page)
    {
    	$condition ="is_del=0"; //查询条件
    	return $this->getList($condition,array('*'),array('pt_id'=>'desc'),$page,$limit=400,true);
    }
    
    
    

    
}