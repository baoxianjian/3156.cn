<?php
/**
 * 此项目controller基类，可对Base_controller中的方法进行重写覆盖
 **/
define('PAGE_VAR_NAME','pagehtml');
define('LIST_VAR_NAME','list');
define('SYS_ADM_LEVEL_MAX',10000);


class Controller_basepage extends Base_controller {
    public $saId;//用户名
    public $saName;//用户ID
    
    protected $tplName='tpl1';
    protected $styleName='style1';
    protected $tplFilePath;

    private $_mdl_adm;
    //private $_mdlAdm;

    
    public function __construct($in_array)
    {
        $ip=SRemote::getRealIp();
        
	/*
        $allowed_ips = array('113.204.101.198','124.205.220.104','124.205.220.105','124.205.220.106','124.205.220.107');
        if(!in_array($ip,$allowed_ips))
        {
            exit('error code:444');
        }
	*/
        
        
		parent::__construct($in_array);
		//以下目录不填写表示使用默认
		$this->app_picdir = 'www';//图片上传目录名

		$this->app_tplpagedir = '';//分页函数模板目录
		$this->app_tplmsgdir = '';//提示消息模板目录

        $this->buildTplFilePath();
        

        $this->ass('STYLE_URL',ASSETS_URL.'/'.$this->styleName);
        $this->ass('TPL_URL',$this->tplName);
        $this->ass('HEADER',$this->tplName.'/common/header.html');
        $this->ass('HEADER_END',$this->tplName.'/common/header_end.html'); 
        $this->ass('FOOTER',$this->tplName.'/common/footer.html');
        $this->ass('SEARCH_URL',SEARCH_URL);
        //$this->ass('common',TEMPLATE_MAIN_URL.'/'.'common');
       // $this->ass('EDIT_URL',$this->)
	   
	   
        
        $this->ass('WWW_URL', WWW_URL); 
        $this->ass('JS_URL', IMG1_URL.'/res/js');
        $this->ass('CSS_URL', IMG1_URL.'/res/css');
        $this->ass('IMG_URL', IMG1_URL.'/res/img');
        $this->ass('ZIXUN_URL', NEWS_URL);
        $this->ass('BZ_URL', NEWS_URL);
      
      
                                                         
        //登录检查在基类进行 update by baoxianjian
        //过滤 无需登录检测的页面和动作
        $filters=array(
        'console'=>array('login','dologin','logout'),
        'uploads'=>array('upload')
        );
        
        $check_login=true;
        foreach ($filters as $fk=>$fv){
            if($fk==CUR_PAGE){
                 if(in_array(CUR_ACTION,$fv)){
                    $check_login=false;
                    break;    
                 }
            }                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
        }


        
        if($check_login)
        {
            $sa=$this->getAdminFromSession(0); 
            
            $this->saId=$sa['said'];
            $this->saName=$sa['saname'];
            
            $this->checkPermission(CUR_PAGE,CUR_ACTION);
        }
        
	}
    
    private function _getMdlADM()
    {
        if(!$this->_mdl_adm)
        {
            $this->_mdl_adm=new Model_sys_admins();
        }
        return $this->_mdl_adm;
    }
    
    /**
    * 得到相应模版路径 
    * add by baoxianjian 20:35 2015/3/21
    * @param mixed $filename  文件名(无需加.html)
    */
    protected function buildTplFilePath($filename='',$type=1) {
        
        if($type==2 || CUR_ACTION=='del' || CUR_ACTION=='delAll')
        {
            $this->tplFilePath=$this->tplName.'/common/show_message.html';    
            return $this->tplFilePath;
        }
        
        $dir=$this->tplName.'/'.CUR_PAGE;
        if($filename==''){
            $filename=CUR_PAGE.'_'.CUR_ACTION;
        }
        $this->tplFilePath=$dir.'/'.$filename.'.html';
        return $this->tplFilePath;
    
        
    }
    
    /**
    * 调用模版 
    * add by baoxianjian
    * @param array $params 要传入的数组
    * @param string $tpl 默认为$this->tplFilePath; 
    * @return mixed
    */
    protected function template($params=array(),$tpl='')
    {
        if(!$tpl){
            $tpl=$this->tplFilePath;
        }

        return $this->render($tpl,$params);
    }

    /**
    * 显示消息
    * 
    * @param mixed $msg
    * @param mixed $type  0:消息  1:成功 3:错误
    * @param mixed $tourl   取_reload值时为刷新页面
    * @param mixed $timeout
    */
    public function showMessage($msg,$type=1,$tourl='',$timeout=2000)
    { 
        if($this->request['ajax'])
        {
            $data=array('status'=>$type,'info'=>$msg,'tourl'=>$tourl,'timeout'=>$timeout);
            echo json_encode($data);
            exit;
        }
        else              
        {
            if($tourl=='_reload')
            {$tostr="window.location.reload();";}
            else if($tourl=='_back')
            {$tostr="history.go(-1);";}
            else if($tourl=='_login')
            {$tostr="window.top.location='/console/login';";}
            else if($tourl!='')
            {$tostr="window.location='{$tourl}';";}
            $str=
    "<script type=\"text/javascript\">
        var temp=layer.alert('{$msg}', {$type}, !1);
        setTimeout(\"layer.close(temp);{$tostr}\", {$timeout});
    </script>";
            $this->ass('SYS_MESSAGE',$str);
        }
    }
    
    /**
    * 建立session id   
    * @param mixed $tid
    * @return string
    */
    protected function buildSessionId()
    {
        $mt=microtime();
        $mark=substr($mt,2,5);
        $mark.=substr($mt,5);
        $mark=str_replace(' ','',$mark);
        return md5($mark);
    }
    
    
    protected function checkSessionStart($sid=0)
    {
      //  print_r($_COOKIE);
      //   echo var_dump($sid),'<br/>';
        if($sid)
        {
            session_id($sid);
          //  echo session_id();
        }
        session_start();
      //  print_r($_SESSION);
        //return intval($_SESSION['user_id']);
    }
    
    //检查session
    protected function checkSession($sid='')
    {     
        //PHPSESSID 
        $this->checkSessionStart(); //先用默认的PHPSESSID进行启动
        if(!$said=intval($_SESSION['said']))//无法启动
        { 
            $sid=$_COOKIE['sid'];
            if(!$sid){return false;} //cookie没得值 已经过期
             
            $this->_getMdlADM(); 
            $rs=$this->_mdl_adm->getSessionBySid($sid); //从数据库中得到 row_session
            
            if(!$rs){return false;}   //数据库中没有此条session
            
            $this->checkSessionStart(); //从数据库中恢复会话

            $_SESSION['said']=$rs['sa_id'];
            $_SESSION['aid']=$rs['session_agent'];
            $_SESSION['lip']=$rs['login_ip'];
            $_SESSION['saname']=$rs['sa_name'];
                                    
            return true;
        }
        if(!$said=intval($_SESSION['said'])){return false;}
        if($_SESSION['aid']!=md5($_SERVER['HTTP_USER_AGENT'])){return false;}
        if($_SESSION['lip']!=SRemote::getRealIp()){return false;}
        return $said;  
    }
    
    //重session中得到用户
    protected function getAdminFromSession($exit=false)
    {
        $said=$this->checkSession();
        if(!$said && $exit)
        {
            $this->tplFilePath=$this->tplName.'/common/show_message.html';
            $this->showMessage('登录状态过期，请重新登录',3,'/console/login',3000);
            echo $this->template(); 
            exit;
        }
        return array('said'=>$said,'saname'=>$_SESSION['saname']);
    }
    
    
    //得到用户id
    protected function getAdminId($exit=false)
    {
        $ru=$this->getAdminFromSession($exit);
        return $ru['said'];
    }
   
    /**
    * 权限检查函数
    *  
    * @param string $page 页面
    * @param string $ac  动作
    */
    public function checkPermission($page,$ac)
    {
        $page=strtolower($page);
        $ac=strtolower($ac);
       // $this->_mdlAdm->setCache(1);
        $this->_getMdlADM();   
        $row=$this->_mdl_adm->getPermissionsAndLevelById($this->saId);
        $p=$row['sa_permissions'];
        $lev=$row['sa_level'];
        
        $p=json_decode($p,true);
    
        if(!$lev || ($lev!=SYS_ADM_LEVEL_MAX && !in_array($ac,$p[$page])))
        {
            $this->buildTplFilePath('',2);
            /*
            if(CUR_PAGE=='main' && CUR_ACTION=='index')
            {
                $tourl="/console/login";
            }
            else
            {
                $tourl="_back";
            }
            */
            $this->showMessage('你没有权限访问该页面',3,'_login',3*1000);
            echo $this->template();
            exit;
        }
    }

}

/**
	End file,Don't add ?> after this.
*/
