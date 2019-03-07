<?php
//mogondb model基类
abstract class Base_mmodel extends SAbstract {
    public $_db;
    protected $_PK;
    protected $_tableName;
    protected $_cache = false;//缓存是否开启 默认关闭
    private $_cacheEngine;//memcached
    private $_cachename_name;//存储此缓存的缓存名的缓存名

	/**
     * 设置主健名称,该方法必需被子类覆盖
     */
    protected abstract function setPrimaryKey();

    /**
     * 设置当前表名,该方法必需被子类覆盖
     */
    protected abstract function setTableName();
    /**
     * 设置当前类,该方法必需被子类覆盖
     */
    protected abstract function setDataBase();
    
    /**
     * 设置当前是否缓存,该方法必需被子类覆盖
     */
    protected abstract function setCache();


    function __construct() {
    	parent::__construct();
		$this->setPrimaryKey();
		$this->setTableName();
		$this->setDataBase();
                $this->setCache();//初始化缓存信息
		$this->_cachename_name = 'CacheTable_mongo_'.$this->_tableName;
		
    }

    public function disconnect() {
    	if(is_object($this->_db)) $this->_db->close();
        unset($this->_db);
    }

    public function __destruct() {
    	if(is_object($this->_db)) $this->_db->close();
        unset($this->_db);
    }

    /**
     * 获取一行数据
     * @param array $condition 条件
     * @param array $item 查询字段
	 * @return array|false 数据 或失败
     */
    public function getOne($condition=array(), $item=array()) {
    	$collection = $this->connect('query');
    	return $collection->findOne($condition,$item);
    }

	/**
     * 获取列表下面是使用示例
     * @param array $condition 条件
     * @param array $item 查询字段
	 * @param array $order 排序 array('id'=>'asc');只支持一个order by ;
	 * @param int $limit 限制查询条数
	 * @param int $page 页数
	 * @return array|false 数据 或失败
	 *  SELECT * FROM users WHERE age>33				$db->users->find(array("age" => array('$gt' => 33)));
		SELECT * FROM users WHERE age<33			$db->users->find(array("age" => array('$lt' => 33)));
		SELECT * FROM users WHERE name LIKE "%Joe%"	$db->users->find(array("name" => new MongoRegex("/Joe/")));
		SELECT * FROM users WHERE name LIKE "Joe%"	$db->users->find(array("name" => new MongoRegex("/^Joe/")));
		SELECT * FROM users WHERE age>33 AND age<=40	$db->users->find(array("age" => array('$gt' => 33, '$lte' => 40)));

     */
    public function getList($condition=array(), $item=array(), $order = array(), $page = 1, $limit = 10) {
		if( ! is_array($condition)){
			$this->setError($code=-99, $msg='缺少必要参数!');
			return false;
		}
                $page = intval($page);
		$collection = $this->connect('query');
		if($item=='*'){
			$item = array();
		}elseif (is_array($item)){
			
		}elseif (is_string($item)){
			$item = explode(',', $item);
		}
		if($page<1)$page=1;
		if(!empty($order)){
			if(is_string($order)){
				$order=trim(str_replace('order by','',$order));
				$order=explode(' ',$order);
				foreach ($order as $key=>$value){
					$v=explode(' ',$value);
					if($v[1]=='asc'){
						$value = 1;
					}else{
						$value = -1;
					}
					$neworder[$v[0]] = $value;
					break;
				}
			}elseif (is_array($order)){
				foreach ($order as $key => $value){
					if($value=='asc'){
						$value = 1;
					}else{
						$value = -1;
					}
					$neworder[$key] = $value;
				}
			}
		}else{
			$neworder = array($this->_PK=>-1);
		}
		if($limit>0){//采用分页
			$skip = $limit*($page-1);
			$list = $collection->find($condition)->sort($neworder)->limit($limit)->skip($skip);
		}else{
			$list = $collection->find($condition)->sort($neworder)->limit(150);//未作限制最多显示150条
		}
		if($list){
			$rlist = array();
			while ($list->hasNext()) {
				$rlist[] = $list->getNext();
			}
		}
		return $rlist;
	}

	/**
	* 获取行数
	* @param int $page 页数
	* @return int|false 行数 或 失败
	*/
	public function getCount($condition=array()){
		$collection = $this->connect('query');
		return $collection->count($condition);
	}

	 /**
     * update
     * @param array $condition 更新条件
	 * @param array $data 更新内容
	 * @return int|false 更新行数 或 失败
     */
    public function update($condition,$data) {
		if(empty($condition) || empty($data)){
			$this->setError($code=-99, $msg='缺少必要参数!');
			return false;
		}
		$collection = $this->connect('main');
		$row = $collection->update($condition, array('$set' => $data),array('multiple'=>true));
		if($row['err']){
			$this->setError($code=-98, $msg=$row['err']);
			return false;
		}
		return $row['n'];
    }

	 /**
     * insert
     * @param array $data	欲插入的数据
	 * @return int|false	新插入行的id 或 失败
     */
    public function insert($data) {
		if(empty($data)){
			$this->setError($code=-99, $msg='缺少必要参数!');
			return false;
		}
		$collection = $this->connect('main');		
		return $collection->insert($data);
    }

    /**
     * delete
     * @param array $condition 更新条件
	 * @return int|false 删除行数 或 失败
     */
    public function delete($condition) {
		if(empty($condition)){
			$this->setError($code=-99, $msg='缺少必要参数!');
			return false;
		}
		$collection = $this->connect('main');
		return $collection->remove($condition,array('justOne'=>false));
    }

//add by wukai	
	public function group($condition, $initial, $key, $reduce) {
		//$key = array('qid'=>true);   //$key没有指定分组依据，那么所有文档认为属于同一组
		//$initial = array("count"=>0,"_id" => array(),"qid" => array());
		// 使用js將要顯示欄位的值塞入
		//$reduce = "function (obj, prev) {
    	//prev.count++;
    	//prev._id.push(obj._id);
    	//prev.qid.push(obj.qid);
		//}";
		$collection = $this->connect('main');
		$condition = array('condition'=>$condition);
		$ret = $collection->group($key, $initial, $reduce, $condition);
		return $ret;
	}
	
    private function connect($db='main'){
    	$mongodb = new SMongodb($db);
    	$this->_db = SMongodb::$_db;
    	//选择数据库
    	$dbName = $this->_dataBase;
        $db = $this->_db->$dbName;
        //制定结果集（表名：）
        $tableName = $this->_tableName;
        $collection = $db->$tableName;
        return $collection;
    }
}


/**
	End file,Don't add ?> after this.
*/
