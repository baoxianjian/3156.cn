<?php
/*{{{LICENSE
+-----------------------------------------------------------------------+
| SlightPHP Framework                                                   |
+-----------------------------------------------------------------------+
| This program is free software; you can redistribute it and/or modify  |
| it under the terms of the GNU General Public License as published by  |
| the Free Software Foundation. You should have received a copy of the  |
| GNU General Public License along with this program.  If not, see      |
| http://www.gnu.org/licenses/.                                         |
| Copyright (C) 2008-2009. All Rights Reserved.                         |
+-----------------------------------------------------------------------+
| Supports: http://www.slightphp.com                                    |
+-----------------------------------------------------------------------+
}}}*/

/**
 * @package SlightPHP
 * @subpackage SDb
 */
class Db_PDO extends DbObject{
	/**
	 *
	 */
	//private $mysql;

	/**
	 *
	 */
	public $host;
	/**
	 *
	 */
	public $port=3306;
	/**
	 *
	 */
	public $user;
	/**
	 *
	 */
	public $password;
	/**
	 *
	 */
	public $database;
	/**
	 *
	 */
	public $charset;
	/**
	 *
	 */
	public $orderby;
	/**
	 *
	 */
	public $groupby;
	/**
	 *
	 */
	public $sql;
	/**
	 *
	 */
	public $count=false;
	/**
	 *
	 */
    public $countOnly=false;
    
	public $limit=0;
	/**
	 *
	 */
	public $page=1;
	/**
	 *
	 */
	private $prefix;
	private $countsql;
	private $affectedRows=0;
	
	//是否行锁
	private $islock = false;
	
	

	/**
	 * @var array $globals
	 */
	static $globals;
	/**
	 * @param field_type $_lock
	 */
	public function setIslock($_lock) {
		$this->islock = $_lock;
	}

	function __construct($prefix="mysql"){
		$this->prefix=$prefix;
	}
	/**
	 * construct
	 *
	 * @param string host
	 * @param string user
	 * @param string password
	 * @param string database
	 * @param int port=3306
	 */
	function init($params=array(),$table=''){
		$pary = explode('_',$table);
		$dbInfo = $pary[0];//获取表前缀
		if(!empty($params['type'])) {
			$type = $params['type'];
		} else {
			$type = 'main';
		}
		$dbConfig = SDb::getConfig($dbInfo,$type);//获取数据库配置文件

		if(!empty($dbConfig)) {
			foreach($dbConfig as $key=>$value){
				$this->$key = $value;
			}

			$this->key = $this->prefix.":".$this->host.":".$this->user.":".$type.":".$pary[0];
			if(!isset(Db_PDO::$globals[$this->key])) Db_PDO::$globals[$this->key] = "";
		}
	}
	/**
	 * is count
	 *
	 * @param boolean count
	 */
	function setCount($count)
	{
		if($count==true){
			$this->count=true;
		}else{
			$this->count=false;
		}
	}
    
    function setCountOnly($co)
    {
        $this->count=true;
        $this->countOnly=$co;
    }
    
	/**
	 * page number
	 *
	 * @param int page
	 */
	function setPage($page)
	{
		if(!is_numeric($page) || $page<1){$page=1;}
		$this->page=$page;
	}
	/**
	 * page size
	 *
	 * @param int limit ,0 is all
	 */
	function setLimit($limit)
	{
		if(!is_numeric($limit) || $limit<0){$limit=0;}
		$this->limit=$limit;
	}
	/**
	 * group by sql
	 *
	 * @param string groupby
	 * eg:	setGroupby("groupby MusicID");
	 *      setGroupby("groupby MusicID,MusicName");
	 */
	function setGroupby($groupby)
	{
		$this->groupby=$groupby;
	}
	/**
	 * order by sql
	 *
	 * @param string orderby
	 * eg:	setOrderby("order by MusicID Desc");
	 */
	function setOrderby($orderby)
	{
		$this->orderby=$orderby;
	}

	/**
	 * select data from db
	 *
	 * @param mixed $table
	 * @param array $condition
	 * @param array $item
	 * @param string $groupby
	 * @param string $orderby
	 * @param string $leftjoin
	 * @return DbData object || Boolean false
	 */
	function select($table,$condition="",$item="*",$groupby="",$orderby="",$leftjoin="",$params=array('type'=>'query')){
		//{{{$item
		if($item==""){$item="*";}
		if(is_array($table)){
			for($i=0;$i<count($table);$i++)
			{
				$tmp[]=trim($table[$i]);
			}
			$table=@implode(" , ",$tmp);
		}else{
			$table=trim($table);
		}

		if(is_array($item)&&!empty($item)){
			$item =@implode(",",$item);
		}
		//}}}
		//{{{$condition
		$condiStr = $this->__quote($condition,"AND",$bind);
        

		if($condiStr!=""){
			$condiStr=" WHERE ".$condiStr;
		}

		//}}}
		//{{{
		$join="";
		if(is_array($leftjoin)){
			foreach ($leftjoin as $key=>$value){
				$join.=" LEFT JOIN $key ON $value ";
			}
		}
		else
		{
			$join=$leftjoin;
		}

		//}}}
		//{{{
		$this->groupby  =$groupby!=""?$groupby:$this->groupby;
		$this->orderby  =$orderby!=""?$orderby:$this->orderby;

		$orderby_sql="";
		$orderby_sql_tmp = array();
		if(is_array($orderby)){
			foreach($orderby as $key=>$value){
				if(!is_numeric($key)){
					$orderby_sql_tmp[]="`{$key}` {$value}";
				}
			}
		}else{
			$orderby_sql=$this->orderby;
		}
		if(count($orderby_sql_tmp)>0){
			$orderby_sql=" ORDER BY ".implode(",",$orderby_sql_tmp);
		}
		//}}}

		$limit="";
		if($this->limit!=0){
			$limit    =($this->page-1)*$this->limit;
			$limit ="LIMIT $limit,$this->limit";
		}
		$this->sql="SELECT $item FROM $table $join $condiStr $groupby $orderby_sql $limit";
	
        
		//直接判断是否锁定
		if ($this->islock==true) {
			$params['type'] = 'main';
			$this->sql .= ' for update ';
		}
		
//print $this->sql;die;
		if($groupby!=''){
			$this->countsql="SELECT count(1) totalSize FROM (SELECT 1 FROM $table $join $condiStr $groupby) ss";
		}else{
			$this->countsql="SELECT count(1) totalSize FROM $table $join $condiStr $groupby";
		}
		
		$data = new DbData;
		$data->page = $this->page;
		$data->limit = $this->limit;
		$start = microtime(true);


		$data->limit = $this->limit;
	//die(print_r($this->sql));
		
        if(!$this->countOnly)
        {
		    $data->items = $this->query($table,$this->sql,$bind,null,$params);
     
		/* die(var_dump($data->items)); */
		if($data->items === false){
			return false;
		}
		$data->pageSize = count($data->items);
		$end = microtime(true);
		$data->totalSecond = $end-$start;
		//}}}

        }
		//{{{
		if($this->count==true and $this->countsql!=""){
			$result_count = $this->query($table,$this->countsql,$bind,null,$params);
			$data->totalSize = $result_count[0]['totalSize'];
			$data->totalPage = ceil($data->totalSize/$data->limit);
		}
		//}}}

		//清空page,limit等设置
		$this->setCount(false);
        	$this->setCountOnly(false);
		$this->setPage(1);
		$this->setLimit(0);
		$this->setGroupby("");
		$this->setOrderby("");
		//die(print_r($this->sql));
		return $data;
	}
	/**
	 *
	 *
	 * @param mixed $table
	 * @param array $condition
	 * @param array $item
	 * @param string $groupby
	 * @param string $orderby
	 * @param string $leftjoin
	 * @return array item
	 */
	function selectOne($table,$condition="",$item="*",$groupby="",$orderby="",$leftjoin="",$params=array('type'=>'query'))
	{
		$this->setLimit(1);
		$this->setCount(false);
		$data=$this->select($table,$condition,$item,$groupby,$orderby,$leftjoin,$params);
		if(isset($data->items[0]))
		return $data->items[0];
		else return false;

	}

	/**
	 * update data
	 *
	 * @param mixed $table
	 * @param string,array $condition
	 * @param array $item
	 * @param int $limit
	 * @return int
	 * update("table",array('name'=>'myName','password'=>'myPass'),array('id'=>1));
	 * update("table",array('name'=>'myName','password'=>'myPass'),array("password=$myPass"));
	 */
	function update($table,$condition="",$item="",$params=array('type'=>'main')){
		$value = $this->__quote($item, "," ,$bind_v);//字符串原样返回
		$condiStr = $this->__quote($condition,"AND",$bind_c);
		if($condiStr!=""){
			$condiStr=" WHERE ".$condiStr;
		}
		$this->sql="UPDATE $table SET $value $condiStr";
		//die($this->sql);
		if($this->query($table,$this->sql,$bind_v,$bind_c,$params)!==false){
			return $this->rowCount();
		}else{
			return false;
		}
	}
	/**
	 * delete
	 *
	 * @param mixed table
	 * @param string,array $condition
	 * @param int $limit
	 * @return int
	 * delete("table",array('name'=>'myName','password'=>'myPass'),array('id'=>1));
	 * delete("table",array('name'=>'myName','password'=>'myPass'),array("password=$myPass"));
	 */
	function delete($table,$condition="",$params=array('type'=>'main')){
		$condiStr = $this->__quote($condition,"AND",$bind);
		if($condiStr!=""){
			$condiStr=" WHERE ".$condiStr;
		}
		$this->sql="DELETE FROM  $table $condiStr";
		if($this->query($table,$this->sql,$bind,null,$params)!==false){
			return $this->rowCount();
		}else{
			return false;
		}
	}
	/**
	 * insert
	 *
	 * @param $table
	 * @param array $item
	 * @param array $update ,egarray("key"=>value,"key2"=>value2")
		 insert into zone_user_online values(2,'','','','',now(),now()) on duplicate key update onlineactivetime=CURRENT_TIMESTAMP;
	 * @return int InsertID
	 */
	function insert($table,$item="",$isreplace=false,$isdelayed=false,$update=array(),$params=array('type'=>'main'))
	{
		if($isreplace==true){
			$command="REPLACE";
		}else{
			$command="INSERT";
		}
		if($isdelayed==true){
			$command.=" DELAYED ";
		}

		$f = $this->__quote($item,",",$bind_f);

		$this->sql="$command INTO $table SET $f ";
		$v = $this->__quote($update,",",$bind_v);
		if(!empty($v)){
			$this->sql.="ON DUPLICATE KEY UPDATE $v";
		}
		
 	//	die($this->sql);
		
		$r=$this->query($table,$this->sql,$bind_f,$bind_v,$params);
		if($r!==false){
			if($this->lastInsertId ()>0){
				return $this->lastInsertId ();
			}elseif($this->affectedRows >0){
				return $this->affectedRows;
			}
		}
		return $r;
	}

	/**
	 * query
	 *
	 * @param string $sql
	 * @return Array $result  || Boolean false
	 */

	public function query($table,$sql,$bind1=array(),$bind2=array(),$params=array('type'=>'main'))
	{
		if(defined("DEBUG")){
			$time=time();
			echo "<br/>\r\n(time:{$time})SQL:$sql\n";
			print_r($bind1);
			print_r($bind2);
		}
		if(empty($sql)) {
			//执行SQL不能为空
			return false;
		}
		//检查sql安全性
		if(!$this->isSafe($sql)) {
			//sql存在风险
			return false;
		}
		$stmt = $this->getPDO($table,$params)->prepare($sql);
		
		if(!$stmt){
			$this->error['code']=Db_PDO::$globals[$this->key]->errorCode ();
			$this->error['msg']=Db_PDO::$globals[$this->key]->errorInfo ();
			return false;
		}
		if(!empty($bind1)){
			foreach($bind1 as $k=>$v){
				$stmt->bindValue($k,$v);
			}
		}

		if(!empty($bind2)){
			foreach($bind2 as $k=>$v){
				$stmt->bindValue($k + count($bind1),$v);
			}
		}
		
		if($stmt->execute ()){
			$this->affectedRows = $stmt->rowCount();
			return $stmt->fetchAll (PDO::FETCH_ASSOC );
		}else{
			$this->error['code']=$stmt->errorCode ();
			$this->error['msg']=$stmt->errorInfo ();

			if(defined("DEBUG")||1){
				print_r($this->error['msg']);
			}
		}
		return false;

	}
	private function lastInsertId(){
		return Db_PDO::$globals[$this->key]->lastInsertId ();
	}
	private function rowCount(){
		return $this->affectedRows;
	}

	/**
	 *
	 * @param string $sql
	 * @return array $data || Boolean false
	 */
	function execute($sql,$table='',$params=array('type'=>'main')){
		return $this->query($table,$sql,$bind1=array(),$bind2=array(),$params);
	}

	private function __connect($forceReconnect=false,$params=array('type'=>'main')){
		if(empty(Db_PDO::$globals[$this->key]) || $forceReconnect){
			if(!empty(Db_PDO::$globals[$this->key])){
				unset(Db_PDO::$globals[$this->key]);
			}
			try{
				Db_PDO::$globals[$this->key] = new PDO($this->prefix.":dbname=".$this->database.";host=".$this->host.";port=".$this->port,$this->user,$this->password);
			}catch(Exception $e){
				die("connect database error:\n".var_export($this,true));
				//die("connect database error :" .$this->database);
			}
		}
		if(!empty($this->charset)){
			$this->execute("SET NAMES ".$this->charset,'',$params);
		}
	}

	private function __quote($condition,$split="AND",&$bind){
		$condiStr = "";
		if(!is_array($bind)){$bind=array();}
		if(is_array($condition)){
			$v1=array();
			$i=1;
			foreach($condition as $k=>$v)
			{
				if(!is_numeric($k))//判断是否为索引数组
				{	
					if(strpos($k,".")>0 || is_int(strpos($k,"`"))){//提取键名
						$v1[]="$k='$v'";
					}else{
						$v1[]="`$k`='$v'";
					}
					$bind[$i++]=$v;//提取键值
				}else{
					$v1[]=($v);
				}
			}
			
			if(count($v1)>0)
			{	
				$condiStr=implode(" ".$split." ",$v1);//拆分键值数组

			}
		}else{
			$condiStr=$condition;
		}
		return $condiStr;
	}

	public function getPDO($table,$params=array('type'=>'query')){
		$this->init($params,$table);
		if(empty(Db_PDO::$globals[$this->key])){
			$this->__connect($forceReconnect=true,$params);
		}
		return Db_PDO::$globals[$this->key];
	}

	/**
	 * 开始事务
	 */
	public function beginTransaction($table,$params=array('type'=>'main')){
		$this->getPDO($table,$params)->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
		return $this->getPDO($table,$params)->beginTransaction();
	}

	/**
	 * 提交事务
	 */
	public function commit($table,$params=array('type'=>'main')){
		$returns = $this->getPDO($table,$params)->commit();
		$this->getPDO($table,$params)->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
		return $returns;
	}

	/**
	 * 回滚事务
	 */
	public function rollBack($table,$params=array('type'=>'main')){
		$returns = $this->getPDO($table,$params)->rollBack();
		$this->getPDO($table,$params)->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
		return $returns;
	}
	/**
	 * 检查sql的是否安全
	 * @param string $sql sql语句
	 * return boolean
	 */
	public function isSafe($sql) {
		$clean = '';
		$error='';
		$old_pos = 0;
		$pos = -1;
		while (true){
			$pos = strpos($sql, '\'', $pos + 1);
			if ($pos === false)
				break;
			$clean .= substr($sql, $old_pos, $pos - $old_pos);
	
			while (true){
				$pos1 = strpos($sql, '\'', $pos + 1);
				$pos2 = strpos($sql, '\\', $pos + 1);
				if ($pos1 === false)
					break;
				elseif ($pos2 == false || $pos2 > $pos1){
					$pos = $pos1;
					break;
				}
				$pos = $pos2 + 1;
			}
			$clean .= '$s$';
			$old_pos = $pos + 1;
		}
	
		$clean .= substr($sql, $old_pos);
		$clean = trim(strtolower(preg_replace(array('~\s+~s' ), array(' '), $clean)));
	
		//老版本的Mysql并不支持union，常用的程序里也不使用union，但是一些黑客使用它，所以检查它
		if (strpos($clean, ' union ') !== false && preg_match('~(^|[^a-z])union($|[^[a-z])~s', $clean) != 0) {
			$fail = true;
			$error="union detect";
			/*}elseif (strpos($clean, '/*') > 2 || strpos($clean, '--') !== false || strpos($clean, '#') !== false) {
				//发布版本的程序可能比较少包括--,#这样的注释，但是黑客经常使用它们
			$fail = true;
			$error="comment detect";*/
		}elseif (strpos($clean, ' sleep') !== false && preg_match('~(^|[^a-z])sleep($|[^[a-z])~s', $clean) != 0) {
			//这些函数不会被使用，但是黑客会用它来操作文件，down掉数据库
			$fail = true;
			$error="slown down detect";
		}elseif (strpos($clean, 'benchmark') !== false && preg_match('~(^|[^a-z])benchmark($|[^[a-z])~s', $clean) != 0) {
			$fail = true;
			$error="slown down detect";
		}elseif (strpos($clean, 'load_file') !== false && preg_match('~(^|[^a-z])load_file($|[^[a-z])~s', $clean) != 0) {
			$fail = true;
			$error="file fun detect";
		}elseif (strpos($clean, 'into outfile') !== false && preg_match('~(^|[^a-z])into\s+outfile($|[^[a-z])~s', $clean) != 0) {
			$fail = true;
			$error="file fun detect";
			//}elseif (preg_match('~\([^)]*?select~s', $clean) != 0){
			//老版本的MYSQL不支持子查询，我们的程序里可能也用得少，但是黑客可以使用它来查询数据库敏感信息
			//	$fail = true;
			//	$error="sub select detect";
		}
		if (!empty($fail)) {
			$this->error['msg'] = $error;
			return false;
		} else {
			return true;
		}
	}
}
?>
