<?php
/**
 * 此项目controller基类，可对Base_controller中的方法进行重写覆盖
 **/
define('PAGE_VAR_NAME','pagehtml');
define('LIST_VAR_NAME','list');


class Controller_basepage extends Base_controller {
    public $_userid;//用户名
    public $_username;//用户ID\
    
    protected $tplName='tpl1';
    protected $styleName='style1';
    protected $tplFilePath;


    
    public function __construct($in_array) {
    	//!isset($_SESSION) && session_start();//自动开启session 
		parent::__construct($in_array);
		//以下目录不填写表示使用默认
		$this->app_picdir = 'www';//图片上传目录名

		$this->app_tplpagedir = '';//分页函数模板目录
		$this->app_tplmsgdir = '';//提示消息模板目录
            
            /*                                       
        //登录检查在基类进行 update by baoxianjian
        //过滤 无需登录检测的页面和动作
        $filters=array(
        'user'=>array('login','dologin','register','logout'),
        );
        
        //$check_login=true;
        foreach ($filters as $fk=>$fv){
            if($fk==CUR_PAGE){
                 if(in_array(CUR_ACTION,$fv)){
                    $check_login=false;
                    break;    
                 }
            }                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
        }
                                                                                                          
        if($check_login){
            $this->user=$this->getUserFromSession();
        }
        else
        {
            $this->user=array('id'=>0,'name'=>'ghost');
        }
        */

        $this->buildTplFilePath();
        
        

        $this->ass('STYLE_URL',ASSETS_URL.'/'.$this->styleName);
        $this->ass('TPL_URL',$this->tplName);
        
        $this->ass('WWW_URL', WWW_URL); 
        $this->ass('JS_URL', IMG1_URL.'/res/js');
        $this->ass('CSS_URL', IMG1_URL.'/res/css');
        $this->ass('IMG_URL', IMG1_URL.'/res/img');
        $this->ass('ZIXUN_URL', NEWS_URL);
        $this->ass('BZ_URL', BZ_URL);
        $this->ass('USER_URL', USER_URL);
        
        $this->ass('HEADER',$this->tplName.'/common/main.header.html');
        $this->ass('HEADER_END',$this->tplName.'/common/main.header_end.html'); 
        $this->ass('FOOTER',$this->tplName.'/common/main.footer.html');
        $this->ass('NAV_ACTIVE',array(CUR_PAGE=>'class="active_nav"'));
		
        //$this->ass('USER_TYPE',$this->getUserType());
        //$this->ass('USER_INFO',$this->getCompanyFromSession(0));
	//	$this->ass('SEO', '');

        
       // $this->ass('LEFT',$this->tplName.'/common/user.left.html');

      
        $ru = $this->getCompanyFromSession(0);
        $this->ass('COMPANY_IS_VIP',$ru['cvip']);
       
        
                

       // $this->ass('SEARCH_URL',SEARCH_URL);
        //$this->ass('common',TEMPLATE_MAIN_URL.'/'.'common');
       // $this->ass('EDIT_URL',$this->)
        
        
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
    
    
            
    
    
    
    
    
    
 

   
     

  
    
}


/**
	End file,Don't add ?> after this.
*/
