<?php
/**
* @name: 招商页面联系方式
* @author: baoxianjian
* @date: 10:14 2015/4/24
*/
define('SYS_NAME','');

class Controller_zhaoshang extends Controller_basepage {
	
	function pageContact(){
		$cmpid = intval($this->request['cmpid']);
		$get['jsoncallback'] = $this->request['callback'];
		$mdl_cmp_company = new Model_cmp_company();
		$data = $mdl_cmp_company->getRowById($cmpid);
		if($data['is_check'] && empty($data['is_del']) && $data['cmp_type'] == 6 && $data['end_time']>=NOW && $data['start_time']<=NOW && $data['audit_state'] == 2 && $data['cmp_state'] == 1){
			$jsondata = array('data'=>trim($data['contactcode']), 'status'=>true);
		}else{
			$jsondata = array('data'=>'到期暂时不显示', 'status'=>false);
		}
		echo SUtil::getJson($jsondata, $get);
		exit();
	} 
	
	function pageProducts(){
	
		$cmpid = intval($this->request['cmpid']);
		$get['jsoncallback'] = $this->request['callback'];
		$mdl_pdt_products = new Model_pdt_products();
		$productsdata = $mdl_pdt_products->getRowsetByCmpId($cmpid, '', 10);
		if(count($productsdata)>0){
			$jsondata = array('data'=>$productsdata, 'status'=>true);
		}else{
			$jsondata = array('msg'=>'暂无数据', 'status'=>false);
		}
		echo SUtil::getJson($jsondata, $get);
		exit();
	}
}