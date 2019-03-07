<?php
/**
 * @author tong
 * @time 2014-2-11 11:28:13 
 */
header("content-type:text/html;charset=utf-8");
class Controller_test extends Controller_basepage {
	//首页
	function pageIndex($inPath){
		//var_dump($inPath);
		//echo '测试一下哈，哈哈';
		//var_dump(SRoute::createUrl('main/index',array('page'=>4),'www'));
		//var_dump($_SERVER);
		//echo 'test';
        //var_dump($inPath);
        $get = $this->getUrlParams($inPath);
        //var_dump($get);
        //var_dump($get);
        $limit = 1;
        $page = max(1,$get['page']);
        /* $param['tempArr'] = array(1=>'测试1',2=>'测试2',3=>'测试3',4=>'测试4',5=>'测试5');
        $param['i'] = 4; */
        
        $mdl_test = new Model_mall_test();
        //$testModel->setReaddb('count');
        $condition = array(1);
        $mdl_test->setCache(false);
        var_dump($_SERVER['QUERY_STRING']);
        $data = $mdl_test->getList($condition,array('*'),array('id'=>'desc'),$limit,$page,true);
        print_r($data);
        $data = $mdl_test->getList($condition,array('*'),array('id'=>'desc'),$limit,$page,true);
        print_r($data);
        $param['list'] = $data['list'];
        exit;
        //$param['pagehtml'] = $this->pageBar($data['count'], $limit, $page, $inPath, $style = 'style1');
        //$param['pagehtml2'] = $this->pageBar($data['count'], $limit, $page, $inPath, $style = 'style2');
        //var_dump($list);
        //$mainServ = new Service_mall_main();
        //$mainServ->test();
        return $this->render('test/index.html',$param);
	}
	function pageTest1(){
	    Lib_comm::notice_app($_GET['id']);
	    $serv = new SService('Service_pay_money');
	    $test = $serv->pay_money(10123,1,'2088002367817914',$remark='',$recharge_type=1,$admin_remark='');
	    var_dump($test);
	    var_dump($serv->getError());
	}
	//文件上传测试
	function pageUpload(){
		var_dump($_FILES);
		$test = $this->getUploadFilePath($_FILES['file']);
		echo SUtil::picsize($test['url'],'150x150');
		var_dump($test);
	}
	
	//添加测试
	function pageAdd(){
		$mdl_test = new Model_mall_test();
		$test = $mdl_test->insert(array(
				'name'=>'测试一个呈',
				'text'=>'测试测试测试测试测试测试测试',
				'datetime'=>date('Y-m-d H:i:s')
		));
		var_dump($test);
	}
	function pageDel(){
		$mdl_test = new Model_mall_test();
		$test = $mdl_test->delete(array('id'=>8));
		var_dump($test);
	}
	
	
}