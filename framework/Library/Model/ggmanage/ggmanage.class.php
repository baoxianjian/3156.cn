<?php

class Model_ggmanage_ggmanage extends Base_model{
	//@override
	public function setPrimaryKey() {
		$this->_primaryKey = 'ggpg_id';
	}
	//@override
	public function setTableName() {
		$this->_tableName = 'gg_position_group';

	}
	public function setCache($status=false){
		$this->_cache = $status;
	}
	
	/**
	 * 获取指定父级分组列表
	 */
	public function getGroup($id=''){
		$id == '' && $id = 0;
		return $this->getList("parent_id=".$id, array('title,ggpg_id'),array('order'=>'ASC',ggpg_id=>'ASC'));
	}
	
	/**
	 * 获取所有未删除顶级分组列表
	 */
	public function getGroupList($page='',$seek=''){
		if ( $page != '' ){
			
			if ( $seek == '' ){
				
				return $this->getList(" parent_id=0",array('title,ggpg_id,dateline,intro'),array('order'=>'ASC','ggpg_id'=>'ASC'),$page, 10, true);
				
			}else{

				return $this->getList("title like '%{$seek}%' ",array('title,ggpg_id,dateline,intro'),array('order'=>'ASC','ggpg_id'=>'ASC'),$page, 10, true);
				
			}
			
			
		}else{
			
			return $this->getList("parent_id=0",array('title,ggpg_id,dateline,intro'),array('ggpg_id'=>'ASC'));
			
		}
		
	}

	public function count($seek=''){
		
		if ( $seek == '' ){
			
			return $this->getCount("parent_id=0");
			
		}else{
			
			return $this->getCount("title='{$seek}'");
			
		}
		
	}
	
	/**
	 * 获取父级分组,或查询指定id
	 */
	public function getParent($id){

		return $this->getOne("ggpg_id=".$id,array('title,ggpg_id'));
		
	}
	
	/**
	 * 获取子分组
	 */
	public function getChildren($id){
		
		return $this->getList("parent_id=".$id,array('title,ggpg_id'));
		
	}
	
	/**
	 * 获取分组名称
	 * @param unknown $id
	 * @return Ambigous <multitype:, false, boolean>
	 */
	public function getName($id){
		
		return $this->getOne("ggpg_id=".$id,'title');
		
	}
	
	
	/**
	 * 查询分组名称
	 * @param unknown $name
	 * @return Ambigous <multitype:, false, boolean>
	 */
	public function exist_name($name){
		
		return $this->getOne("title='{$name}'",'title');
		
	}
	
	/**
	 * 获取编辑数据
	 * @param unknown $id
	 * @return Ambigous <multitype:, false, boolean>
	 */
	public function getOneById($id){
		
		return $this->getOne("ggpg_id=".$id,array('title,ggpg_id,parent_id','intro','`order`'));
		
	}
	
	/**
	 * 编辑更新处理
	 * @param unknown $id
	 * @param unknown $data
	 * @return Ambigous <number, false, boolean>
	 */
	public function updataById($id,$data){
		
		return $this->update("ggpg_id=".$id, $data);
		
	}
	
	/**
	 * 按条件获取列
	 * @param unknown $condition
	 * @param unknown $val
	 * @param string $condStr
	 * @param string $arr
	 * @return Ambigous <multitype:, false, boolean>
	 */
	public function getCondition($condition, $val, $condStr, $arr){
		return $this->getOne($condition.$condStr.$val, $arr);	
	}
	
	/**
	 * 自动加载方法
	 * @param unknown $medthod
	 * @param unknown $args
	 * @return Ambigous <Ambigous, multitype:, false, boolean>
	 */
	public function __call($medthod, $args){
		$condition = preg_replace('/findBy(\w{1,})/is', '$1', $medthod);
		$args[1] == NULL && $args[1] = '=';
		$args[2] == NULL && $args[2] = '*';
		return $this->getCondition($condition, $args[0], $args[1], $args[2]);
		//die($condition);
	}
	
}
/**
 End file,Don't add ?> after this.
 */
