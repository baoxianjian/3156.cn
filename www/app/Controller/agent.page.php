<?php
/**
* @name: 用户控制器 
* @author: baoxianjian
* @date: 16:36 2015/3/29
*/        
define('SYS_NAME','agent');
class Controller_agent extends Controller_basepage {
    
     //代理用户中心首页
    function pageIndex($inPath){
    	$ru=$this->getUserFromSession(1);
        $uname=$ru['name'];
        $mdl_user=new Model_user_user();
        $row=$mdl_user->getRowByUserName($uname);
    	 
        $this->ass("row", $row);
        
        
       
        
       return $this->template();
    }
    
	
        
}
