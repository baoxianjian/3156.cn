<?php
/**
* @name: 企业控制器 
* @author: baoxianjian
* @date: 16:36 2015/4/6
*/        
define('SYS_NAME','phb');


class Controller_paihangbang extends Controller_basepage {
         
    /**
     * 医药公司列表视图
     * @param unknown $inPath
     * @return Ambigous <mixed, string, void, string>
     */
    function pageIndex($inPath){
          
        //通过pdt_type表得到产品类型。
        $mdl_pdtype=new Model_pdt_pdtTypes();
        $list_pdtype=$mdl_pdtype->getListAlltp();
        
        foreach ($list_pdtype['list'] as $v)
        {
            $list_pdtype_temp[$v['parent_id']][]=$v;
        }                    
        $this->ass('list_pdtype',$list_pdtype_temp);
        
        //得到药剂类型数组
        $mdl_products = new Model_pdt_mainProducts();
        $medicamentType = $mdl_products->getMedicamentTypes();
        $this->ass('medicamenttype',$medicamentType);
        
        
         $title="药品招商排行榜_医药招商排行榜_全国药品网-3156医药网";
         $keywords="药品招商,医药招商,医药招商排行榜,中药,西药,保健品招商,药妆,OTC,医疗器械,计生用品,卫生用品,医疗设备,3156医药网";
         $description="3156医药网为您带来最火爆的医药招商、药品招商信息，提供OTC排行榜、保健品排行榜、医疗器械排行榜、药妆排行榜等信息,为您提供最贴心的招商服务,保证让您招商满意!";
         $this->setSEO($title,$keywords,$description);
         
        
        
                                    
        return $this->template($tplArr);
    }
    
    
   
    
    
    
    
    
}