<?php
/**
* @name: 招商广告管理 
* @author: wukai
* @date: 10:32 2015/5/22
*/        

class Controller_zsad extends Controller_basepage {
    
	//列表页
	function pageHeader(){
		if($this->request['_ac']=='save'){
			$code = $this->request['daima'];
			if(empty($code)){$this->showMessage('代码不能为空', 3);exit('代码不能为空');}
			$code = str_replace("gg(\'", '', $code);
			$code = str_replace("\')", '', $code);
			$html = '<ul class="hot_zhangshang">'.gg($code).'</ul>';
			
			$adhtml_path = ZSTPl_DIR."/inc/ad.shtml";
			if (!file_exists($adhtml_path) && !mkdir($adhtml_path, 0777, true)) {
				$this->showMessage('生成目录失败，请联系管理员！', 3);
			} else if (!is_writeable($adhtml_path)) {
				$this->showMessage('文件无法写入，请联系管理员！', 3);
			}
			if(is_file($adhtml_path) ){
				unlink($adhtml_path);
			}
			$fp = fopen($adhtml_path, "w+");
			fwrite($fp, $html);
			$this->showMessage('生成成功');
		}
        return $this->template($param);
	}
	
	function pageFooter(){
		if($this->request['_ac']=='save'){
			$code = $this->request['daima'];
			if(empty($code)){$this->showMessage('代码不能为空', 3);exit('代码不能为空');}
			$code = str_replace("gg(\'", '', $code);
			$code = str_replace("\')", '', $code);
			$html = gg($code);
			
			$adhtml_path = ZSTPl_DIR."/inc/ad_footer.shtml";
			if (!file_exists($adhtml_path) && !mkdir($adhtml_path, 0777, true)) {
				$this->showMessage('生成目录失败，请联系管理员！', 3);
			} else if (!is_writeable($adhtml_path)) {
				$this->showMessage('文件无法写入，请联系管理员！', 3);
			}
			if(is_file($adhtml_path) ){
				unlink($adhtml_path);
			}
			$fp = fopen($adhtml_path, "w+");
			fwrite($fp, $html);
			$this->showMessage('生成成功');
		}
        return $this->template($param);
	}
}
