<?php
abstract class SAbstract {
	
	protected $_time;
	protected $_ymd;
	protected $_error;
	
	function __construct(){
		$this->_time = time();
		$this->_ymd = date('Y-m-d',$this->_time);
	}
	
	/**
	 * 写入错误信息
	 * @param int $code
	 * @param string $msg
	 */
	protected function setError($msg="",$code=0){
		$this->_error["code"] = $code;
		$this->_error["msg"] = $msg;
	}
	/**
	 * 获取错误信息
	 * @param string $type
	 */
	public function getError($type="msg"){
		return $this->_error[$type];
	}
}