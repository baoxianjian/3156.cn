<?php
/**
 * 极验行为式验证安全平台，php 网站主后台包含的库文件
 */

class SGeetest{

	const GT_API_SERVER  = 'http://api.geetest.com';
	const GT_SSL_SERVER  = 'http://api.geetest.com';
	const GT_SDK_VERSION  = 'php_2.15.4.2.2';

	private $captcha_id;
	private $private_key;

	public function __construct() {
		$this->challenge = "";
	}

	public function set_captchaid($captcha_id) {
		$this->captcha_id = $captcha_id;
	}

	public function set_privatekey($private_key) {
		$this->private_key = $private_key;
	}

	public function register() {
		$this->challenge = $this->send_request("/register.php", array("gt"=>$this->captcha_id));
		if (strlen($this->challenge) != 32) {
			return 0;
		}
		return 1;
	}

	public function get_widget($product, $popupbtnid = "", $ssl = FALSE) {
		$params = array(
			"gt" => $this->captcha_id,
			"challenge" => $this->challenge,
			"product" => $product,
		);
		if ($product == "popup") {
			$params["popupbtnid"] = $popupbtnid;
		}
		$server = $ssl ? self::GT_SSL_SERVER : self::GT_API_SERVER;
		return "<script type='text/javascript' src='". $server ."/get.php?". http_build_query($params) ."'></script>";
	}

	public function validate($challenge, $validate, $seccode) {
		if ( ! $this->check_validate($challenge, $validate)) {
			return FALSE;
		}
		$query = http_build_query(array("seccode"=>$seccode, "sdk"=>self::GT_SDK_VERSION));
		$codevalidate = $this->http_post('api.geetest.com', '/validate.php', $query);
		if (strlen($codevalidate) > 0 && $codevalidate == md5($seccode)) {
			return TRUE;
		} else if ($codevalidate == "false"){
			return FALSE;
		} else {
			return $codevalidate;
		}
	}

	private function check_validate($challenge, $validate) {
		if (strlen($validate) != 32) {
			return FALSE;
		}
		if (md5($this->private_key.'geetest'.$challenge) != $validate) {
			return FALSE;
		}
		return TRUE;
	}

	private function send_request($path, $data, $method = "GET") {
		if ($method == "GET") {
			$opts = array(
			    'http'=>array(
				    'method'=>"GET",
				    'timeout'=>2,
			    )
		    );

            $path=self::GT_API_SERVER.$path."?". http_build_query($data);
		    $context = stream_context_create($opts);
			$response = file_get_contents($path, false, $context);
           // var_dump($response);exit;
            /*
            $fp = fopen(self::GT_API_SERVER.$path."?". http_build_query($data), 'r', false, $context);
            fpassthru($fp);
            fclose($fp); 
               */
            if(!$response)
            {
                $response=$this->_http_request($path,5,$context);
            }
           /*
            $path=self::GT_API_SERVER.$path."?". http_build_query($data);
            $response2 = SRemote::getHtmlContent(self::GT_API_SERVER.$path."?". http_build_query($data));
           */ 
			return $response;
		}
	}

	private function http_post($host,$path,$data,$port = 80){
		$http_request = "POST $path HTTP/1.0\r\n";
		$http_request .= "Host: $host\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$http_request .= "Content-Length: " . strlen($data) . "\r\n";
		$http_request .= "\r\n";
		$http_request .= $data;
		$response = '';
		if (($fs = @fsockopen($host, $port, $errno, $errstr, 10)) == false) {
			die ('Could not open socket! ' . $errstr);
		}		
		fwrite($fs, $http_request);
		while (!feof($fs))
			$response .= fgets($fs, 1160);
		fclose($fs);		
		$response = explode("\r\n\r\n", $response, 2);
		return $response[1];
	}
    
    private function _http_request($url,$timeout=2,$header=array()){ 
        if (!function_exists('curl_init')) { 
            throw new Exception('server not install curl'); 
        } 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_HEADER, true); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); 
        if (!$header){ 
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        } 
        $data = curl_exec($ch); 
        list($header, $data) = explode("\r\n\r\n", $data); 
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        if ($http_code == 301 || $http_code == 302) { 
            $matches = array(); 
            preg_match('/Location:(.*?)\n/', $header, $matches); 
            $url = trim(array_pop($matches)); 
            curl_setopt($ch, CURLOPT_URL, $url); 
            curl_setopt($ch, CURLOPT_HEADER, false); 
            $data = curl_exec($ch); 
        } 

        if ($data == false) { 
            curl_close($ch); 
        } 
        @curl_close($ch); 
        return $data; 
}  
 
 //----------直接调用下面的方法   
 
    //得到验证码
    public function get_validation_code()
    {
        $this->set_captchaid("582291c1736501b5976cbd1622e1514e");
        if ($this->register()) {
            $vf_code = $this->get_widget("float");//若采用弹出式，要添加第二个参数（提交按钮的id）
        } else {
            $vf_code = "use your own captcha HTML web code!";//这里输出网站原有验证码
        }
        return $vf_code;
    }
    
    //检查验证结果 1成功 2失败 0不知道
    public function get_validation_result($request)
    {
        $this->set_privatekey("dbec4cb8841d9c94898ca4dfc090e814");

        if (isset($request['geetest_challenge']) && isset($request['geetest_validate']) && isset($request['geetest_seccode'])) 
        {
            $result = $this->validate($request['geetest_challenge'], $request['geetest_validate'], $request['geetest_seccode']);
            if ($result == TRUE)
            {
                return 1;  //成功
            } else if ($result == FALSE) 
            {
                return 2; //失败
            } else {
                return 0;  //不知道
            }

        }
        else
        {
            exit("use your own captcha validate");
            
            //网站原有验证码的验证
            //$result = your_own_captcha_validate();
        }
        return 0;
    }
    
    
    
}
?>