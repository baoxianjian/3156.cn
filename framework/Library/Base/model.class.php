<?php
/**
 参数备忘	select($table,$condition="",$item="*",$groupby="",$orderby="",$leftjoin="",$params=array('type'=>'query'))
			selectOne($table,$condition="",$item="*",$groupby="",$orderby="",$leftjoin="",$params=array('type'=>'query'))
			update($table,$condition="",$item="",$params=array('type'=>'main'))
			delete($table,$condition="",$params=array('type'=>'main'))
			insert($table,$item="",$isreplace=false,$isdelayed=false,$update=array(),$params=array('type'=>'main'))
			query($table,$sql,$bind1=array(),$bind2=array(),$params=array())
 */
abstract class Base_model extends SAbstract {

    public $_db;
    protected $_PK;
    protected $_tableName;
    protected $_cache = false;//缓存是否开启 默认关闭
    private $_cacheEngine;//memcached
    private $_cachename_name;//存储此缓存的缓存名的缓存名
    private $readdb = 'query';//查询库可以修改，当单独提出台服务器来做后台统计时，可以使用,也可在查询必须走主库时使用

	/**
     * 设置主健名称,该方法必需被子类覆盖
     */
    protected abstract function setPrimaryKey();

    /**
     * 设置当前表名,该方法必需被子类覆盖
     */
    protected abstract function setTableName();
    /**
     * 设置当前是否缓存
     */
    protected abstract function setCache();
	
	
    function __construct() {
    	parent::__construct();
        $this->_db = SDb::getDbEngine("pdo_mysql");
		$this->setPrimaryKey();
		$this->setTableName();
		$this->setCache();//初始化缓存信息
		$this->_cachename_name = 'CacheTable_'.$this->_tableName;
    }
    

    public function __destruct() {
    	$this->_db = null;
        unset($this->_db);
    }

    /**
     * 打印sql错误 调试时使用
     *
     */
    public function debug() {
        echo $this->_db->sql;
        echo "<br/>";
        print_r($this->_db->error); 
    }
    /**
     * 设置从库信息
     * @param unknown $readdb
     */
    public function setReaddb($readdb){
    	$this->readdb = $readdb;
    }

    /**
     * 获取一行数据
     * @param array|string $condition 条件
     * @param array|string $item 查询字段
     * @param array|string $leftjoin 联表信息
     * @param bool		   $islock 是否行锁
	 * @return array|false 数据 或失败
     */
    public function getOne($condition='', $item='*',$leftjoin="",$islock=false) {
    	$data = array();
    	if ($this->_cache==true&&$islock==false) {
        	$cacheKey = array(
	        		'condition' => $condition,
	        		'item' => $item,
	        		'leftjoin' => $leftjoin
	        	);
        	$data = $this->getCachedata($cacheKey);
        }
        if (empty($data)) {
        	$this->_db->setIslock($islock);
        	$data = $this->_db->selectOne($this->_tableName, $condition, $item,$groupby="",$orderby="",$leftjoin,$params=array('type'=>$this->readdb));
        	if ($this->_cache==true&&$data) {//缓存开启，那么再存缓存
				$this->setCachedata($cacheKey, $data);
			}
        }
    	return $data;
    }

	/**
     * 获取列表  
     * 把$limit和$page调换了顺序 update by baoxianjian 17:27 2015/3/21 
     * @param array|string $condition 条件
     * @param array|string $item 查询字段
	 * @param array|string $order 排序
	 * @param int $page 页数
     * @param int $limit 限制查询条数     
	 * @param boolean $iscount 是否查询总条数
	 * @return array|false 数据 或失败
     */
    public function getList($condition, $item='*', $order = '', $page = 1, $limit = '', $iscount=true, $leftjoin="") {
                //2015-04-13 逻辑错误，暂时注释 START
		/**if(!$condition){
			$this->setError($code=-99, $msg='缺少必要参数!');
			return false;
		}**/
        	$page=intval($page);
                //2015-04-13 逻辑错误，暂时注释 END
		$data = array();
		if ($this->_cache==true) {//缓存开启，那么先读缓存
			$cacheKey = array(
					'condition' => $condition,
					'item' => $item,
					'leftjoin' => $leftjoin,
					'order' => $order,
					'limit' => $limit,
					'page' => $page,
					'iscount' => $iscount
			);//缓存名字组合
			$data = $this->getCachedata($cacheKey);
		}
        
        
		if (empty($data)) {
			$this->_db->setPage($page);
			$this->_db->setLimit($limit);
			$this->_db->setCount($iscount);
			$dataObj = $this->_db->select($this->_tableName, $condition, $item, $groupby='', $order, $leftjoin,$params=array('type'=>$this->readdb));
			$data = array();
			$data['list'] = $dataObj->items;
			$data['count'] = $dataObj->totalSize;
			if ($this->_cache==true&&$data) {//缓存开启，那么再存缓存
				$this->setCachedata($cacheKey, $data);
			}
		}
		
        return $data;
    }
	
	/**
	* 获取行数
	* @param int $page 页数
	* @return int|false 行数 或 失败
	*/
	public function getCount($condition='',$groupby = '',$leftjoin=''){
        $this->_db->setCountOnly(true);
	//	$data = $this->_db->selectOne($this->_tableName, $condition, 'count(*) as tablesize',$groupby,$orderby="",$leftjoin="",$params=array('type'=>'count'));
           $data = $this->_db->select($this->_tableName, $condition, 'count(*) as tablesize',$groupby,$orderby="",$leftjoin="",$params=array('type'=>'count'));
        $this->_db->setCountOnly(false);
        return $data->totalSize;
	}

	 /**
     * update
     * @param array|string $condition 更新条件
	 * @param array $data 更新内容
	 * @return int|false 更新行数 或 失败
     */
    public function update($condition,$data) {
		if(empty($condition) || empty($data)){
			$this->setError($code=-99, $msg='缺少必要参数!');
			return false;
		}
		$update = $this->_db->update($this->_tableName, $condition, $data);
		($this->_cache==true&&$update) && $this->delCachedata();
        return $update;
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
		$lastid = $this->_db->insert($this->_tableName, $data);
		($this->_cache==true&&$lastid)&&$this->delCachedata();
        return $lastid;
    }
	
    /**
     * delete
     * @param array|string $condition 更新条件
	 * @return int|false 删除行数 或 失败
     */
    public function delete($condition) {
		if(empty($condition)){
			$this->setError($code=-99, $msg='缺少必要参数!');
			return false;
		}
		$this->_cache==true&&$this->delCachedata();
        return $this->_db->delete($this->_tableName, $condition);
    }
	
	/**
	 * 执行sql语句
	 * @param <string> $table 表名
	 * @param <string> $sql  sql语句
	 */
	public function query($table,$sql,$params=array('type'=>'main')){
		return $this->_db->query($table,$sql,array(),array(),$params);
	}
	/**
	 * 事务开启
	 */
	public function begin(){
		return $this->_db->beginTransaction($this->_tableName);
	}
	/**
	 * 事务提交
	 */
	public function commit(){
		return $this->_db->commit($this->_tableName);
	}
	/**
	 * 事务回滚
	 */
	public function back(){
		return $this->_db->rollBack($this->_tableName);
	}
	/**
	 * 存缓存
	 * @param array $cacheKeyArr
	 * @param array $data
	 */
	private function setCachedata($cacheKeyArr,$data){
		$this->getCacheEngine();
		//存储缓存信息24小时
		$time = 86400;
		//存储缓存数据
		$cacheName = $this->combName($cacheKeyArr);
		$this->_cacheEngine->set($cacheName,$data,$time);
		//存储缓存名
		$data_cachename = $this->_cacheEngine->get($this->_cachename_name);
		$data_cachename[] = $cacheName;
		$this->_cacheEngine->set($this->_cachename_name,$data_cachename);
		return true;
	}
	/**
	 * 取缓存
	 * @param array $cacheKeyArr
	 * @return array $data
	 */
	private function getCachedata($cacheKeyArr){
		$this->getCacheEngine();
		$cacheName = $this->combName($cacheKeyArr);
		return $this->_cacheEngine->get($cacheName);
	}
	/**
	 * 删除此表下所有的缓存
	 * @param array $cacheKeyArr
	 * @return array $data
	 */
	public function delCachedata(){
		$this->getCacheEngine();
		$data_cachename = $this->_cacheEngine->get($this->_cachename_name);
		if (is_array($data_cachename)) {
			foreach ($data_cachename as $value){
				$this->_cacheEngine->del($value);//删除此表下所有缓存
			}
		}
		$this->_cacheEngine->del($this->_cachename_name);//删除缓存名
		return true;
	}
	private function getCacheEngine(){
		$this->_cacheEngine = new SCache('memcache','default');
	}
	//组合名称
	private function combName($array){
		ksort($array);
		foreach ($array as $key=>$value){
			if(is_array($value)){
				$array[$key] = $this->combName($value);
			}
		}
		return $this->_tableName.'_'.md5(json_encode($array));
	}
	
/**
	 * 数据保存
	 * @param mixed $pkid  当存在主健值是，为更新否则为插入，$pkid传入false时为插入
	 * @param array $data  要更新的数据
	 * @param boolean $lock 是否锁表,公用于更新操作
	 */
	public function save($pkid,$data,$lock = false){
		if (!$data){
			return true;
		}
		$tableName = $this->_tableName;
		if (!$tableName){
			$this->setError(0, 'model缺少表名');
			return false;
		}
		$primarykey = $this->_PK;
		if (!$primarykey){
			$this->setError(0, 'model缺少主键');
			return false;
		}
		if ($pkid){//存在主健值则为修改
			$condition = array(
				$primarykey => $pkid,
			);
			if ($lock == true){
				$rowCount = $this->_db->update($tableName,$condition,$data,array('lock'=>1));
			} else {
				$rowCount = $this->_db->update($tableName,$condition,$data);
			}
			if ($rowCount === false){
				$this->setError(0, $this->getDbError());
				return false;
			} else {
				($this->_cache==true&&$rowCount)&&$this->delCachedata();
				return true;
			}
		} else {//不存在主健值为插入
			$lastInsertID = $this->_db->insert($tableName,$data);
			($this->_cache==true&&$lastInsertID)&&$this->delCachedata();
			return $lastInsertID;
		}
	}
    
    /**
    * 解析ids，方便IN查询
    * 
    * @param string $ids
    * @return string
    */
    public function parseIds($ids,$arr=0)
    {
        $id_list=$ids;
        if(!is_array($ids))
        {
            $id_list=explode(',', $ids);
        }
        foreach ($id_list as $k=>$v)
        {
            $id_list_temp[intval($v)]=1;
        }
        $id_list_temp=array_keys($id_list_temp);
        if($arr)
        {
            return $id_list_temp;
        }
        
        $ids=implode(',', $id_list_temp);

        if(!$ids){return false;}
        return $ids;
    }
    
     /**
    * 根据主键id删除一行数据
    * add by baoxianjian 10:20 2015/4/25
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
    * 批量删除记录,根据id数组或id字符串(1,2,3)
    * add by baoxianjian 23:01 2015/4/25
    * @param string $ids
    * @param mixed $destroy
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
    }
    

    /**
    * 添加一条记录 
    * add by baoxianjian 10:20 2015/4/25  
    * @param array $row
    * @return int|false
    */
    public function addRow($row)
    {
        if(!$row) return false;
        return $this->insert($row);
    }
    
    /**
    * 修改一条记录（根据Id）
    * add by baoxianjian 10:20 2015/4/25  
    * @param array $row
    * @param int $id
    * @return int|false
    */
    public function updateRowById($row,$id=0)
    {
        //die(SUtil::P($row));
        if(!$row) return false;
        if(!$id=intval($id)){return false;}

        unset($row[$this->_PK]);
        return $this->update($this->_PK.'='.$id,$row);
        //return $this->delete(array($this->_PK=>$id));
    }
    
    /**
    * 得到一条记录（根据id）
    * add by baoxianjian 10:20 2015/4/25  
    * @param int $id
    * @return array|false
    */
    public function getRowById($id)
    {
        if(!$id=intval($id)){return false;}
        return $this->getOne(array($this->_PK=>$id),'*');
    }
    
    /**
    * 得到模块类型列表
    * add by baoxianjian 14:48 2015/4/26    
    */
    public function getModTypes()
    {
        //mod_type=>mod_name
        return array('1'=>'会员','2'=>'厂商','3'=>'产品','4'=>'资讯','5'=>'代理');
    }
    
    public function searchList($page,$url,$qstr,$limit=10)
    {
        $page=intval($page);
        if(!$limit=intval($limit))
        {
             $limit=10;
        }
        
        //  $limit=10;
        $start=($page-1)*$limit;    //0,10   10,10  20,10

        $r_url=SOLR_URL."/{$url}?{$qstr}&wt=json&start={$start}&rows={$limit}";
        
        $rc=SRemote::getCurlContent($r_url);
        
        $r_list = json_decode($rc,true);
        
        if($r_list['grouped'])
        {
            $r_gf=$r_list['responseHeader']['params']['group.field'];
            $r_list_temp_grouped = $r_list['grouped'][$r_gf]['groups']; 
            $r_list_temp['count'] = $r_list['grouped'][$r_gf]['ngroups'];
            
            $i=0;
            foreach ($r_list_temp_grouped as $val)
            {
                foreach ($val['doclist']['docs'] as $val2)
                {
                    $r_list_temp['list'][$i]=$val2;
                    $i++; 
                }
                
            }
        }
        else
        {
            $r_list = $r_list['response'];
            $r_list_temp['count'] = $r_list['numFound'];
            $r_list_temp['list'] = $r_list['docs']; 
        }
        
        
        
        if(defined('DEBUG'))
        {
            echo $r_url,'<br/>'; 
            print_r($r_list); 
            exit;
        }

        unset($r_list);
        return $r_list_temp;
    }

}


/**
	End file,Don't add ?> after this.
*/
