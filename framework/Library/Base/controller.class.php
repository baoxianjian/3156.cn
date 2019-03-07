<?php
//controller基类，
//所有公用信息全部在这里，如果有的项目需要不同，那么在项目继承类basepage.class.php中重写函数即可
define('STATE_DEL_YES',1);
define('STATE_DEL_NO',0);
define('STATE_AUDIT_NONE',1);
define('STATE_AUDIT_YES',2);
define('STATE_AUDIT_NO',3);

abstract class Base_controller extends STpl {
	//下面参数可在继承基controller里进行符值，然后把这里个目录重新定义
	protected $app_picdir = '';//图片上传目录名
	protected $app_tplpagedir = '';//分页函数模板目录
	protected $app_tplmsgdir = '';//提示消息模板目录
	protected $app_showpagenum = 10;//分页函数显示的页面码数
	protected $_time;
	protected $_path;
    protected $request; //add by baoxianjian 20:25 2015/3/8
    

	
    
	//初始货函数
	protected function __construct($path='') {

		$this->_time = time();//初始化当前时间戳
		$this->_path = $path;
        $this->request=$this->getUrlParams($path);
        
        //静态化定义
        define('WEB_STATIC',intval($this->request['static']));
           
        //导航
        $mdl_nav = new Model_com_navigations();
        if(WEB_STATIC==1 || WEB_STATIC_ALL==1)
        {
            $nav=$mdl_nav->getListStatic();
        }
        else
        {
            $nav=$mdl_nav->getListDynamic();    
        }
        $this->ass('NAV',$nav);
        
        
        //热搜词
        $hot_keys=array('感冒','止咳','消炎','壮阳','皮肤','妇科','补肾','止痛','止血','儿科');
        
        foreach ($hot_keys as $v) {
           $hot_keys_temp[]=array('kw_name'=>$v,'kw_name_encoded'=>urlencode(str_replace(' ','',$v)));
        }
        $this->ass('hot_keys',$hot_keys_temp);
        
        
        $this->ass('ROOT_DOMAIN', ROOT_DOMAIN); 
        $this->ass('WWW_URL', WWW_URL); 
        $this->ass('JS_URL', IMG1_URL.'/res/js');
        $this->ass('CSS_URL', IMG1_URL.'/res/css');
        $this->ass('IMG_URL', IMG1_URL.'/res/img');
        $this->ass('ZIXUN_URL', NEWS_URL);
        $this->ass('BZ_URL', BZ_URL);
        $this->ass('USER_URL', USER_URL);

	}


   
	//获取$inPath的参数与传值
	public function getUrlParams($inPath) {
		$count = count($inPath);
		$newary = array();
		for ($i = 2; $i < $count; $i++) {
			if ($i % 2 == 0) {
				$newary[$inPath[$i]] = @$inPath[$i + 1];
			}
		}
		//合并get post里面的数据
		$arr = array_merge($newary,array_merge_recursive($_GET, $_POST));
		return $arr;
	}
    
    /**
    * 得到当前页码
    * update by baoxianjian 16:23 2015/3/22
    * @param string $inPath
    * @return int
    */
    public function getPageNumber()
    {
         //$get=$this->getUrlParams($inPath);
         return max(1,$this->request['page']);
    }
    
	//ajax返回信息统一格式化 array('status'=>1,'data'=>array());如需要其它参数那么可再添加其它参数
	protected function printJson($data) {
		$jcb = SUtil::getStr($_REQUEST['jsoncallback']);
		if ($jcb) {//如果是跨域操作
			return $jcb . "(" . json_encode($data) . ");";
		} else {
			return json_encode($data);
		}
	}

    
	/**
	 * 分页函数 
     * 将$limit放在$inPath后(因为$limit不常用) update by baoxianjian  17:52 2015/3/21
	 * @param unknown $count 总条数
	 * @param unknown $i_page 当前页数
	 * @param unknown $inPath 目录参数
     * @param unknown $limit 每页显示条数
	 * @param string $style 样式名称
	 * @param string $anchor URL GET 参数 默认为空自动补全当页get参数
	 */
	protected function pageBar($count, $i_page, $inPath, $limit=10, $style = 'style1', $anchor = '') {
		//当没有传参数时并且URL中有get参数时补全
		//die($_SERVER['QUERY_STRING']);
        	if(!$style){$style='style1';}
		if (is_string($anchor) && $anchor=='' && $_SERVER['QUERY_STRING']) {
			$anchor = '?'.$_SERVER['QUERY_STRING'];
		}

		$pagenum = ceil($count / $limit);
		$i_page = max(1, $i_page);
		$page = min($pagenum, $i_page);
		$prepg = $page - 1;
		$nextpg = $page == $pagenum ? 0 : $page + 1;
		$offset = ($page - 1) * $limit;
		$startdata = $count ? ($offset + 1) : 0;
		$enddata = min($offset + $limit, $count);
		$rule = "{$inPath[0]}/{$inPath[1]}";
		$pars = $this->getUrlParams($inPath);
        
                //update by baoxianjian 22:42 2015/3/26 去掉无用参数
                foreach ($pars as $k=>$v) { 
                    if($k == 'seek'){   //不要随便改底层
                        unset($pars[$k]);
                    }
         
                    if(!$v) unset($pars[$k]);
                }
        
		if (array_key_exists('page', $pars)) {
			unset($pars['page']);
		}
		$params['totalSize'] = $count;
		$params['pageSize'] = $limit;

		$params['first'] = SRoute::createUrl($rule, array('page' => 1), $pars) . $anchor;
		if (!empty($nextpg)) {
			$params['nextpg'] = SRoute::createUrl($rule, array('page' => $nextpg), $pars) . $anchor;
		} else {
			$params['nextpg'] = null;
		}
		if (!empty($prepg)) {
			$params['prepg'] = SRoute::createUrl($rule, array('page' => $prepg), $pars) . $anchor;
		} else {
			$params['prepg'] = null;
		}
		$params['last'] = SRoute::createUrl($rule, array('page' => $pagenum), $pars) . $anchor;
		$params['startdata'] = $startdata;
		$params['enddata'] = $enddata;
		$params['currpage'] = $i_page;
		$params['pagenum'] = $pagenum;
		if ($pagenum > $this->app_showpagenum) {
			if ($i_page >= $this->app_showpagenum) {
				$params['start'] = (int)($i_page - $this->app_showpagenum / 2);
				$params['max'] = $this->app_showpagenum;
				if ($pagenum - $params['start'] < $this->app_showpagenum) {
					$params['start'] = $pagenum - $this->app_showpagenum;
				}
			} else {
				$params['start'] = 0;
				$params['max'] = $this->app_showpagenum;
			}
		} else {
			$params['start'] = 0;
			$params['max'] = $this->app_showpagenum;
		}
		for ($i = $params['start']; $i < min(($params['start']+$params['max']),$params['pagenum']); $i++) {
			$params['pages'][$i]['page'] = $i + 1;
			$params['pages'][$i]['url'] = SRoute::createUrl($rule, array('page' => $params['pages'][$i]['page']), $pars) . $anchor;
		}
		if ($this->app_tplpagedir) {
			return $this->render($this->app_tplpagedir."/page/$style.html", $params);
		}
       //update by baoxianjian 
        //print_r($params);
       // exit;
		return $this->render("common/page/$style.html", $params);
	}
	
    /**
     * 分页函数 
     * 将$limit放在$inPath后(因为$limit不常用) update by baoxianjian  17:52 2015/3/21
     * @param unknown $count 总条数
     * @param unknown $i_page 当前页数
     * @param unknown $inPath 目录参数
     * @param unknown $limit 每页显示条数
     * @param string $style 样式名称
     * @param string $anchor URL GET 参数 默认为空自动补全当页get参数
     */
    protected function pageBarS($count, $i_page, $tpl, $tpli, $limit=10, $style = 'style1') {
        //当没有传参数时并且URL中有get参数时补全
        //die($_SERVER['QUERY_STRING']);
        
        if(!$tpl)
        {
            echo 'tpl is null';
            exit;
            //$tpl = '/{$page}.shtml';
        }

        $pagenum = ceil($count / $limit);
               
        if($i_page > $pagenum)
        {
            Header("HTTP/1.1 412 page overflow");
            echo 'error: page overflow';
            exit;
        }

        $i_page = max(1, $i_page);
        $page = min($pagenum, $i_page);
        $prepg = $page - 1;
        $nextpg = $page == $pagenum ? 0 : $page + 1;
        $offset = ($page - 1) * $limit;
        $startdata = $count ? ($offset + 1) : 0;
        $enddata = min($offset + $limit, $count);
       // $rule = "{$inPath[0]}/{$inPath[1]}";
        //$pars = $this->getUrlParams($inPath);
        
        //update by baoxianjian 22:42 2015/3/26 去掉无用参数
        foreach ($pars as $k=>$v) {
            if(!$v) unset($pars[$k]);
        }
        
        if (array_key_exists('page', $pars)) {
            unset($pars['page']);
        }
        $params['totalSize'] = $count;
        $params['pageSize'] = $limit;
                                    
        $params['first'] = $this->_pageBarTplConvert(1,$tpl,$tpli);  //str_replace($tpl_var,'1',$tpl);
        if (!empty($nextpg)) {
            //$params['nextpg'] = SRoute::createUrl($rule, array('page' => $nextpg), $pars) . $anchor;
            $params['nextpg'] = $this->_pageBarTplConvert($nextpg,$tpl,$tpli);//str_replace($tpl_var,$nextpg,$tpl); 
        } else {
            $params['nextpg'] = null;
        }
        if (!empty($prepg)) {
            $params['prepg'] = $this->_pageBarTplConvert($prepg,$tpl,$tpli);//str_replace($tpl_var,$prepg,$tpl);
        } else {
            $params['prepg'] = null;
        }
        $params['last'] = $this->_pageBarTplConvert($pagenum,$tpl,$tpli);//str_replace($tpl_var,$pagenum,$tpl); // SRoute::createUrl($rule, array('page' => $pagenum), $pars) . $anchor;
        $params['startdata'] = $startdata;
        $params['enddata'] = $enddata;
        $params['currpage'] = $i_page;
        $params['pagenum'] = $pagenum;
        if ($pagenum > $this->app_showpagenum) {
            if ($i_page >= $this->app_showpagenum) {
                $params['start'] = (int)($i_page - $this->app_showpagenum / 2);
                $params['max'] = $this->app_showpagenum;
                if ($pagenum - $params['start'] < $this->app_showpagenum) {
                    $params['start'] = $pagenum - $this->app_showpagenum;
                }
            } else {
                $params['start'] = 0;
                $params['max'] = $this->app_showpagenum;
            }
        } else {
            $params['start'] = 0;
            $params['max'] = $this->app_showpagenum;
        }
        for ($i = $params['start']; $i < min(($params['start']+$params['max']),$params['pagenum']); $i++) {
            $params['pages'][$i]['page'] = $i + 1;              
            $params['pages'][$i]['url'] = $this->_pageBarTplConvert($params['pages'][$i]['page'],$tpl,$tpli);//str_replace($tpl_var,$params['pages'][$i]['page'],$tpl);
            //SRoute::createUrl($rule, array('page' => $params['pages'][$i]['page']), $pars) . $anchor;
        }
       
        if ($this->app_tplpagedir) {
            return $this->render($this->app_tplpagedir."/page/{$style}.html", $params);
        }
       //update by baoxianjian 
        //print_r($params);
       // exit;
        return $this->render("common/page/$style.html", $params);
    }
    
    private function _pageBarTplConvert($tpl_val,$tpl,$tpli)
    {
        $tpl_var = '{$page}';
        $str = str_replace($tpl_var,$tpl_val,$tpl);
        $str = str_replace($tpli,'',$str);
        return $str;  
          
    }
    
    
        /**
    * 得到相应模版路径 
    * add by baoxianjian 20:35 2015/3/21
    * @param mixed $filename
    */
    protected function buildTplFilePath($filename='',$sysname='') 
    {
        if(CUR_ACTION=='del' || CUR_ACTION=='delAll')
        {
            $this->tplFilePath=$this->tplName.'/common/show_message.html';    
            return $this->tplFilePath;
        }
        $dir=$this->tplName;
        if($sysname=='')
        {
            if(defined('SYS_NAME'))
            {
                $sysname=SYS_NAME;
            }
        }
        $dir.= '/'.$sysname;
        if($filename==''){
            $filename=CUR_PAGE.'.'.CUR_ACTION;
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
            
            if(intval($type) > 99)
            {
                $this->ass('SYS_MESSAGE',$msg);
                $this->ass('TO_URL',$tourl);
                $this->tplFilePath=$this->tplName."/common/msg_{$type}.html";
                echo $this->template();
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
    }
    
    
    /**
    *  设置seo的信息
    *  add by baoxianjian 21:47 2015/5/15
    * @param mixed $title
    * @param mixed $description
    * @param mixed $keywords
    */
    protected function setSEO($title='',$keywords='',$description='')
    {
        #设置下默认值  
        /*
        if(!$title){$title='xxx';} //应来自于常量
        if(!$description){$description='xxx';} 
        if(!$keywords){$keywords='xxx';} 
        */
        
        /* 
        #设置下后缀         
        $title.=' -xxx';   //应来自于常量  
        $description.=' -xxx';
        $keywords.=' -xxx';
        */
        
        
        
        $a['title']=$this->trimSEO($title);
        $a['description']=$this->trimSEO($description);
        $a['keywords']=$this->trimSEO($keywords);
        $this->ass('SEO',$a);
    }
    
    private function trimSEO($str,$char='_')
    {
        $str=preg_replace('/'.$char.'+/',$char,$str);
        return trim($str,'_');
    }
    
    

//---------------------------前台主站-------------------------   

   
   private $_mdl_user;
    
    protected $tplName='tpl1';
    protected $styleName='style1';
    protected $tplFilePath; 
    
    private function _getMdlUser()
    {
        if(!$this->_mdl_user)
        {
            $this->_mdl_user=new Model_user_user();
        }
        return $this->_mdl_user;
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
    
    
    protected function checkSessionStart($sid='')
    {

      // if(!$_SESSION || !$_SESSION['uid'])
      // { 
            if($sid){session_id($sid);}
            session_start();
      // } 
        return intval($_SESSION['uid']);
    }
    
    //检查session
    protected function checkSession($sid='')
    {       
        //PHPSESSID 
        $this->checkSessionStart(); //先用默认的PHPSESSID进行启动
        $uid=intval($_SESSION['uid']);
        
        if(!$uid)//无法启动
        { 
            $sid=$_COOKIE['sid'];

            if(!$sid){return false;} //cookie没得值 已经过期

            $this->_getMdlUser(); 

            $rs=$this->_mdl_user->getSessionBySid($sid); //从数据库中得到 row_session
 
            if(!$rs){return false;}   //数据库中没有此条session
            
            $this->checkSessionStart(); //从数据库中恢复会话

            $_SESSION['uid']=$rs['user_id'];
            $_SESSION['aid']=$rs['session_agent'];
            $_SESSION['lip']=$rs['login_ip'];
            $_SESSION['uname']=$rs['user_name'];
            $_SESSION['um']=$rs['user_mods'];
                
            if($rs['user_mods'] && $this->checkUserMods(2,$rs['user_mods']))
            {              
                $mdl_cmp=new Model_cmp_company();  
                $row_cmp=$mdl_cmp->getRowByUserId($rs['user_id']);
                
                $_SESSION['cid']=$row_cmp['cmp_id'];
                //vip判断
                if(intval($row_cmp['cmp_type']==6) && SUtil::nowInTimeRange($row_cmp['start_time'],$row_cmp['end_time']))
                {
                    $cvip=1;                            
                }
                $_SESSION['cvip']= intval($cvip);
            }
        }

        if(!$uid=intval($_SESSION['uid'])){return false;}
        if($_SESSION['aid']!=md5($_SERVER['HTTP_USER_AGENT'])){return false;}
        if($_SESSION['lip']!=SRemote::getRealIp()){return false;}
        return $uid; 
         
    }
    
    /**
    * 从SESSION中得到用户
    * 
    * @param bool $u_exit 用户id检查
    * @param bool $m_exit  用的模块检查
    * @return array
    */
    protected function getUserFromSession($u_exit=false,$m_exit=false)
    {

        $uid=$this->checkSession();


        if($u_exit && !$uid)
        { 
            $re_login=true;
        }
        if($m_exit && !$re_login)
        {   
            if(!$this->checkUserMods(1,$_SESSION['um'])) //1为代理商
            {
               $re_login=true; 
            }
        }
        
        if($re_login)
        {
            
            $this->clearSession();
            $this->showMessage('登录状态过期，请重新登录',105);
            /*
            $this->tplFilePath=$this->tplName.'/common/show_message.html';
            $this->showMessage('登录状态过期，请重新登录',3,'/user/login',2000);
            echo $this->template(); 
            */
            exit;
            
        }
        return array('uid'=>$uid,'name'=>$_SESSION['uname']);
    }
    
    //从session中得到企业
    protected function getCompanyFromSession($c_exit=false,$m_exit=false)
    {     
         $ru=$this->getUserFromSession(0);
         
         $cid=$_SESSION['cid'];
         if($c_exit && !$cid)
         {
            $re_login=true;
         }
         if($m_exit && !$re_login) 
         {
             if(!$this->checkUserMods(2,$_SESSION['um'])) //2为企业    
             {
                $re_login=true; 
             }  
         }

         if($re_login)
         {
             $this->clearSession();
             $this->showMessage('登录状态过期，请重新登录',105); 
             /*       
             $this->tplFilePath=$this->tplName.'/common/show_message.html';
             $this->showMessage('登录状态过期，请重新登录',3,'/user/login',2000);
             echo $this->template(); 
             */
             exit;
         }

         $ru['cid']=$cid;
         $ru['cvip']=$_SESSION['cvip']; 
         return $ru;
    }
    
    //得到用户id
    protected function getUserId($exit=false)
    {
        $ru=$this->getUserFromSession($exit);
        return $ru['uid'];
    }
    
    //得到企业id
    protected function getCmpId($exit=false)
    {
        $rc=$this->getCompanyFromSession($exit);
        return $rc['cid'];
    }
    
    //得到用户类型
    protected function getUserType()
    {
        $rc=$this->getCompanyFromSession(0);
        if($rc['cvip'])
        {
            $user_type=21; //企业VIP
        }
        else if ($rc['cid'])
        {
            $user_type=20; //企业用户
        }
        else if($rc['uid'])
        {
            $user_type=10; //代理商用户
        }
        else
        {
            $user_type=0;  //游客
        }
        return $user_type;
    }
    
    //检查用户开通的模块
    protected function checkUserMods($mod_id,$user_mods='')
    {
        if(!$mod_id=intval($mod_id)){return false;}
        
        if(!$user_mods)
        {
            $this->checkSession();
            $user_mods = $_SESSION['um'];
        }
        if(!$user_mods){return false;}
        
        $u_mods=explode(',',$user_mods);
        if(!in_array($mod_id,$u_mods)){return false;} //2为企业
        return true;
    }
    
    //清除session
    protected function clearSession()
    {
        $this->checkSession();  
        session_destroy();
        setcookie("sid",'',-1,"/",ROOT_DOMAIN); 
    }
    


}
