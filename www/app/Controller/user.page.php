<?php
/**
* @name: 用户控制器 
* @author: baoxianjian
* @date: 16:36 2015/3/29
*/        
define('SYS_NAME','user');
header('Content-type:text/html;charset=utf-8');
class Controller_user extends Controller_basepage {
    
     //用户中心首页
    function pageIndex($inPath){
        $mdl_user=new Model_user_user(); 
        
        $ru=$this->getUserFromSession();
                
        $row=$mdl_user->getRowById($ru['id']);
        
        $row['reg_time']=SUtil::formatTime($row['reg_time']);
        $params['info']=$row;
        return $this->template($params);
    }
    
    //用户登录页面
    function pageLogin($inPath){
        
        return $this->template();
    }
    
    //用户登录处理
    function pageDoLogin()
    {
        
        $row=$this->request['row'];
        $this->buildTplFilePath('user.login'); 
        $mdl_user=new Model_user_user();  
        
        $row=$this->request['row'];
        
        #一系列的检查
        if($row['user_name']=='')
        {
            return $this->showRegisterMessage($row,'用户名不能为空');
        }
        if($row['user_pwd']=='')
        {
            return $this->showRegisterMessage($row,'密码不能为空');
        }
        
        $row_user=$mdl_user->getRowByUserName($row['user_name']);
        $uid=$row_user['user_id'];
        if($row_user['user_pwd']==md5($row['user_pwd']))
        {
            $row_login['session_id']=$this->buildSessionId();
            $row_login['session_agent']=md5($_SERVER['HTTP_USER_AGENT']);
            $row_login['login_ip']=SRemote::getRealIp();
            
            
            $up=$mdl_user->updateRowById($row_login,$uid);
            if($up)
            {
                $this->checkSessionStart($row_login['session_id']);
                $_SESSION['uid']=$row_user['user_id'];
                $_SESSION['aid']=$row_login['session_agent'];
                $_SESSION['lip']=$row_login['login_ip'];
                $_SESSION['uname']=$row_user['user_name'];
                                
                $user_mods=explode(',',$row_user['user_mods']);

                if(in_array('2',$user_mods))  //企业用户额外增加session
                {
                    $mdl_cmp=new Model_cmp_company();
                    $row_cmp=$mdl_cmp->getRowByUserId($uid);
                    
                    $_SESSION['cid']=$row_cmp['cmp_id'];
                    
                    //vip判断
                    if(intval($row_cmp['cmp_type']==6) && SUtil::nowInTimeRange($row_cmp['start_time'],$row_cmp['end_time']))
                    {
                        $cvip=1;                            
                    }
                    $_SESSION['cvip']= intval($cvip);
                    
                   
                   return $this->showRegisterMessage($row,'登录成功',1,'/company');   
                }

                return  $this->showRegisterMessage($row,'登录成功',1,'/agent');   
            }
            return $this->showRegisterMessage($row,'更新登录信息失败');    
        }
        
        return $this->showRegisterMessage($row,'用户名或密码错误');
    }
    
    //用户注册
    function pageRegister()
    {
        return $this->template();
    }
    
    //用户注册处理
    function pageDoRegister()
    {        
        $row=$this->request['row'];
        $this->buildTplFilePath('user.register'); 
        $mdl_user=new Model_user_user();  
        
        #一系列的检查
        if($row['user_name']=='')
        {
            return $this->showRegisterMessage($row,'用户名不能为空');
        }
        $row_temp=$mdl_user->getRowByUserName($row['user_name']);
        
        if($row['user_name']==$row_temp['user_name'])
        {
             $uname=$row['user_name'];
             $row['user_name']='';
             return $this->showRegisterMessage($row,"用户名:{$uname}已存在,请使用其他用户名."); 
        }
        
        if($row['email']=='')
        {
            return $this->showRegisterMessage($row,'邮箱不能为空');
        }
        if(!SUtil::Is_email($row['email']))
        {
            $row['email']='';
            return $this->showRegisterMessage($row,'邮箱格式不正确');
        }
        
        
        $len=strlen($row['user_pwd']);
        if($len<6 || $len >20)
        {
            $row['user_pwd']='';
            return $this->showRegisterMessage($row,'密码长度应在6到20个字符之间');
        }
        if($row['user_pwd']!=$row['user_pwd_temp'])
        {
           $row['user_pwd_temp']='';
           return $this->showRegisterMessage($row,'两次密码输入不一致'); 
        }
        //unset($row['user_pwd_temp']);
                    

        $row_temp=$row;
        unset($row_temp['user_pwd_temp']);
        $row_temp['user_pwd']=md5($row_temp['user_pwd']);
        $row_temp['reg_time']=NOW;
        $uid=$mdl_user->addRow($row_temp);
        
        if($uid)
        {
            return $this->showRegisterMessage($row,'注册成功,您可以进行登录了。',1,'/user/login');  
        }

        return $this->showRegisterMessage($row,'注册失败',3);
    }
    
    //退出
    function pageLogout()
    {
        $this->checkSession();
        session_destroy();
        $this->showMessage('退出登录成功!',1,'/user/login');
        $this->buildTplFilePath('user.login');
        return $this->template();
    }
    
    /**
     * 显示账户信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    
    function pageInfo()
    {
        return $this->template();
    }
    
    /**
     * 显示账户信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    
    function pageInfor1()
    {
        $ru=$this->getUserFromSession(1);
        $uname=$ru['name'];   
        $mdl_user=new Model_user_user();
        $row=$mdl_user->getRowByUserName($uname);
         
        if($this->request['ac']=='save')
        { 
            $uname=$ru['name'];
            $mdl_user=new Model_user_user();
            $res=$mdl_user->getRowByUserName($uname);
            $id=$res['user_id'];            
            $row=$this->request['row'];                     
            $res=$mdl_user->updateRowById($row,$id);
            if($res)
            {
                SUtil::success("修改成功！",$jumpUrl='/user/infor2');
               // $this->showMessage('修改成功！',3,$tourl='/user/infor2');
            }
            else
            {
                SUtil::error("修改失败!");
            }
        
        }else{
            
            $this->preparRow($row);
            $this->ass('sex',$row);
            $this->ass("agency", $row);            
        }
               
        return $this->template();
    }
    
    /**
     * 显示账户信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    function pageInfor2()
    {   $ru=$this->getUserFromSession(1);
        $uname=$ru['name'];
        $mdl_user=new Model_user_user();
        $row=$mdl_user->getRowByUserName($uname);
                 
        $this->ass("row", $row);                     
        return $this->template();
    }
    
    //默认选中设置
    private function preparRow($row)
    {
        if($row)
        {
            //选中设置
            $sex_checked=array($row['sex']=>"checked='checked' ");
            $agency_checked=array($row['agency']=>"checked='checked' ");
                      
            $this->ass('sex_checked',$sex_checked);
            $this->ass('agency_checked',$agency_checked); 
                                 
            $this->ass('row',$row);
        }
    }
    
    /**
     * 修改密码(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    function pageModify()
    {    
    	// $ru=$this->getUserFromSession(1);
        $ru=$this->getCompanyFromSession(0);
        $user_id=$ru['uid'];   
        $mdl_user=new Model_user_user();         
        $row_old=$mdl_user->getRowById($user_id);     
        if($this->request['ac']=='save')
        {
            $user_id=$ru['uid'];
            $row=$this->request['row'];          
            //$this->buildTplFilePath('user.modify'); 
            $mdl_user=new Model_user_user();           
            $row_old=$mdl_user->getRowById($user_id);   
            #一系列的检查
            if($row['user_pwd']=='')
            {
                //return $this->showRegisterMessage($row,'旧密码不能为空');
                SUtil::error("旧密码不能为空！");
            }          
            //比较老密码是否正确
            if($row_old['user_pwd']!=md5($row['user_pwd']))
            {
                $row['user_pwd']='';
                //return $this->showRegisterMessage($row,'旧密码不正确');
                SUtil::error("修改失败,旧密码不正确！");
            }   
        
            $len=strlen($row['new_pwd']);
            if($len<6 || $len >20)
            {
                $row['new_pwd']='';
                //return $this->showRegisterMessage($row,'密码长度应在6到20个字符之间');
                SUtil::error("修改失败,密码长度应在6到20个字符之间！");
            }
            if($row['confrim']!=$row['new_pwd'])
            {
                $row['comfrim']='';
                //return $this->showRegisterMessage($row,'两次密码输入不一致');
                SUtil::error("修改失败,两次密码输入不一致！");
            }
      
            //unset($row['user_pwd_temp']);
            $row_temp['user_pwd']=md5($row['new_pwd']);
            //$row_temp['reg_time']=NOW;
            $result=$mdl_user->updateRowById($row_temp,$user_id);
            
          
            
            if($result)
            {
                SUtil::success("修改成功！");
            }
            else
            {
                SUtil::error("修改失败！");
            }
          
        }
        else
        {
            #初始化
        }                                                              
        return $this->template();
    }
   
    
    /**
    * 显示信息(注册时用的)
    * @param array $row
    * @param string $msg
    * @param int $type
    * @param string $tourl
    * @return mixed
    */
    private function showRegisterMessage($row,$msg,$type=0,$tourl)
    {
        $this->showMessage($msg,$type,$tourl,3000);
        $params['row']=$row;
        
        return $this->template($params);
    }
    
    /**
     * 忘记密码视图
     */
    public function pageforget(){
    	
    	return $this->template();
    	
    }
    
    /**
     * 忘记密码处理方法1
     */
    public function pagedoForget1(){
    	
    	SUtil::isAjax() || die('error');
    	$this->getCmpId();
    	$questArr = SUtil::html_arr($this->request);
    	
    	//用户model
    	$mdl_user=new Model_user_user();
    	
    	//初始化状态值
    	$msg_data['status'] = 1;
    	$user_result = $mdl_user->getRowByEmail($questArr['email'], $questArr['user_name']);
    //	die(SUtil::P($user_result));
	    if ( $user_result == NULL ){
	    			
    		$msg_data['status'] = 0;
   			$msg_data['info'] = '邮箱与用户名不匹配';
	    			
    	}elseif ( $_SESSION['user_code'] != strtolower($questArr['code'])  ){
	   		 
	   		$msg_data['status'] = 0;
	   		$msg_data['info'] = '验证码错误';
	   		 
    	}
	    	
	    	//发送邮箱
	    if ( $msg_data['status'] == 1 ){
	   		
	   		//邮箱扩展
	   		$mail = new SMail();
	   		 
    		//读取配置文件
	    	$file = file(CONFIG_DIR.'/email.ini');
	    		 
	    	//die(var_dump($file));
	    		 
	   		//设置使用文件配置
	   		$mail->SMail();
	    		 
	   		//设置配置文件
	   		Smail::setConfigFile($file);
	    		 
    		//生成随机因子
	    	$charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
	    		
	    	for ( $i=0; $i<8; $i++ ){
	    		
	   			$code .= $charset[rand(0, strlen($charset)-1)];
	    		
	    		
	   		} 
	    		
	   		//地址 标题 内容 username
    		if ( $mail->sendMail($questArr['email'],'邮箱验证码',$code, '3156.cn') > 0 ){
	    		
    			$_SESSION['email_code'] = $code;
    			$_SESSION['user_code_id'] = $user_result['user_id'];
    		//	die(SUtil::P($_SESSION['user_code_id']));
	    		$msg_data['info'] = '邮箱发送成功，请耐心等待';
	    		//unset($_SESSION['user_code']);
	    			
	    	}else{
	    		
	    		$msg_data['status']	= 0;
	    		$msg_data['info'] = '邮箱发送失败';	
	    		
	    	}
	    		 		//	echo $mail->getErrMsg();
	    		
	    }
	    
	    die(json_encode($msg_data));
	    	
    	
    }
    
    
    /**
     * 忘记密码处理方法1
     */
    public function pagedoForget2(){
    	 
    	SUtil::isAjax() || die('error');
    	$this->getCmpId();
    	$questArr = SUtil::html_arr($this->request);
    	 
    	if ( $_SESSION['email_code'] == $questArr['email_code'] && !empty($_SESSION['email_code']) ){
    		
    		$msg_data['status'] = 1;
    		$msg_data['info'] = '邮箱验正确';
    		$_SESSION['check'] = 1;
    		
    	}else{
    	
    		$msg_data['status'] = 0;
    		$msg_data['info'] = '邮箱验错误';
    		
    	}
    	die(json_encode($msg_data));
    }
    
    /**
     * 忘记密码处理3
     */
    public function pagedoForget3(){
    	
    	SUtil::isAjax() || die('error');
    	$this->getCmpId();
    	$questArr = SUtil::html_arr($this->request);
    	
    	( empty($_SESSION['user_code_id']) || $_SESSION['check'] != 1 ) && die(json_encode(array('status'=>0,'info'=>'操作失效')));
    	
    	$questArr['user_pwd'] != $questArr['user_pwd_2'] && die(json_encode(array('status'=>0,'info'=>'两次密码不一致')));
    	
    	//用户model
    	$mdl_user=new Model_user_user();
    	//die(SUtil::P($_SESSION['user_code_id']));
    	if ( $mdl_user->updateRowById(array('user_pwd'=>md5($questArr['user_pwd'])),$_SESSION['user_code_id']) !== false ){
    		
    		//写在session
    		unset($_SESSION['user_code_id']);
    		unset($_SESSION['email_code']);
    		unset($_SESSION['check']);
    		
    		die(json_encode(array('status'=>1,'info'=>'密码修改成功')));
    		
    	}else{
    		//die($_SESSION['user_id']);
    		die(json_encode(array('status'=>0,'info'=>'密码修改失败')));
    	}
    	
    	
    	
    }
    
    /**
     * 验证码生成方法
     * @return string
     */
    public function pageCreateCode(){
    	
    	$code = new SCode();
    	$code->config('185','34','4','user_code');
    	return $code->create();
    }
    
  	/**
  	 * 表单校验
  	 */
    public function pageInputCheck(){
    	
    	SUtil::isAjax() || die('error');
    	$this->getCmpId();
    	$questArr = SUtil::html_arr($this->request);
    	
    	//用户model
    	$mdl_user=new Model_user_user();
    	
    	//用户名
    	if ( isset($questArr['user_name']) && !isset($questArr['email']) ){
    		
    		if ( $mdl_user->getRowByUserName($questArr['user_name']) == NULL ){
    			
    			$msg_data['status'] = 0;
    			$msg_data['info'] = '该用户名不存在';
    			
    		}else{
    			
    			$msg_data['status'] = 1;
    			$msg_data['info'] = '用户名可用';
    			
    		}
    		
    	}elseif( isset($questArr['email']) ){//邮箱
    		
    		if ( $mdl_user->getRowByEmail($questArr['email'], $questArr['user_name']) == NULL ){
    			
    			$msg_data['status'] = 0;
    			$msg_data['info'] = '邮箱输入错误';
    			
    		}else{
    			
    			$msg_data['status'] = 1;
    			$msg_data['info'] = '邮箱可用';
    			
    		}
    		
    		
    	}else{
    		
    		
    		if ( $_SESSION['user_code'] != strtolower($questArr['code'])  ){
    			
    			$msg_data['status'] = 0;
    			$msg_data['info'] = '验证码错误';
    			
    		}else{
    			
    			$msg_data['status'] = 1;
    			$msg_data['info'] = '验证码可用';
    			
    		}
    		
    		
    	}
    
    	die(json_encode($msg_data));
    	
    }

        
}