<?php
 /**
* @name: 系统管理员的数据模型 
* @author: baoxianjian
* @date: 20:10 2015/4/8
*/
class Model_sys_groups extends Base_model{
    private $_saId;
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'sg_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'sys_groups'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    //初始化，必填said
    function __construct($said){
        parent::__construct();
        if(!$said=intval($said))
        {
            exit('groups:sa id can not be null.');
        }
        $this->_saId=$said;
    }
    
    /**
    * 得到用户组列表所有  
    * 
    * @param int $page
    * @param array $srow
    * @return array|false
    */
    public function getListAll($page,$srow=array())
    {
        $condition = "sg.is_del=0";
        
        $condition.=$this->_getMoreCondition();
        $condition=str_replace('sa_id','sg.sa_id',$condition);  
     
        //查询条件
        if($id=intval($srow['id']))
        {
            $condition.=" AND sg.{$this->_PK} ={$id}"; 
        }
        if($sg_name=trim($srow['sg_name']))
        {
            $condition.=" AND sg.sg_name LIKE '%{$sg_name}%'";
        }
        
        $this->_tableName='sys_groups sg';
        //$leftjoin="left join search_ggs sg ON sgp.sgp_id=sg.sgp_id ";
        $leftjoin=array('sys_admins sa'=>'sg.sa_id=sa.sa_id');
        $data=$this->getList($condition,"sg.*,sa.sa_name","ORDER BY sg.sg_id DESC",$page,10,true,$leftjoin);          
        $this->_tableName='sys_gruops';    
        return $data; 

        
    }


    
    /**
    * 得到所有数据
    * 
    */
    public function getRowsetAll($myg=1)
    {
        $condition = "is_del=0"; //查询条件
        
        $condition.=$this->_getMoreCondition('<=',$myg);  

        return $this->getList($condition,array('*'),array(),0,0,false);  
    }
    
    
    /**
    * 根据主键id删除一行数据
    * 
    * @param int $id
    * @return int|false
    */
    public function deleteRowById($id,$destroy=false)
    {
        if(!$id=intval($id)) return 0;
        
        $condition="{$this->_PK}={$id}"; 
       
        $condition.=$this->_getMoreCondition('<');  
        
        return $this->update($condition, array('is_del=1'));
        //return $this->delete(array($this->_PK=>$id));
    }
    


    /**
    * 修改一条记录（根据Id）
    * 
    * @param array $row
    * @param int $id
    * @return int|false
    */
    public function updateRowById($row,$id=0)
    {
        if(!$row) return false;
        if(!$id=intval($id)){return false;}

        $condition="{$this->_PK}={$id}"; 
        $condition.=$this->_getMoreCondition('<');   
        
        unset($row[$this->_PK]);
        return $this->update($condition,$row);
    }
    
    /**
    * 得到一条记录（根据id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getRowById($id)
    {
        if(!$id=intval($id)){return false;}
        
        $condition="{$this->_PK}={$id}"; 
        $condition.=$this->_getMoreCondition();    
        
        return $this->getOne($condition);
    }
    
    /**
    * 得到系统权限所有
    * 
    */
    public function getPermissionsAll($myp,$level=0)
    {
        if(!$myp && !$level){return false;}
        //系统 表 操作
         $p=array(
         'com'=>array(
            'main'=>array('index','left','left2','left3','left4','top','center'),
            ),
         'sys'=>array(
            'groups'=>array('list','edit','add','update','grant','del','destroy'),
            'admins'=>array('list','edit','add','update','grant','del','destroy'),
            'notices'=>array('list','edit','add','update','del','destroy'),
			'friendlinks'=>array('list','edit','add','update','del','destroy'),
            ),
         'news'=>array(
			'arttype'=>array('list','listjson','edit','add','update','del','destroy'),
			'article'=>array('list','edit','add','update','del','destroy','audit','publish'),
			'gather'=>array('list','edit','add','update','del','destroy'),
			'keys'=>array('list','edit','add','update','del','destroy'),
			),	
		'pdt'=>array(
			'pdtproducts'=>array('list','edit','add','update','del','destroy','audit','fileinto','ajaxorder'),
			'pdttypes'=>array('list','edit','add','update','del','destroy'),
			'pdtkeys'=>array('list','edit','add','update','del','destroy'),
			
			),
		'block1'=>array(
			'blockkeywords'=>array('list','edit','add','update','del','destroy'),
			'blockkeydata'=>array('list','edit','add','update','del','destroy'),
			
			),	
		'block2'=>array(
			'blockgroups'=>array('list','edit','add','update','del','destroy'),
			'blocktemplates'=>array('list','edit','add','update','del','destroy'),
			'blockblocks'=>array('list','edit','add','update','del','destroy'),
			'blockdata'=>array('list','blocks','edit','add','update','del','destroy'),
			),
		'gg'=>array(
			'ggresource'=>array('list','update'),
            'ggqueue'=>array('list','edit','add','update','del','destroy','audit'),
            'ggposition'=>array('list','edit','add','update','del','destroy'),
            'ggmanage'=>array('list','edit','add','update','del','destroy'),
			'ggmaterials'=>array('list','edit','add','update','del','destroy'),
			'ggstandard'=>array('list','edit','add','update','del','destroy'),
			'ggtemplate'=>array('list','edit','add','update','del','destroy'),
			),	
		 'search'=>array(
            'generalize'=>array('index','editge','on_off','add','update','stopover','del','destroy'),
            'adposition'=>array('list','edit','add','update','del','destroy'),
            'keywords'=>array('list','edit','add','update','del','destroy'),
			'ads'=>array('list','edit','add','update','del','destroy'),
            'main'=>array('list','edit','add','update','del','destroy'),
		    ),
		 'agent'=>array(
            'need'=>array('list','edit','add','check','update','del','delallagent','rec','reallagent','examineallagent','unexamineallagent','destroy','fileinto'),
            'intent'=>array('list','edit','check','update','del','delallcmts','rec','reallcmts','examineallcmts','unexamineallcmts','destroy','fileinto'),
            'stationinfo'=>array('list','edit','add','update','del','destroy'),
			'blacklist'=>array('list','edit','add','update','del','destroy'),
            ),	
		'user'=>array(
			'cmpcompany'=>array('list','edit','add','update','del','destroy','audit'),
			'agency'=>array('list','edit','add','update','del','destroy'),
			),	
         );
         
        return $this->filterPermissions($p,$myp,$level);
    }
    
    /**
    * 从所给权限中过滤出自己的
    * 
    * @param array $allp 所给权限
    * @param array $myp  自己的权限
    */
    public function filterPermissions($allp,$myp,$level=0)
    {
         $p=$allp;
         //如果是10000，可以得到所有权限配置
         if(intval($level)==10000)
         {         
             return $p;  
         }
         
         //json字符串时  
         if(!is_array($myp))
         {
             $myp=json_decode($myp,true);
         }
         
         //必须要有值，配置信息不随意返回
         if(!$myp){return false;}
         
         $p2=array();
         foreach ($p as $k1=>$v1)
         {
             foreach ($v1 as $k2=>$v2)
             {
                 if($myp[$k2])
                 {
                    $p2[$k1][$k2]=$myp[$k2];
                 }
                 /*
                foreach ($v2 as $v3)
                {
                    
                }
                */
             }
         }
         
        return $p2;  
    }

    public function getPermissionsDic()
    {
        $dic=array(
        'list'=>'列表显示',
        'edit'=>'编辑',
        'add'=>'添加',
        'update'=>'修改',
        'grant'=>'赋权',
        'del'=>'删除',
        'destroy'=>'彻底删除',
        'audit'=>'审核',
        'fileinto'=>'批量上传',
        'publish'=>'发布',
        'ajaxorder'=>'保存排序',
        'rec'=>'恢复',
        'check'=>'备注',
        'delallagent'=>'批量删除需求留言',	        
        'reallagent'=>'批量恢复需求留言',
        'examineallagent'=>'批量审核需求留言',
        'unexamineallagent'=>'批量取消审核需求留言',
        'delallcmts'=>'批量删除意向留言',            
        'reallcmts'=>'批量恢复意向留言',
        'examineallcmts'=>'批量审核意向留言',
        'unexamineallcmts'=>'批量取消审核意向留言',
        	
        #com
        'com'=>'公共',
        'main'=>'后台',
        'index'=>'首页',
        'top'=>'顶部',
        'left'=>'内容左边',
        'left2'=>'业务左边',
        'left3'=>'用户左边',
        'left4'=>'系统左边',
        'center'=>'中央',
        #sys
        'sys'=>'系统设置',
        'groups'=>'用户组（角色）',
        'admins'=>'管理员',
        'notices'=>'系统公告',
        'friendlinks'=>'友情链接',
        #news
        'news'=>'资讯管理',
        'arttype'=>'资讯分类',
        'listjson'=>'选择资讯分类',
        'article'=>'资讯列表',
        'gather'=>'文章采集管理',
        'keys'=>'文档关键词管理',  
        #pdt
       'pdt'=>'产品管理',	
       'pdtproducts'=>'产品类表',
       'pdttypes'=>'产品分类',
       'pdtkeys'=>'产品标签管理',
        #block1		
        'block1'=>'关键字专区',
        'blockkeywords'=>'区块关键字列表',
        'blockkeydata'=>'专区数据管理',
        #block2					
        'block2'=>'区块管理',
        'blockgroups'=>'区块分组',
        'blocktemplates'=>'区块模块',
        'blockblocks'=>'区块管理',
        'blockdata'=>'区块数据管理',
        'blocks'=>'区块选择',
        #gg		
        'gg'=>'广告管理',
        'ggresource'=>'广告资源管理',
        'ggqueue'=>'广告单管理',
        'ggposition'=>'广告位管理',
        'ggmanage'=>'分组管理',
        'ggmaterials'=>'素材管理',
        'ggstandard'=>'规格管理',
        'ggtemplate'=>'模块管理',
        #search
        'search'=>'搜索引擎管理',
        'gereralize'=>'推广管理',
	'stopover'=>'过期自动停用',
	'on_off'=>'开启/停用',
        'editge'=>'编辑',
        'adposition'=>'广告位管理',
        'keywords'=>'关键字管理',
        'ads'=>'广告管理',
        'main'=>'数据同步',
        		
        #agent
        'agent'=>'留言管理',
        'need'=>'需求留言管理',
        'intent'=>'意向留言管理',
        'stationinfo'=>'站内来电询单',
		'blacklist'=>'黑名单',
         
        #user
        'user'=>'会员管理',
		'compcompany'=>'厂商管理',
		'agency'=>'代理商管理',		
        );
        return $dic;
    }
    
    //得到更多的条件
    private function _getMoreCondition($lev_op="<=",$myg=0)
    {       
        $mdl_adm=new Model_sys_admins($this->_saId);   
        $row=$mdl_adm->getRowById($this->_saId);
        $level=$row['sa_level'];
        $gid=$row['sg_id'];

        if($myg)
        {
            $myg_str=" OR sg_id={$gid}";
        }
        
        $condition='';
        //超级管理员可以看到所有用户组（角色）
        if($level!=SYS_ADM_LEVEL_MAX)
        {
             $condition=" AND (sa_id={$this->_saId} {$myg_str} OR sg_level{$lev_op}{$level})";
        }
        return $condition;
    }
    
}