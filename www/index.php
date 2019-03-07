<?PHP



/*
$beginning = array('kk'=>'foo');
$end = array(1 => 'bar',2=>'sdfasd');
$result = array_merge($beginning, $end);
print_r($result);
exit;
         
 */

ini_set('display_errors', true);
//define('DEBUG',1); //只要定义了就会打印出sql
error_reporting(E_ALL^E_WARNING^E_NOTICE);
require_once("./config.php");
//通过参数来开启调试功能，上线后此调试禁用,主要用于显示SQL语句
//$_REQUEST['debug']=1;
if($_REQUEST['debug']){ 
	define('DEBUG',1);
}

              
//
//$q='ads/edit-id-1-r-1';
/*                   
if($_GET['q'])
{
    $q=$_GET['q'];
}
else
{
    $q='comments/list';
}
                           
    //$q='main/index-url-http://www.3156.cn-id-1';   
   
    $q='product/index';    
    $q='product/';    
    $q='product'; 
    $q='main/index/';    
    $q='main/index-url-http://www.3156.cn/main/index-id--name-baoge';

     
    */
    
    $q="main/index?id=10=";
        
if (false === ($res = SlightPHP::run())) {
    if(is_int(strpos($_SERVER['REQUEST_URI'],'zhaoshang')))
     {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: http://www.3156.cn/');
        exit;
     }
    
    header('HTTP/1.1 404 Not Found');
    header('Status: 404 Not Found');
    include FRAME_DIR.'/Tpl/templates/404.html';
} else {
    echo $res;
    //file_put_contents('index.shtml',$res);
}

/**
	End file,Don't add ?> after this.
*/