<?php 
	
	/**
	 * 控制台管理控制器
	 * @author Administrator
	 *
	 */
	Class Controller_console extends Controller_basepage {
        //用户登录页面
        function pageLogin($inPath){
            
            return $this->template();
        }
        
        //用户登录处理
        function pageDoLogin()
        {
            $row=$this->request['row'];
            $this->buildTplFilePath('console_login'); 
            $mdl_adm=new Model_sys_admins();  

            #一系列的检查
            if($row['sa_name']=='')
            {
                return $this->showRegisterMessage($row,'用户名不能为空',3);
            }
            if($row['sa_pwd']=='')
            {
                return $this->showRegisterMessage($row,'密码不能为空',3);
            }
            
            $row_admin=$mdl_adm->getRowByAdminName($row['sa_name']);
            $sa_id=$row_admin['sa_id'];
            if($row_admin['sa_pwd']==md5($row['sa_pwd']))
            {
                $row_login['session_id']=$this->buildSessionId();
                $row_login['session_agent']=md5($_SERVER['HTTP_USER_AGENT']);
                $row_login['login_ip']=SRemote::getRealIp();
                
                
                $up=$mdl_adm->updateRowById($row_login,$sa_id);
                if($up)
                {   
                    //sso_login进行跨域登录,并使用P3P模式存储COOKIE
                    header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
                    setcookie("sid",$row_login['session_id'],time()+30*24*3600,"/"); //30天


                    //$this->checkSessionStart($row_login['session_id']);
                    $this->checkSessionStart();
                    
                    
                    $_SESSION['said']=$row_admin['sa_id'];
                    $_SESSION['aid']=$row_login['session_agent'];
                    $_SESSION['lip']=$row_login['login_ip'];
                    $_SESSION['saname']=$row_admin['sa_name'];
                                
                    return  $this->showRegisterMessage($row,'登录成功',1,'/main');   
                }
                return $this->showRegisterMessage($row,'更新登录信息失败');    
            }
            
            return $this->showRegisterMessage($row,'用户名或密码错误');
        }
        
        //退出
        function pageLogout()
        {
            $this->checkSession();
            session_destroy();
            setcookie("PHPSESSID",'',time()-1,"/"); 
            setcookie("sid",'',time()-1,"/");
            $this->showMessage('退出登录成功!',1,'/console/login');
            $this->buildTplFilePath('console_login');
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

	}

?>