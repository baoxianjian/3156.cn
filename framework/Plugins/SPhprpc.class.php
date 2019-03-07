<?php
class SPhprpc{
	private $_rpc;
	function __construct($scene=''){
		if ($scene == 'client'){
			if (!class_exists('PHPRPC_Client')){
				require 'phprpc/phprpc_client.php';
			}
			$this->_rpc = new PHPRPC_Client();
		} elseif ($scene == 'server'){
			if (!class_exists('PHPRPC_Server')){
				require 'phprpc/phprpc_server.php';
			}
			$this->_rpc = new PHPRPC_Server();
		}
	}
	
	function __call($name,$args){
		if ($this->_rpc){
			return call_user_func_array(array(&$this->_rpc,$name),$args);
		} else {
			return false;
		}
	}
}