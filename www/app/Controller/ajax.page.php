<?php
/**
* @name: 异步加载 
* @author: wukai
* @date: 16:36 2015/5/19
*/        
class Controller_ajax extends Controller_basepage {
         
    function pageHotpdt(){
		$get['jsoncallback'] = $this->request['callback'];
		$id = $this->request['cmp_id'];
		$num = $this->request['list'];
		$mdl_company = new Model_cmp_company();
		$cmpdata = $mdl_company->getRowById($id);
		if(empty($cmpdata)){
			$jsondata = array('status'=>false);
			echo SUtil::apiOut($jsondata, '', $get);
        	exit();
		}
		$mdl_pdt_products = new Model_pdt_products();
		$productsdata = $mdl_pdt_products->getListAll($page, array('PRC.cmp_id'=>$id, 'PRC.audit_state'=>2, 'PRC.is_del'=>2));
		foreach($productsdata['list'] as $key=>$value){
			$productsdata['list'][$key]['linkurl'] = SRoute::createUrl('product/info', array('id'=>$value['pdt_id']));
			$productsdata['list'][$key]['imgurl'] = $value['img'].'?w=118&h=88';
			$productsdata['list'][$key]['info'] = SString::tcutstr($value['spec'], 80);
		}
		$list = $productsdata['list'];
		$jsondata = array('data'=>$list, 'status'=>true);
		echo SUtil::apiOut($jsondata, '', $get);
        exit();
	}
	
	function pageLikenews(){
		$get['jsoncallback'] = $this->request['callback'];
		$type = $this->request['type'];
		if(empty($type)){
			$jsondata = array('status'=>false);
			echo SUtil::apiOut($jsondata, '', $get);
        exit();
		}
		$mdl_news = new Model_news_news();
		$mdl_type = new Model_news_type();
		$type_data = $mdl_type->getRowById($type);
		if(empty($type_data['name'])){
			$jsondata = array('status'=>false);
			echo SUtil::apiOut($jsondata, '', $get);
        	exit();
		}
		$news_data = $mdl_news->getListAll($page, array('type_id2'=>$type));
		foreach($news_data['list'] as $key=>$value){
			$_jsdata[$key]['linkurl'] = "/{$type_data['en_name']}/u{$value['user_id']}a{$value['news_id']}.shtml";
			$_jsdata[$key]['title'] = $value['title'];
		}
		$list = $_jsdata;
		$jsondata = array('data'=>$list, 'status'=>true);
		echo SUtil::apiOut($jsondata, '', $get);
        exit();
	}
}