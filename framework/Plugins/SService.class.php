<?php
/**
 * Service 入口
 * @author tong
 * @date 2014/7/15
 *
 */
class SService{
	private $_class;
	private $_className;
	private static $_entrance = false;//定义入口标记，此标识，只可在本类中修改
	public function __construct($className='default'){
		self::$_entrance = true;//开启入口
		$this->_className = $className;
		$this->_class = new $className;
		self::$_entrance = false;//关闭入口
	}
	public function __call($name,$args){
		//aopbefore
		Lib_aop::start($this->_className, $name, $args);//某个类中某个方法执行前再执行此;
		$return = call_user_func_array(array($this->_class,$name),$args);
		//aopafter
		Lib_aop::end($this->_className, $name, $args, $return);//某个类中某个方法执行后再执行此
		return $return;
	}
	/**
	 * 得到入口标识
	 */
	public static function getEntrance(){
		return self::$_entrance;
	}
}