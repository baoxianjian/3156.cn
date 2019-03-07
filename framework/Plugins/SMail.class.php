<?php

/**
 * SMTP发送邮箱类
 */

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."mail/class.phpmailer.php");
/**
 * @package SlightPHP
 */
class SMail extends PHPMailer{
	private $mail;
	private $errMSG;
	
	public function SMail($type='test2'){

		$this->useConfig($type);
	}
	
	private static $_config;
	
	static function setConfigFile($file){
		self::$_config = $file;
	}
	static function getConfig($zone=null){
		return SConfig::getConfig(self::$_config,$zone);
	}
	public function useConfig($zone){
		$hosts=array();
		$cfg = self::getConfig($zone);
		
		//die(var_dump($cfg));
		
		if(empty($cfg))$cfg=self::getConfig("default");
		if(!is_array($cfg)){
			$tmp = $cfg;
			unset($cfg);
			$cfg[] = $tmp;
		}
		
	//	die(var_dump($cfg));
		
		if(!empty($cfg)){
			foreach($cfg as $host){
				break;
			}
		}
		$this->mail	= new PHPMailer();
		//die(var_dump($host->host));
		$this->mail->IsSMTP();						// telling the class to use SMTP
		$this->mail->Host       = $host->host;	// SMTP server
		$this->mail->SMTPAuth   = true;				// enable SMTP authentication
		$this->mail->Port       = $host->port;				// set the SMTP port for the GMAIL server
		$this->mail->Username   = $host->user;	// SMTP account username
		$this->mail->Password   = $host->pass;		// SMTP account password
		$this->mail->From		= $host->from;
		$this->mail->FromName   = $host->fromname;
		$this->mail->CharSet = "UTF-8";
		//$this->mail->SMTPDebug  = 2;				// enables SMTP debug information (for testing)
													// 1 = errors and messages
													// 2 = messages only
		//$this->mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	}
	
	/**
		地址 标题 内容 username
	*/
	public function sendMail($email,$title,$cont,$username=''){
		//标题
		$this->mail->Subject = $title ? $title:"3156.test";		

		//null
		$this->mail->MsgHTML($cont);
		
		//null
		$this->mail->AddAddress($email, $username);		
		
		//$this->mail->AddAttachment("images/phpmailer.gif");      // attachment
		//$this->mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
		//成功发送这时屏幕右下角会弹出窗口
		if(!$this->mail->Send()) {
			$this->errMSG = "Error: " . $this->mail->ErrorInfo;
			return false;
		} else {
			return true;
		}
	}
	
	public function getErrMsg(){
		return $this->errMSG;
	}
}