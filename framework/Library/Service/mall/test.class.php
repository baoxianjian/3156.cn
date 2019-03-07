<?php
/**
 * 测试Serv
 */
class Service_mall_test extends Base_service{
	//判断参数是否为1
	function isOne($a){
		if ($a==1) {
			return true;
		}else{
			return false;
		}
	}
	
	//判断参数是否为2
	function isTwo($a){
		if ($a==2) {
			return true;
		}else{
			return false;
		}
	}
} 