<?php
/**
* @name: 得到导航信息模型 
* @author: baoxianjian
* @date: 22:44 2015/5/5
*/

class Model_com_navigations extends Base_model{        
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'xx';
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'xx';
        
    }
    //@override
       public function setCache($status=false){
        $this->_cache = $status;
    }    
    
    public function getListStatic()
    {
        return array('main'=>WWW_URL,
        'yyzs'=>'http://yyzs.'.ROOT_DOMAIN,
        'ypzs'=>'http://ypzs.'.ROOT_DOMAIN,
        'otc'=>'http://otc.'.ROOT_DOMAIN,
        'bjp'=>'http://bjp.'.ROOT_DOMAIN,
        'yz'=>'http://yz.'.ROOT_DOMAIN,
        'ylqx'=>'http://ylqx.'.ROOT_DOMAIN,
        'daili'=>WWW_URL.'/daili/',
        'zixun'=>NEWS_URL,
        #疾病查询
        'jibing'=>BZ_URL.'/jibing/',
        'company'=>WWW_URL.'/company/',
        'product'=>WWW_URL.'/product/',
        'shuju'=>'http://shuju.'.ROOT_DOMAIN);
    }
    
    public function getListDynamic()
    {
        return array('main'=>'http://www.'.ROOT_DOMAIN,
        'yyzs'=>WWW_URL.'/yyzs/',
        'ypzs'=>WWW_URL.'/ypzs/',
        'otc'=>WWW_URL.'/otc/',
        'bjp'=>WWW_URL.'/bjp/',
        'yz'=>WWW_URL.'/yz/',
        'ylqx'=>WWW_URL.'/ylqx/',
        'daili'=>WWW_URL.'/daili/',
        'zixun'=>NEWS_URL,
        'jibing'=>BZ_URL.'/jibing/',
        'company'=>WWW_URL.'/company/',
        'product'=>WWW_URL.'/product/',
        'shuju'=>'http://shuju.'.ROOT_DOMAIN);
    }

    
}
    

