<?php
/**
* @name: 企业控制器 
* @author: baoxianjian
* @date: 16:36 2015/4/6
*/        
define('SYS_NAME','cmp');


class Controller_company extends Controller_basepage {
         
    /**
     * 医药公司列表视图
     * @param unknown $inPath
     * @return Ambigous <mixed, string, void, string>
     */
    function pageIndex($inPath){
        //得到区域数组
       
        $mdl_area=new Model_com_area();
        $area_rowset = $mdl_area->getAllRowset(false,false);
        
        //获得一级产品类型
        $mdl_pdtype=new Model_pdt_pdtTypes();
        $data=$mdl_pdtype->getListAlltypeone($page);
        $tplist=$data['list'];
        $this->ass('tplist', $tplist);
        
        
        //公司model
        $mdl_company = new Model_cmp_company();
        
        //获取分页
        $page = $this->getPageNumber();
        
        //获取列表数据
        $k=$this->request['k'];
           $main_type=$this->request['main_type'];
           $city_name=$this->request['city_name'];      
               
           $srow=array('main_type'=>$main_type,'city_name'=>$city_name,'k'=>$k, 'audit_state'=>2);
                                     
           
           $company_results = $mdl_company->getPreListAll($page,$srow);
           $count = $company_results['count'];
           
           //分页字符串
           $pageShow = $this->pageBar($count, $page, $inPath,10,false,false);
           
           //重组数据
           foreach ( $company_results['list'] as $key=>$v ){
               
               //设置默认图片
               $v['cmp_img'] == NULL && $v['cmp_img'] = '/static/search_default.jpg';
            
            $company_results['list'][$key]['linkurl'] = "/company/{$v['cmp_id']}/";//SRoute::createUrl('company/about', array('id'=>$v['cmp_id']));
               
               //限制主营产品字数
               mb_strlen($v['main_products'], 'utf8') > 40 && $v['main_products'] = mb_substr($v['main_products'], 0, 40, 'utf8');
               
               
           }
           
   // die(SUtil::P($company_results));
        
           $tplArr = array(
                   
                   'list'=>$company_results['list'],
                   'pagehtml'=>$pageShow,//分页字符
                   
           );
           
           $this->preparRow($row);
           $this->ass('row',$srow);
           $this->ass('area',$area_rowset);
                      
            
           $this->ass('num', $count);
           $this->ass('ye', ceil($count/10));
           $this->ass('page', $page);
           
           if(count($srow)!=''){
               foreach($tplist as $v){
                   if($srow['main_type']==$v['pt_id']){
                       $main_type=$v['pt_name'];
                   }
               }
                
               $title="医药企业列表{$main_type}{$city_name}_3156医药网_全国药品网";        
           }else{
               $title='医药企业列表_医药公司_医药招商企业_全国药品网-3156医药网';
           }
       
           $keywords='医药企业列表,医药公司,医药企业,医药经营公司,医药招商企业,医药生产企业';
           $description='医药公司列表包括医药招商企业、医药生产企业、医药经营公司等,为您提供丰富的招商公司和招商产品。';
           $this->setSEO($title,$keywords,$description);
           
           
        return $this->template($tplArr);
    }
    
    
    
    function pageAbout($inPath){
        //公司model
        
        
        $id = intval($this->request['id']);
        $mdl_company = new Model_cmp_company();
        $cmpdata = $mdl_company->getRowById($id);
        
        if(!$cmpdata){return false;}

        
        $cmpdata['list_licence_pic']=explode(',',$cmpdata['licence_pic']);
    
        //产品mdl 得到产品
        $mdl_pdt= new Model_pdt_products();
        $data=$mdl_pdt->getListAllcmppdt($page,$id);
                    
        foreach($data['list'] as $key=>$value){
            $data['list'][$key]['url'] =  "/product/{$value['pdt_id']}.shtml";//SRoute::createUrl('product/info', array('id'=>$value['pdt_id']));
        }
                
        $this->ass('list_pdt', $data['list']);
        
        
        if(empty($cmpdata)){
            $this->showMessage('非法操作!', 1);
            exit();
        }
        $this->ass('row', $cmpdata);
    //    $this->ass('list_pdt', $this->pageHotpdt($id, 4));
        
        $cmpdata['cmp_intro']=trim($cmpdata['cmp_intro']);
        $cmpdata['cmp_intro'] = str_replace(array("/r/n", "/r", "/n"), "", $cmpdata['cmp_intro']);
        $cmpdata['cmp_intro']=SString::tcutstr( $cmpdata['cmp_intro'],80);
        
        $title="{$cmpdata['cmp_name']}介绍-3156医药网";
        $keywords="{$cmpdata['cmp_name']},{$cmpdata['cmp_name']}招商,{$cmpdata['cmp_name']}联系方式";
        $description="{$cmpdata['cmp_intro']}";
        $this->setSEO($title,$keywords,$description);
        return $this->template($tplArr);
    }
    
    
    
    function pageProduct($inPath){
        
        
        $page = $this->getPageNumber();
        $id = intval($this->request['id']);
        $mdl_company = new Model_cmp_company();
        $cmpdata = $mdl_company->getRowById($id);
        $cmp_name=$cmpdata['cmp_name'];
        
        
        
        if(empty($cmpdata)){
            $this->showMessage('非法操作!', 1);
            exit();
        }
        
        
        
        //产品mdl 得到右边热门产品
        $mdl_pdt= new Model_pdt_products();
        $pdtdata=$mdl_pdt->getListAllcmppdt($page,$id);
            
        foreach($pdtdata['list'] as $key=>$value){
            $pdtdata['list'][$key]['url'] = "/product/{$value['pdt_id']}.shtml";//SRoute::createUrl('product/info', array('id'=>$value['pdt_id']));
        }
        
        $this->ass('list_pdt', $pdtdata['list']);
        
        
        
        
        
        $mdl_pdt_products = new Model_pdt_products();
        
        $data=$mdl_pdt_products->getListAllpdtpaly($page, $id);

        foreach($data['list'] as $key=>$value){
            $data['list'][$key]['linkurl'] = "/product/{$value['pdt_id']}.shtml";//SRoute::createUrl('product/info', array('id'=>$value['pdt_id']));
        }
        
        $param[LIST_VAR_NAME] = $data['list'];
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath, $limit=9, $style = 'style1', false);
/*        
        $productsdata = $mdl_pdt_products->getListAll($page, array('PRC.cmp_id'=>$id, 'PRC.audit_state'=>2, 'PRC.is_del'=>2));
        foreach($productsdata['list'] as $key=>$value){
            $productsdata['list'][$key]['linkurl'] = "/product/{$value['pdt_id']}.shtml";//SRoute::createUrl('product/info', array('id'=>$value['pdt_id']));
        }
        $list = $productsdata['list'];
        $pageShow = $this->pageBar($productsdata['count'], $page, $inPath);
*/        
        
        $this->ass('list', $data['list']);
        $this->ass('row', $cmpdata);
//        $this->ass('list', $productsdata['list']);
        $this->ass('pagehtml', $pageShow);
        $title="{$cmp_name}_{$cmp_name}产品图片_产品代理-3156医药网";
        $keywords="{$cmp_name},{$cmp_name}产品图片,产品展示,产品代理";
        $description="{$cmp_name}产品展示。";
        $this->setSEO($title,$keywords,$description);
    //    return $this->template($tplArr);
        return $this->template($param);
    }
    
    
    function pageZixun($inPath){
        $page = $this->getPageNumber();
        $id = intval($this->request['id']);
        $mdl_company = new Model_cmp_company();
        $mdl_arttype = new Model_news_type();
        $cmpdata = $mdl_company->getRowById($id);
        $cmp_name=$cmpdata['cmp_name'];
        
       
        
            //产品mdl 得到右边热门产品
        $mdl_pdt= new Model_pdt_products();
        $pdtdata=$mdl_pdt->getListAllcmppdt($page,$id);
            
        foreach($pdtdata['list'] as $key=>$value){
            $pdtdata['list'][$key]['url'] = "/product/{$value['pdt_id']}.shtml";//SRoute::createUrl('product/info', array('id'=>$value['pdt_id']));
        }
        
        $this->ass('list_pdt', $pdtdata['list']);
        
        
        
        
        if(empty($cmpdata)){
            $this->showMessage('非法操作!', 1);
            exit();
        }
        $mdl_news = new Model_news_news();
        $newsdata = $mdl_news->getListAll($page, array('com_id'=>$id, 'is_del'=>0, 'audit_state'=>2));
        
        $list=$newsdata['list'];
        foreach($list as $key=> $value){
            $type_data = $mdl_arttype->getRowById($value['type_id2']);
            $list[$key]['type_name'] = $type_data['name']; 
            //$list[$key]['linkurl'] = SRoute::createUrl('main/show', array('en'=>$type_data['en_name'], 'id'=>$value['news_id']));
            $list[$key]['linkurl'] = "{$ZIXUN_URL}/u{$value['admin_id']}a{$value['news_id']}.shtml";//资讯详情页地址  add by zhangl
        }
        
        $count = $mdl_news->count(array('com_id'=>$id, 'is_del'=>0, 'audit_state'=>2));
        $pageShow = $this->pageBar($count, $page, $inPath);
        $this->ass('row', $cmpdata);
        $this->ass('list', $list);
        $this->ass('cmp_id', $id);
        $this->ass('pagehtml', $pageShow);
        // $this->ass('list_pdt', $this->pageHotpdt($id, 4));
        
        $title="{$cmp_name}-3156医药网";
        $keywords="{$cmp_name},{$cmp_name}招商";
        $description="企业资讯收录了{$cmp_name}最新的企业资讯、企业新闻,为代理商代理药品提供实时、权威的企业资讯。";
        $this->setSEO($title,$keywords,$description);
        
        return $this->template($tplArr);
    }
    
    
    //默认选中设置
    private function preparRow($row)
    {
        if($row)
        {
            //选中设置
            $main_type_checked=array($row['main_type']=>"style='background: none repeat scroll 0 0 #fb8200;'");
            $city_name_checked=array($row['city_name']=>"style='background: none repeat scroll 0 0 #fb8200;'");
        
            $this->ass('main_type_checked',$main_type_checked);
            $this->ass('city_name_checked',$city_name_checked);
            
            $this->ass('row',$row);
    
        }
    }
    
    public function pageHotpdt($cmpid, $limit){
        $mdl_company = new Model_cmp_company();
        $cmpdata = $mdl_company->getRowById($cmpid);
        if(empty($cmpdata)){
            return false;
        }
        $mdl_pdt_products = new Model_pdt_products();
        $productsdata = $mdl_pdt_products->getListAll($page, array('PRC.cmp_id'=>$cmpid, 'PRC.audit_state'=>2, 'PRC.is_del'=>2), $limit, ' ORDER BY PRC.click_count');
        foreach($productsdata['list'] as $key=>$value){
            $productsdata['list'][$key]['linkurl'] = "/product/{$value['pdt_id']}.shtml";//SRoute::createUrl('product/info', array('id'=>$value['pdt_id']));
            if($value['img']){
            $productsdata['list'][$key]['imgurl'] = $value['img'].'?w=118&h=88';
            }
            $productsdata['list'][$key]['info'] = SString::tcutstr($value['spec'], 80);
        }
        $list = $productsdata['list'];
        return $list;
    }
    
    
    
    
    
    
    
    
    
    
}