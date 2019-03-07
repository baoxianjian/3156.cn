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
date_default_timezone_set('PRC');
//echo date("Y-m-d H:I:s",time());exit;
#所有网站全局配置
define('ROOT_DOMAIN','3156.test');
//define('WEB_STATIC',1); 
define('BLOCK_ORIGIN_DIR','../blocks');  
define('BLOCK_MAX_EXPIRY_TIME',86400); //区块最大过期时间  add by baoxianjian 12:02 2015/5/13

define('GOTO_URL','http://count.'.ROOT_DOMAIN.'/main/goto');    //add by baoxianjian 23:01 2015/5/13
define('WWW_ONLINE_URL','http://www.3156.test'); //add by baoxianjian 16:37 2015/4/29 线上运行地址
define('WWW_URL','http://www.'.ROOT_DOMAIN);
define('NEWS_URL','http://zixun.'.ROOT_DOMAIN);
define('BZ_URL','http://bingzheng.'.ROOT_DOMAIN);
define('IMG1_URL','http://img1.'.ROOT_DOMAIN);
define('USER_URL','http://user.'.ROOT_DOMAIN);

define('SOLR_URL','http://211.147.6.220:18087/solr');

//array(44,45,46,47)
define('GG_REFRESH_GROUP_IDS','44,45,46,47');
define('WEB_STATIC_ALL',1);






//define('WEB_STATIC',0);
/**
 * @package SlightPHP
 */
final class SlightPHP{
	/**
	 * 项目移动目录
	 * @var string
	 */
	private static $appDir=".";

	/**
	 * Controller目录
	 * @var string
	 */
	private static $controller='Controller';//默认controller目录是APPDIR目录下的Controller目录
	
	/**
	 * Controller 类名称
	 * @var string
	 */
	private static $page;
	private static $defaultPage="main";

	/**
	 * 方法名 
	 * @var string
	 */
	private static $entry;
	private static $defaultEntry="index";
	//参数分隔符
	private static $splitFlag="-";

	//设置默认的pathinfo
	private static $pathinfo = '';
	public static function setPathinfo($pathinfo){
		SlightPHP::$pathinfo = $pathinfo;
		return true;
	}
	
	public static function setController($value){
		SlightPHP::$controller=$value;
		return true;
	}
	
	public static function getController(){
		return SlightPHP::$controller;
	}
	/**
	 * defaultClass set
	 * 
	 * @param string $page
	 * @return boolean
	 */
	public static function setDefaultPage($page){
		SlightPHP::$defaultPage = $page;
		return true;
	}
	/**
	 * getDefaultClass get
	 * 
	 * @return string
	 */
	public static function getDefaultPage(){
		return SlightPHP::$defaultPage;
	}
	/**
	 * defaultMethod set
	 * 
	 * @param string $entry
	 * @return boolean
	 */
	public static function setDefaultEntry($entry){
		SlightPHP::$defaultEntry = $entry;
		return true;
	}
	/**
	 * defaultMethod get
	 * 
	 * @return string $method
	 */
	public static function getDefaultEntry(){
		return SlightPHP::$defaultEntry;
	}
	/**
	 * splitFlag set
	 * 
	 * @param string $flag
	 * @return boolean
	 */
	public static function setSplitFlag($flag){
		SlightPHP::$splitFlag = $flag;
		return true;
	}
	/**
	 * defaultMethod get
	 * 
	 * @return string
	 */
	public static function getSplitFlag(){
		return SlightPHP::$splitFlag;
	}
	/**
	 * appDir set && get
	 * IMPORTANT: you must set absolute path if you use extension mode(extension=SlightPHP.so)
	 *
	 * @param string $dir
	 * @return boolean
	 */

	public static function setAppDir($dir){
		SlightPHP::$appDir = $dir;
		return true;
	}
	/**
	 * appDir get
	 * 
	 * @return string
	 */
	public static function getAppDir(){
		return SlightPHP::$appDir;
	}

	/**
	 * main method!
	 *
	 * @param string $path
	 * @return boolean
	 */

	final public static function run($path=""){
		@date_default_timezone_set('PRC');
		mb_internal_encoding("UTF-8");
 
		if($_SERVER['SERVER_PORT'] == '443') define('HTTPS', true);
		//{{{


		$path_array = array();
		if(!empty($path)){
			$isPart = true;

            //add by baoxianjian 16:37 2015/4/21 使用参数解析器
            $path_array=SlightPHP::urlPaser($path);
            
			//$path_array = preg_split("/[$splitFlag\/]/",$path,-1,PREG_SPLIT_NO_EMPTY);
		}else{
			$isPart = false;
			//主要是用于Nginx不支持$_SERVER["PATH_INFO"]的问题
			if (SlightPHP::$pathinfo) {
				$url = SlightPHP::$pathinfo;       
			}elseif (!empty($_GET['PATH_INFO'])) {
				$url = $_GET['PATH_INFO'];
			}elseif (!empty($_SERVER["PATH_INFO"])){
				$url = $_SERVER["PATH_INFO"]; 
			}else{
				$url = '';
			}  
			//无论服务器支持不支持全部对PATH_INFO参数进行处理
			unset($_GET['PATH_INFO']);
			$_SERVER['QUERY_STRING'] = preg_replace('/PATH_INFO=[a-z0-9\-\/\.]*&?/', '', $_SERVER['QUERY_STRING']);
			if(!empty($url))
		            {
		                $path_array = preg_split("/[$splitFlag\/]/",$url,-1,PREG_SPLIT_NO_EMPTY);
		                //add by baoxianjian 16:37 2015/4/21 使用参数解析器
		                $path_array=SlightPHP::urlPaser($url);   
		            }
		}
       
       //echo SUtil::httpFilter("i")

		//SQL注入防御、XSS跨站攻击过滤
		$_POST = SUtil::httpFilter($_POST);
		$_GET = SUtil::httpFilter($_GET);
		$_REQUEST = SUtil::httpFilter($_REQUEST);
		$_COOKIE = SUtil::httpFilter($_COOKIE);
        $path_array = SUtil::httpFilter($path_array);
        
        
		$page	= !empty($path_array[0]) ? $path_array[0] : SlightPHP::$defaultPage ;
		$entry	= !empty($path_array[1]) ? $path_array[1] : SlightPHP::$defaultEntry ;
		

		if(!$isPart){
			SlightPHP::$page	= $page;
			SlightPHP::$entry	= $entry;
		}else{
			if($page == SlightPHP::$page && $entry == SlightPHP::$entry){
				SlightPHP::debug("part ignored [$path]");
				return;
			}
		}


		$app_file = SlightPHP::$appDir . DIRECTORY_SEPARATOR . SlightPHP::$controller . DIRECTORY_SEPARATOR . $page . ".page.php";
        if(!file_exists($app_file)){
			SlightPHP::debug("file[$app_file] not exists");
			return false;
		}else{
			require_once(realpath($app_file));
		}
		$method = "Page".$entry;
		$classname = SlightPHP::$controller ."_". $page;
		if(!class_exists($classname)){
			SlightPHP::debug("class[$classname] not exists");
			return false;
		}

        //update by baoxianjian 9:35 2015/3/22 
        //定义当前控制器的页面和动作
        define('CUR_PAGE',$page);
        define('CUR_ACTION',$entry);
        
		$classInstance = new $classname($path_array);
                         

                

		if(!method_exists($classInstance,$method)){
			SlightPHP::debug("method[$method] not exists in class[$classname]");
			return false;
		}

		$path_array[0] = $page;
		$path_array[1] = $entry;


		return call_user_func(array(&$classInstance,$method),$path_array);
	}
	

	/**
	 * @var boolean
	 */
	public static $_debug=0;
	/**
	 * debug status set
	 *
	 * @param boolean $debug
	 * @return boolean
	 */
	public static function setDebug($debug){
		SlightPHP::$_debug = $debug;
		return true;
	}
	/**
	 * debug status get
	 * 
	 * @return boolean 
	 */
	public static function getDebug(){
		return SlightPHP::$_debug;
	}
	/*private*/
	private static function debug($debugmsg){
		if(SlightPHP::$_debug){
			error_log($debugmsg);
			//echo "<!--debug: ".$debugmsg."-->";
		}
	}
    
    /**
    * url参数解析器
    * add by baoxianjian 16:37 2015/4/21
    * @param string $q query string
    * @return array
    */
    private static function urlPaser($q)
    {
      //      $q='main/index-url-http://www.3156.test/main/index-id--name-baoge';
      ///main/index/-id-1
       // $q=strtolower($q);  //MAIN/INDEX  //
        $q=str_replace('http:/','http://',$q);
        //$last_index=strlen($q)-1;
        if($q{0}=='/'){$q=substr($q,1);}
        //if($q{$last_index}=='/'){$q=substr($q,0,$last_index);}
        $splitFlag = preg_quote(SlightPHP::$splitFlag);  
        if(($i=strpos($q,'/'))>0)
        {
            $path_array[0]=substr($q,0,$i);
            if(($i2=strpos($q,'-'))>0)
            {
                 $path_array[1]=substr($q,$i+1,$i2-($i+1));
                 $q=substr($q,$i2+1);
                 $q=str_replace('--','- -',$q);   //$q='main/index-url-http://www.3156.test/main/index-id--name-baoge';
                 $pas = preg_split("/[$splitFlag]/",$q,-1,PREG_SPLIT_NO_EMPTY);  //path array append
            }
            else
            {
                $path_array[1]=substr($q,$i+1);
                $path_array[1]=str_replace('/','',$path_array[1]);  # main/index/
            }
            if($pas)
            {
                foreach ($pas as $v)
                {
                    array_push($path_array,trim($v));
                }
            }
        }
        else
        {
            $path_array[0]=$q;
        }
        
        return $path_array;
    }
}
?>
