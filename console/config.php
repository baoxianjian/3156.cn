<?PHP
//一些主要目录的配置
define('FRAME_DIR', "../framework");
define('PLUGINS_DIR',FRAME_DIR."/Plugins");
define('LIB_DIR',FRAME_DIR."/Library");
define('TOOLS_DIR',FRAME_DIR."/Tools");//外来API包目录使用此常量来加载
define('COMTPL_DIR',FRAME_DIR."/Tpl");//公用模板目录
define('FTPTPL_DIR',"../uploads/ftptpl");//公用FTP上传目录
define('ZSTPl_DIR',"../www/zhaoshang");//公用招商页面目录
define('HEADER_URL',"/zhaoshang/inc/header.shtml");//公用招商页面头部
define('FOOTER_URL',"/zhaoshang/inc/footer.shtml");//公用招商页面底部
define('CONTACT_URL',"http://www.3156.test/zhaoshang/index");//公用招商联系方式JS
define('GUESTBOOK_JS',"http://img1.3156.test/res/js/dev/assets/common/guestbook.js?v=20150414");//公用留言JS
define("CONFIG_DIR","../config");
define('NOW',time());
define('UPLOADS_DIR','../search/assets/uploads');

define('ASSETS_URL',"/assets");
define('TEMPLATE_MAIN_URL',"/app/templates");
define('SEARCH_URL','http://192.168.13.123:8080/solr');
define('UPLOADS_URL','http://localhost');




require_once(FRAME_DIR."/SlightPHP.php");
require_once(LIB_DIR."/Service/com/blocks.func.php");  //add by baoxianjian 12:01 2015/4/20  区块调用函数引入  
SlightPHP::setDebug(false);
SlightPHP::setAppDir("app");
SlightPHP::setDefaultPage("main");
SlightPHP::setDefaultEntry("index");
SlightPHP::setSplitFlag("-.");
//类的加载
function __autoload($class){
	//加载Library中的类
	if('Base'==substr($class,0,4) ||'Model'==substr($class,0,5) || 'Service'==substr($class,0,7) || 'Lib'==substr($class,0,3)){
		$file = LIB_DIR ."/". str_replace("_","/",$class) . ".class.php";
	}elseif("Test"==substr($class,0,4)){//加载测试类
		$file = FRAME_DIR."/". str_replace("_","/",$class) . ".class.php";
	}elseif($class{0}=="S"){//加载Plugins中的类
		$file = PLUGINS_DIR."/$class.class.php";
	}else{
		$file = SlightPHP::getAppDir()."/".str_replace("_","/",$class).".class.php";
	}
	if(file_exists($file)) return require_once($file);
}
spl_autoload_register('__autoload');
//加载配置信息
$consts = SConfig::getConfig(CONFIG_DIR. "/const.ini");
foreach ($consts as $k=>$v){
	if (!defined($k)){
		define($k, $v);
	}
}
SRoute::setConfigFile(CONFIG_DIR."/route.ini",'www');
//{{{
SDb::setConfigFile(CONFIG_DIR. "/db.ini");
SMongodb::setConfigFile(CONFIG_DIR.'/mongo.ini');
SCache::setConfigFile(CONFIG_DIR."/cache.ini");
SMail::setConfigFile(CONFIG_DIR.'/email.ini');
SRedis::setConfigFile(CONFIG_DIR.'/redis.ini');
SSolr::setConfigFile(CONFIG_DIR.'/search.ini');
//SGearman::setConfigFile(CONFIG_DIR.'/gearman.ini');
//}}}

/**
	End file,Don't add ?> after this.
*/
