<?php
/**
 * AOP 对执行Service中的函数，执行前与执行后进行函数通知等操作的用户
 * @author TONG
 * @date 2014/07/15
 */
class Lib_aop{
	public static function start($className,$methodName,$arg){
		switch ($className){
			//此为测试
			case 'Service_mall_test':
				if ($methodName=='isOne') {
					var_dump($arg);
				}
				break;
			default:
				break;
		}
	}
	public static function end($className,$methodName,$arg,$return){
		switch ($className){
			//此为测试
			case 'Service_mall_test':
				if ($methodName=='isOne') {
					var_dump($arg);
				}
				break;
			default:
				break;
		}
	}
}