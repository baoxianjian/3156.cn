<?PHP
/*
$beginning = array('kk'=>'foo');
$end = array(1 => 'bar',2=>'sdfasd');
$result = array_merge($beginning, $end);
print_r($result);
exit;

 */
ini_set('display_errors', true);
//$_GET['debug']=1;
if(isset($_GET['debug']))
{
    define('DEBUG',1); //只要定义了就会打印出sql     
}
//define('DEBUG',1); //只要定义了就会打印出sql     

error_reporting(E_ALL^E_WARNING^E_NOTICE);
require_once("./config.php");

                          
//
//$q='ads/edit-id-1-r-1';
/*                  
if($_GET['q'])
{
    $q=$_GET['q'];
}
else
{
    $q='ads/list';
}
     $q='search/sreach_list';     
     $_GET['seek']=urlencode('感冒');
 */   
if (false === ($res = SlightPHP::run($q))) {
    header('HTTP/1.1 404 Not Found');
    header('Status: 404 Not Found');
    include FRAME_DIR.'/Tpl/templates/404.html';
} else {
    echo $res;
}

/**
	End file,Don't add ?> after this.
*/
