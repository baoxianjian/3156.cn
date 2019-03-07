<?php
include('Xmlrpc.php');

class Test {

	var $xmlrpc;
	var $path="http://xmlrpc.powercdn.com/user"; //请求路径;
	var $port="80"; //请求端口
	var $username="69302011";//验证用户名
	var $password="000000";//验证密码
	var $method="cache.refreshList"; //批量请求方法
	var $methodonly="cache.refresh"; //单个网址请求方法
	/*
	 * 批量刷新网址
	 */
	function rpcRefreshRpc($datas)	{

		$this->xmlrpc= new CI_Xmlrpc(array());
		$this->xmlrpc->server($this->path, $this->port);
		$this->xmlrpc->client->auth($this->username,md5($this->password));

		if(is_array($datas)){			
			$this->xmlrpc->method($this->method); //'account.accountLogin'
		}else{			
			$this->xmlrpc->method($this->methodonly); //'account.accountLogin'
		}

		$this->xmlrpc->request(array($datas));

		if ( ! $this->xmlrpc->send_request())
		{
		   echo $this->xmlrpc->display_error();
		   return "false";
		} else {
		   return $this->xmlrpc->display_response();
		}
	} 
	/*
	 * 单条刷新网址 for example www.3158.cn
	 */
	public static function  rpcRefresh($url){
		echo $this->rpcRefreshRpc($url);
	}
	/*
	 * 批量刷新网址 for example 　array("www.3158.cn","3156.test")
	 */
	public static function  rpcRefreshList($urls){
		echo $this->rpcRefreshRpc(array($urls,"array"));
	}

	function index(){
		$this->rpcRefreshList(array("3.com","3.3.com"));		
	}
}
?>