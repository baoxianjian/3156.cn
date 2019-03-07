<?php
/**
* @name: 招商页面检索 
* @author: wukai
* @date: 10:32 2015/4/30
**/        

class Controller_zhaoshang extends Controller_basepage {

	
	function pageIndex(){
		SUtil::isAjax()? '':SUtil::success('非法操作');
		$cmpid = $this->request['cmpid'];
		$mdl_cmp_company = new Model_cmp_company();
		$cmp_data = $mdl_cmp_company->getRowById($cmpid);
		if($cmp_data['cmp_type'] != 6){
			$jsondata = array('msg'=>'非法操作！', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}
		if(empty($cmp_data['cmp_id'])){
			$jsondata = array('msg'=>'非法操作！', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}
		$data['title'] = $this->request['title'];
		$data['keywords'] = $this->request['keywords'];
		$data['description'] = $this->request['description'];
		$data['customurl'] = $this->request['customurl'];
		$data['cmp_name'] = $cmp_data['cmp_name'];
        	$data['page_url'] = $cmp_data['page_url'];
        
		$dir = FTPTPL_DIR . '/'.$cmpid.'/';
		//获取目录下面的所有模板文件
		if (is_dir($dir)){
			if ($dh = opendir($dir)){
				while (($file = readdir($dh))!= false){
					$info = pathinfo($file);
					if($info['extension'] == 'shtml'){
						$this->makeShtml($file, $data, $cmpid);
					}
				}
				closedir($dh);
			}
			$jsondata = array('msg'=>'检测通过，生成成功！', 'status'=>true);
			echo SUtil::getJson($jsondata);
			exit();
		}else{
			$jsondata = array('msg'=>'文件不存在，请上传！', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}		
	}
	
	function pageShtml($inPath){
		$questArr = SUtil::html_arr($this->request);
		unset($questArr['page']);
		$page = $this->getPageNumber($inPath);//获取分页参数
		$company_db = new Model_cmp_company();
		$questArr['cmp_type'] = 6;
		$questArr['audit_state'] = 2;
		$limit = 20;
		$company_results = $company_db->getListAll($page, $questArr, $limit);
		$count = $company_results['count'];
		$pageShow = $this->pageBar($count, $page, $inPath, $limit);//获取分页字符串
		foreach ( $company_results['list'] as &$v ){
			//时间格式转化
			$v['start_time'] = SUtil::formatTime($v['start_time']);
			$v['end_time'] = SUtil::formatTime($v['end_time']);				
			$v['cmp_lv'] = $this->ischecklv($v['cmp_lv']);
			//审核状态
			//公司审核状态
			if ( $v['audit_state'] == 1 ){
				$v['audit_state'] = '待审核';
			}elseif ( $v['audit_state'] == 2 ){
				$v['audit_state'] = '审核通过';
			}elseif ( $v['audit_state'] == 3 ){
				$v['audit_state'] = '审核未通过';
			}else{
				$v['audit_state'] = '需重新审核';
			}
		}
		$tplArr = array(
			'list'=>$company_results['list'],
			'pagehtml'=>$pageShow,//分页字符
		);
		$this->ass('seekArr', $questArr);
		return $this->template($tplArr);	
	}
	
	function pageMakeshtml(){
		SUtil::isAjax()? '':SUtil::success('非法操作');
		$cmpids = $this->request['check'];
		$mdl_cmp_company = new Model_cmp_company();
		$ids = $oids = array();
		foreach($cmpids as $value){
			$cmpid = $value;
			$cmp_data = $mdl_cmp_company->getRowById($cmpid);
			if($cmp_data['cmp_type'] != 6 || empty($cmp_data['cmp_id'])){
				$jsondata = array('msg'=>'非法操作！', 'status'=>false);
				echo SUtil::getJson($jsondata);
				exit();
			}else if(empty($cmp_data['page_url'])){
				$jsondata = array('msg'=>'ID:'.$cmpid.'没有自定义URL，无法生成招商页', 'status'=>false);
				echo SUtil::getJson($jsondata);
				exit();
			}else{
				$data['title'] = $cmp_data['title'];
				$data['keywords'] = $cmp_data['keywords'];
				$data['description'] = $cmp_data['description'];
				$data['customurl'] = $cmp_data['page_url'];
				$data['cmp_name'] = $cmp_data['cmp_name'];
				$dir = FTPTPL_DIR . '/'.$cmpid.'/';
				//获取目录下面的所有模板文件
				if (is_dir($dir)){
					if ($dh = opendir($dir)){
						while (($file = readdir($dh))!= false){
							$info = pathinfo($file);
							if($info['extension'] == 'shtml'){
								$this->makeShtml($file, $data, $cmpid);
							}
						}
						closedir($dh);
					}
					$ids[] = $cmpid;
					$mdl_cmp_company->updateRowById(array('is_check'=>1), $cmpid);
				}else{
					$oids[] = $cmpid;
				}
			}
		}
		$msg = '';
		if(count($ids)){
			$msg .=implode(',', $ids).'检测通过，生成成功！';
		}
		if(count($oids)){
			$msg .=implode(',', $oids).'文件不存在，请上传！';
		}
		$jsondata = array('msg'=>$msg, 'status'=>true);
		echo SUtil::getJson($jsondata);
		exit();
	}
	
	function pageContacts(){
		SUtil::isAjax()? '':SUtil::success('非法操作');
		$cmpids = $this->request['check'];
		$mdl_cmp_company = new Model_cmp_company();
		$ids = $oids = array();
		foreach($cmpids as $value){
			$cmpid = $value;
			$cmp_data = $mdl_cmp_company->getRowById($cmpid);
			$data['customurl'] = $cmp_data['page_url'];
			$dir = FTPTPL_DIR . '/'.$cmpid.'/';
			$file ='index.shtml';
			$filepath = $dir.$file;
			if (!file_exists($filepath)) {  
				$jsondata = array('msg'=>'ID:'.$cmpid.'文件不存在，请上传！', 'status'=>false);
				echo SUtil::getJson($jsondata);
				exit();
			}else{
				$zspath = ZSTPl_DIR.'/'.$data['customurl'].'/';
				$contact_path = $zspath.'/img/';
				$filedata = file_get_contents($filepath);
				
				$contactdata = $this->checkIscontact($filedata, $contact_path, $cmpid);
				if($contactdata){
					$ids[] = $cmpid;
				}else{
					$oids[] = $cmpid;
				}
			}
		}
		$msg = '';
		if(count($ids)){
			$msg .=implode(',', $ids).'未过期，生成成功！';
		}
		if(count($oids)){
			$msg .=implode(',', $oids).'已过期，清空联系方式！';
		}
		$jsondata = array('msg'=>$msg, 'status'=>true);
		echo SUtil::getJson($jsondata);
		exit();
	}
	
	
	function pageContact(){
		SUtil::isAjax()? '':SUtil::success('非法操作');
		$cmpid = $this->request['cmpid'];
		$data['customurl'] = $this->request['customurl'];
		$dir = FTPTPL_DIR . '/'.$cmpid.'/';
		$file ='index.shtml';
		$filepath = $dir.$file;
		if (!file_exists($filepath)) {  
			$jsondata = array('msg'=>'文件不存在，请上传！', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}else{
			$zspath = ZSTPl_DIR.'/'.$data['customurl'].'/';
			$contact_path = $zspath.'/img/';
			$filedata = file_get_contents($filepath);

			$contactdata = $this->checkIscontact($filedata, $contact_path, $cmpid);
			if($contactdata){
				$jsondata = array('msg'=>'未过期，生成成功！', 'status'=>true);
				echo SUtil::getJson($jsondata);
				exit();
			}else{
				$jsondata = array('msg'=>'已过期，清空联系方式！', 'status'=>true);
				echo SUtil::getJson($jsondata);
				exit();
			}
		}
	}
	
	private function makeShtml($file, $data, $cmpid){
		$dir = FTPTPL_DIR . '/'.$cmpid.'/';
		if (!file_exists($dir.'index.shtml')) {  
			$jsondata = array('msg'=>'ID：'.$cmpid.'文件不存在，请上传！', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}
		$zspath = ZSTPl_DIR.'/'.$data['customurl'].'/';
		$filepath = $dir.$file;
		$filedata = file_get_contents($filepath);
	
		/*//替换页面img地址
		$reg_footer = '/"img\//is';
		$data_footer = '"'.$cmpid."/img/";
		$filedata = preg_replace($reg_footer, $data_footer, $filedata);*/
		
		$this->checkIsfull($filedata, $cmpid, $file);
		$filedata = $this->regIsfull($filedata, $data, $cmpid);
		if (!file_exists($zspath) && !mkdir($zspath, 0777, true)) {
			$jsondata = array('msg'=>'ID：'.$cmpid.'生成目录失败，请联系管理员！', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		} else if (!is_writeable($zspath)) {
			$jsondata = array('msg'=>'ID：'.$cmpid.'生成目录失败，请联系管理员！', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}
		
		$ftpsrcpath = $dir.'img/';
		$zssrcpath = $zspath.'/img/';
		if(is_dir($ftpsrcpath)){
			$this->recurse_copy($ftpsrcpath, $zssrcpath);
		}
		$zs_file_path = $zspath."/".$file;
		if(is_file($zs_file_path) ){
			unlink($zs_file_path);
		}
		$fp = fopen($zs_file_path, "w+");
		fwrite($fp, $filedata);
	}
	
	private function checkIscontact($math_data, $contact_path, $cmpid){
		preg_match("/<!--{CONTACT}-->(.*?)<!--{\/CONTACT}-->/is", $math_data, $contact);
		if($contact[1]){
			$mdl_cmp_company = new Model_cmp_company();
			$data = $mdl_cmp_company->getRowById($cmpid);
			if($data['cmp_type'] == 6 && $data['end_time']>=NOW){
				$filedata = $contact[1];
			}else{
				$filedata = '';
			}
			if (!file_exists($contact_path) && !mkdir($contact_path, 0777, true)) {
				$jsondata = array('msg'=>'ID：'.$cmpid.'生成目录失败，请联系管理员！', 'status'=>false);
				echo SUtil::getJson($jsondata);
				exit();
			} else if (!is_writeable($contact_path)) {
				$jsondata = array('msg'=>'ID：'.$cmpid.'生成目录失败，请联系管理员！', 'status'=>false);
				echo SUtil::getJson($jsondata);
				exit();
			}
			if( is_file($contact_path."contact.shtml" ) ){
				unlink($contact_path."contact.shtml");
			}
			$fp = fopen($contact_path."contact.shtml", "w+");
			fwrite($fp, $filedata);
			if($filedata){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}
	
	private function checkIsfull($math_data, $cmpid, $filepath){
		//检测必要标签
		preg_match("/<!--{TITLE}-->(.*?)<!--{\/TITLE}-->/is", $math_data, $title);
		preg_match("/<!--{KEYWORDS}-->(.*?)<!--{\/KEYWORDS}-->/is", $math_data, $keywords);
		preg_match("/<!--{DESCRIPTION}-->(.*?)<!--{\/DESCRIPTION}-->/is", $math_data, $description);
		preg_match("/<!--{HEADER_960}-->(.*?)<!--{\/HEADER_960}-->/is", $math_data, $header_960);
		preg_match("/<!--{HEADER_990}-->(.*?)<!--{\/HEADER_990}-->/is", $math_data, $header_990);
		preg_match("/<!--{GUESTBOOK}-->(.*?)<!--{\/GUESTBOOK}-->/is", $math_data, $guetbook);
		preg_match("/<!--{CONTACT}-->(.*?)<!--{\/CONTACT}-->/is", $math_data, $contact);
		preg_match("/<!--{FOOTER}-->(.*?)<!--{\/FOOTER}-->/is", $math_data, $footer);
		$filename = pathinfo($filepath);
		if(empty($title[0]) && $filename['basename'] == 'index.shtml'){
			$jsondata = array('msg'=>'ID：'.$cmpid.$filepath.'文件，TITLE标签不存在，请检查', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}else if(empty($keywords[0]) && $filename['basename'] == 'index.shtml'){
			$jsondata = array('msg'=>'ID：'.$cmpid.$filepath.'文件，KEYWORDS标签不存在，请检查', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}else if(empty($description[0]) && $filename['basename'] == 'index.shtml'){
			$jsondata = array('msg'=>'ID：'.$cmpid.$filepath.'文件，DESCRIPTION标签不存在，请检查', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}else if(empty($header_960[0]) && empty($header_990[0])){
			$jsondata = array('msg'=>'ID：'.$cmpid.$filepath.'文件，HEADER标签不存在，请检查', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}else if(empty($guetbook[0])){
			$jsondata = array('msg'=>'ID：'.$cmpid.$filepath.'文件，GUESTBOOK标签不存在，请检查', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}else if(empty($contact[0])){
			$jsondata = array('msg'=>'ID：'.$cmpid.$filepath.'文件，CONTACT标签不存在，请检查', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}else if(empty($footer[0])){
			$jsondata = array('msg'=>'ID：'.$cmpid.$filepath.'文件，FOOTER标签不存在，请检查', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}
	}
	
	private function regIsfull($filedata, $data, $cmpid){
		//title替换正则
		$reg_title = '/<!--{TITLE}-->.+<!--{\/TITLE}-->/is'; 
		$data_title = "<title>{$data['title']}</title>";
		$filedata = preg_replace($reg_title, $data_title, $filedata);
		
		//keywords替换正则
		$reg_keywords = '/<!--{KEYWORDS}-->.+<!--{\/KEYWORDS}-->/is'; 
		$data_keywords = '<meta name="keywords" content="'.$data['keywords'].'"/>';
		$filedata = preg_replace($reg_keywords, $data_keywords, $filedata);
		
		//Description替换正则
		$reg_description = '/<!--{DESCRIPTION}-->.+<!--{\/DESCRIPTION}-->/is'; 
		$data_description = '<meta name="description" content="'.$data['description'].'"/>';
		$filedata = preg_replace($reg_description, $data_description, $filedata);
		
        if($data['page_url'])
        {
            $zhaoshang_html='<li><a href="'.WWW_URL.'/zhaoshang/'.$data['page_url'].'/" target="_blank">招商主页</a></li>';
        }
		$headerhtml = '<div class="zhaoshang_header">
			<div class="company_nav">
				<div class="zhaoshang_company">
					<i class="ico_001"></i>
					'.SString::tcutstr($data['cmp_name'], 34).'
					<i></i>
				</div>
                          
				<ul	class="zs_comp_nav">
                    '.$zhaoshang_html.'
					<li><a href="'.WWW_URL.'/company/'.$cmpid.'/" target="_blank">公司介绍</a></li>
					<li><a href="'.WWW_URL.'/company/product-id-'.$cmpid.'" target="_blank">产品展示</a></li>
					<li><a href="'.WWW_URL.'/company/zixun-id-'.$cmpid.'" target="_blank">企业资讯</a></li>
				</ul>
				<a href="#tag_1" class="i_agent">我要代理</a>
			</div>
		</div>';
		//HEADER替换正则
		$reg_header = '/<!--{HEADER_960}-->.+<!--{\/HEADER_960}-->/is'; 
		$data_header = '<!--#include virtual="'.HEADER_URL.'_960"-->'.$headerhtml;
		
		$filedata = preg_replace($reg_header, $data_header, $filedata);
		
		//HEADER替换正则
		$reg_header = '/<!--{HEADER_990}-->.+<!--{\/HEADER_990}-->/is'; 
		$data_header = '<!--#include virtual="'.HEADER_URL.'"-->'.$headerhtml;
		$filedata = preg_replace($reg_header, $data_header, $filedata);
		
		//CONTACT替换正则
		$reg_contact = '/<!--{CONTACT}-->.+<!--{\/CONTACT}-->/is';
		$data_contact = '<!--#include virtual="img/contact.shtml"-->';
		$filedata = preg_replace($reg_contact, $data_contact, $filedata);
		$zspath = ZSTPl_DIR.'/'.$data['customurl'].'/';
		$contact_path = $zspath.'/img/';
		$contactdata = $this->checkIscontact($filedata, $contact_path, $cmpid);
		
		
		//OTHERCODE替换正则
		$reg_othercode = '/<!--{OTHERCODE}-->(.+)<!--{\/OTHERCODE}-->/is';
		$data_othercode = "$1";
		$filedata = preg_replace($reg_othercode, $data_othercode, $filedata);
		
		//FOOTER替换正则
		$reg_footer = '/<!--{FOOTER}-->.+<!--{\/FOOTER}-->/is';
		$data_footer = '<!--#include virtual="'.FOOTER_URL.'"-->';
		$filedata = preg_replace($reg_footer, $data_footer, $filedata);
		
		//GUESTBOOK替换正则
		$reg_guestbook = '/<!--{GUESTBOOK}-->.+<!--{\/GUESTBOOK}-->/is'; 
		$data_guestbook = "<script>var Cmp_id=\"{$cmpid}\", c_webid = '1401', modtype = \"1\", modid = \"{$cmpid}\", referrer = document.referrer;</script>";
		$data_guestbook .= '<script src="'.GUESTBOOK_JS.'" type="text/javascript"></script>';
		$filedata = preg_replace($reg_guestbook, $data_guestbook, $filedata);
		return $filedata;
	}
	
	private function recurse_copy($src,$dst) {  // 原目录，复制到的目
		$dir = opendir($src);
		if (!file_exists($dst) && !mkdir($dst, 0777, true)) {
			$jsondata = array('msg'=>'ID：'.$cmpid.'生成目录失败，请联系管理员！', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		} else if (!is_writeable($dst)) {
			$jsondata = array('msg'=>'ID：'.$cmpid.'生成目录失败，请联系管理员！', 'status'=>false);
			echo SUtil::getJson($jsondata);
			exit();
		}
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}
	
	public function ischecklv($lv){
		switch ($lv) {
			case 1:
				$lvs = '普通厂商';
				break;
			case 2:
				$lvs = '铜牌厂商';
				break;
			case 3:
				$lvs = '银牌厂商';
				break;
			case 4:
				$lvs = '金牌厂商';
				break;
			case 5:
				$lvs = '钻石厂商';
				break;
		}
		return $lvs;
	}
}
