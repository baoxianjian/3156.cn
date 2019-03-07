<?php
/**
 * 根据码表的值，获取名称
 * yill
 */

$serives = array();

/**
 * 格式 getname articlestatus="xxx"  获取文章状态名称
 */
function smarty_function_getname($params){
	global $serives;
	if(empty($params)) return '';
	$result = '';
	foreach($params as $key=>$value){
		$obj = null;
		switch ($key){
			case 'articlestatus':
				if(!isset($serives[$key])){
					$obj =  new Model_mn_status();
					$serives[$key] = $obj;
				}
				else{
					$obj = $serives[$key];
				}
				$result = $obj->getValue($value);
				break; 	
			default:
				break;
		}
	}
	
	return is_null($result) ? '' : $result;
	
}

?>
