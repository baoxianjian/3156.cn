<?php
/**
* @name: 搜索广告 
* @author: baoxianjian
* @date: 10:32 2015/3/20
*/        

class Controller_uploads extends Controller_basepage {

	//上传
	function pageUpload($inPath){

    session_id($this->request['sid']);
    session_start();

// Check post_max_size (http://us3.php.net/manual/en/features.file-upload.php#73762)
    $POST_MAX_SIZE = ini_get('post_max_size');
    $unit = strtoupper(substr($POST_MAX_SIZE, -1));
    $multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

    if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
        header("HTTP/1.1 500 Internal Server Error"); // This will trigger an uploadError event in SWFUpload
        echo "POST exceeded maximum allowed size.";
        exit(0);
    }

// Settings
    //$save_path = getcwd() . "/uploads/";                // The path were we will save the file (getcwd() may not be reliable and should be tested in your environment)
   $save_path=UPLOADS_DIR.'/';
    $upload_name = "Filedata";
    $max_file_size_in_bytes = 2147483647;                // 2GB in bytes
    $extension_whitelist = array("jpg", "gif", "png");    // Allowed file extensions
    $valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';                // Characters allowed in the file name (in a Regular Expression format)
    
// Other variables    
    $MAX_FILENAME_LENGTH = 260;
    $file_name = "";
    $file_extension = "";
    $uploadErrors = array(
        0=>"There is no error, the file uploaded with success",
        1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
        2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
        3=>"The uploaded file was only partially uploaded",
        4=>"No file was uploaded",
        6=>"Missing a temporary folder"
    );


// Validate the upload
    if (!isset($_FILES[$upload_name])) {
        $this->HandleError("No upload found in \$_FILES for " . $upload_name);
        exit(0);
    } else if (isset($_FILES[$upload_name]["error"]) && $_FILES[$upload_name]["error"] != 0) {
        $this->HandleError($uploadErrors[$_FILES[$upload_name]["error"]]);
        exit(0);
    } else if (!isset($_FILES[$upload_name]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
        $this->HandleError("Upload failed is_uploaded_file test.");
        exit(0);
    } else if (!isset($_FILES[$upload_name]['name'])) {
        $this->HandleError("File has no name.");
        exit(0);
    }
    
// Validate the file size (Warning: the largest files supported by this code is 2GB)
    $file_size = @filesize($_FILES[$upload_name]["tmp_name"]);
    if (!$file_size || $file_size > $max_file_size_in_bytes) {
        $this->HandleError("File exceeds the maximum allowed size");
        exit(0);
    }
    
    if ($file_size <= 0) {
        $this->HandleError("File size outside allowed lower bound");
        exit(0);
    }


// Validate file name (for our purposes we'll just remove invalid characters)
    $file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES[$upload_name]['name']));
    if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
        $this->HandleError("Invalid file name");
        exit(0);
    } 
    
    $file_name=md5($file_name.time());

// Validate that we won't over-write an existing file
    if (file_exists($save_path . $file_name)) {
        $this->HandleError("File with this name already exists");
        exit(0);
    }

// Validate file extension
    $path_info = pathinfo($_FILES[$upload_name]['name']);
    $file_extension = $path_info["extension"];
    $is_valid_extension = false;
    foreach ($extension_whitelist as $extension) {
        if (strcasecmp($file_extension, $extension) == 0) {
            $is_valid_extension = true;
            break;
        }
    }
    if (!$is_valid_extension) {
        $this->HandleError("Invalid file extension");
        exit(0);
    }

// Validate file contents (extension and mime-type can't be trusted)
    /*
        Validating the file contents is OS and web server configuration dependant.  Also, it may not be reliable.
        See the comments on this page: http://us2.php.net/fileinfo
        
        Also see http://72.14.253.104/search?q=cache:3YGZfcnKDrYJ:www.scanit.be/uploads/php-file-upload.pdf+php+file+command&hl=en&ct=clnk&cd=8&gl=us&client=firefox-a
         which describes how a PHP script can be embedded within a GIF image file.
        
        Therefore, no sample code will be provided here.  Research the issue, decide how much security is
         needed, and implement a solution that meets the needs.
    */
	
	
	$file_fulls =  $file_path = $this->getFullName($file_name, $file_extension);
	$file_name_full = $file_fulls['url'];
	/*
        At this point we are ready to process the valid file. This sample code shows how to save the file. Other tasks
         could be done such as creating an entry in a database or generating a thumbnail.
         
        Depending on your server OS and needs you may need to set the Security Permissions on the file after it has
        been saved.
    */
    if (!@move_uploaded_file($_FILES[$upload_name]["tmp_name"], $file_name_full)) {
        $this->HandleError("File could not be saved:".$file_name_full);
        exit(0);
    }

    $file_path = $file_fulls['saveurl'];
    $img_size=getimagesize($file_name_full);
    
    $uid=$this->request['uid'] ? $this->request['uid'] : $_SESSION['uid'];
    $said=$this->request['said'] ? $this->request['said'] : $_SESSION['said'];
    $uid=intval($uid);
    $cid=intval($cid);
    
    $mt=intval($this->request['mt']);
    $mid=intval($this->request['mid']);
    $mm=trim($this->request['mm']);
    
    $row['up_name']=$path_info['filename'];
    $row['file_name']=$file_name;
    $row['file_ext']=$file_extension;
    $row['file_path']=$file_path;
    $row['img_size']=$img_size[0].'x'.$img_size[1];
    $row['file_size']=$file_size;
    
    $row['user_id']= $uid;
    $row['sa_id']= $said;
    $row['mod_type']=$mt;
    $row['mod_id']= $mid;
    $row['mod_mark']=$mm;
    
    $row['dateline']=NOW;
    
    $mdl_up=new Model_file_uploads();
    
    $mdl_up->addRow($row);

    
    $jsondata = array('path'=>$file_path, 'aid'=>1);
	echo SUtil::apiOut($jsondata);
	exit();


        
        
        
        //默认ad/ad_list.html
        #调用相应模版
        //return $this->template($param);
	}
    

    /* Handles the error output. This error message will be sent to the uploadSuccess event handler.  The event handler
    will have to check for any error messages and react as needed. */
    function HandleError($message) {
        echo $message;
    }

	/**
     * 重命名文件
     * @return string
     */
    private function getFullName($file_name, $ext, $pathFormat = "../../picture/uploads/{rand:1}/upload/images/{yyyy}{mm}{dd}/{time}{rand:6}"){
		//替换日期事件
		$t = time();
		$d = explode('-', date("Y-y-m-d-H-i-s"));
		$format = $pathFormat;
		$format = str_replace("{yyyy}", $d[0], $format);
		$format = str_replace("{yy}", $d[1], $format);
		$format = str_replace("{mm}", $d[2], $format);
		$format = str_replace("{dd}", $d[3], $format);
		$format = str_replace("{hh}", $d[4], $format);
		$format = str_replace("{ii}", $d[5], $format);
		$format = str_replace("{ss}", $d[6], $format);
		$format = str_replace("{time}", $t, $format);
	
		//过滤文件名的非法自负,并替换文件名
		$oriName = substr($file_name, 0, strrpos($file_name, '.'));
		$oriName = preg_replace("/[\|\?\"\<\>\/\*\\\\]+/", '', $oriName);
		$format = str_replace("{filename}", $oriName, $format);
	
		//替换随机字符串
		$randNum = rand(1, 10000000000) . rand(1, 10000000000);
		if (preg_match("/\{rand\:([\d]*)\}/i", $format, $matches)) {
			$format = preg_replace("/\{rand\:[\d]*\}/i", substr($randNum, 0, $matches[1]), $format);
		}
		$filePath['url'] = $format . $ext;
		$dirname = dirname($filePath['url']);
		//创建目录失败
        if (!file_exists($dirname) && !mkdir($dirname, 0777, true)) {
            return;
        } else if (!is_writeable($dirname)) {
            return;
        }
		$filePath['saveurl'] = preg_replace("/..\/..\/picture\/uploads\/(\d+)\//", "http://img$1.".ROOT_DOMAIN."/", $filePath['url']);
		return $filePath;
	}
	
    
     //列表页
    function pageList($inPath){
        #得到当前页码
        $page=$this->getPageNumber();
        #实例化 搜索广告 数据模型
        $mdl_up = new Model_file_uploads();
        //$testModel->setReaddb('count');
        $mdl_up->setCache(false);

        #收索
        $s_id=$this->request['id'];
        $s_title=$this->request['title'];
        $link_url=$this->request['link_url'];
        $srow=array('id'=>$s_id,'title'=>$s_title,'link_url'=>$link_url);

                        
        #得到搜索广告列表
        $data = $mdl_up->getListAll($page,$srow);

        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($list);$i++){
            $list[$i]['dateline']=SUtil::formatTime($list[$i]['dateline']);
            
            $this->request['id']=$list[$i]['up_id'];
            $list[$i]['del_url']=$url=SRoute::createUrl('ads/del', array('page' => $page,'id'=>$list[$i]['up_id']), $this->request); 
        }
        //unset($this->request['id']);
        
       // print_r($this->request);

        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);

        $param['srow']=$srow;
        
        //默认ad/ad_list.html
        #调用相应模版
        return $this->template($param);
    }
    
    function pageDel($inPath)
    {
        $mdl_up=new Model_file_uploads();
        $cid=$this->getCmpId(1);    
        $id=$this->request['id'];
        $result=$mdl_up->deleteRowById($id);
        if($result)
        {
            $this->showMessage('删除成功',1);
        }
        else
        {
            $this->showMessage('删除失败',3);
        }
    }
	
	function pageArea(){
		$mdl_area = new Model_com_area();
		$area = $mdl_area->getAllRowset();
		foreach($area as $key=>$value){
			$id = str_replace('A', '',$value['codeid']);
			$jsondata[$id]['name'] = $value['name'];
			$jsondata[$id]['codeid'] = $value['codeid'];
			$subdata = $mdl_area->getRowsetByCodeId($value['codeid']);
			foreach($subdata as $k=>$val){
				$sid = str_replace('A', '',$val['codeid']);
				$jsondata[$id]['items'][$sid]['name'] = $val['name'];
				$jsondata[$id]['items'][$sid]['codeid'] = $val['codeid'];
				$_subdata = $mdl_area->getRowsetByCodeId($val['codeid']);
				foreach($_subdata as $kk=>$v){
					$_sid = str_replace('A', '',$v['codeid']);
					$jsondata[$id]['items'][$sid]['items'][$_sid]['name'] = $v['name'];
					$jsondata[$id]['items'][$sid]['items'][$_sid]['codeid'] = $v['codeid'];
				}
			}
		}
		echo SUtil::apiOut($jsondata);
		exit();
	}
    
    
}
