<?php
/**
* @name: 搜索广告 
* @author: baoxianjian
* @date: 10:32 2015/3/20
*/        

class Controller_uploads extends Controller_basepage {

	//上传
	function pageUpload($inPath){

    session_id($this->request['PHPSESSID']);
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
    
    $file_new_name=md5($file_name.time());

// Validate that we won't over-write an existing file
    if (file_exists($save_path . $file_new_name)) {
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

    $dir=date('m',NOW);
    $dir_full=UPLOADS_DIR.'/'.$dir;
    mkdir($dir_full,0755);
    $save_path=$dir_full;
    $file_name_full=$dir_full.'/'.$file_new_name.'.'.$file_extension;

// Process the file
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

    $file_path='/assets/uploads/'.$dir.'/'.$file_new_name.'.'.$file_extension;
    $img_size=getimagesize($file_name_full);
    
    
    $row['up_name']=$path_info['filename'];
    $row['file_name']=$file_name;
    $row['file_ext']=$file_extension;
    $row['file_path']=$file_path;
    $row['img_size']=$img_size[0].'x'.$img_size[1];
    $row['file_size']=$file_size;
    
    $row['user_id']= $_SESSION['uid'];
    $row['mod_id']= $_SESSION['cid'];
    $row['sa_id']=$_SESSION['said'];
    $row['mod_type']=$_SESSION['mt']; //预留 2
    $row['mod_mark']=$_SESSION['mm']; //标记
    
    $row['dateline']=NOW;
    
    $mdl_up=new Model_file_uploads();
    
    $mdl_up->addRow($row);

    
    echo '/assets/uploads/'.$dir.'/'.$file_new_name.'.'.$file_extension;
    exit(0);


        
        
        
        //默认ad/ad_list.html
        #调用相应模版
        //return $this->template($param);
	}
     
    //列表
    function pageList($inPath)
    {
        $mdl_up=new Model_file_uploads();

        $cid=$this->getCmpId(1);
        
        $srow=array('mod_id'=>$cid,'mod_type'=>2);
        if($lis=$this->request['mark'])
        {
           $srow['mod_mark']=$lis;
        }

        $rowset=$mdl_up->getRowsetAll(1,$srow,100);
        
        //预处理
        foreach ($rowset as $k=>$v) 
        {
            $rowset[$k]['dateline']=SUtil::formatTime($rowset[$k]['dateline'],3);
        }
        
        if($this->request['ajax'])
        {
            exit(json_encode($rowset));
           //print_r($rowset);exit;
        }
        
        
    }
    
    function pageDel($inPath)
    {
        $mdl_up=new Model_file_uploads();
        $cid=$this->getCmpId(1);    
        $id=intval($this->request['id']);
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

    /* Handles the error output. This error message will be sent to the uploadSuccess event handler.  The event handler
    will have to check for any error messages and react as needed. */
    function HandleError($message) {
        echo $message;
    } 
    
	
	
}
