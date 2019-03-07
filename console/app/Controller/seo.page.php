<?php
header("content-type:text/html;charset=utf-8");
class Controller_seo extends Controller_basepage {
    var $baidukey = "lQuvZqzqMo7zsfRl"; //3156在百度站长平台的更新密钥
	//提交百度收录网址页面
	function pageBaidu($inPath){
        #define("DEBUG",1);
        
        return  $this->template();
        //return $this->render('tpl1/article/article_baidu.html',$param);
	}
	
    //提交发送
    function pageBaidusubmit($inPath){
        #define("DEBUG",1);
        $urls=$this->request['urls'];
        $url_array = explode("\r\n", $urls);  //按行分隔          
        while (list($key, $value) = each($url_array)){
            $url_array_new[$key]['url'] = trim($value);//去掉空格
            $temparray = parse_url(trim($value));
            $url_array_new[$key]['host'] = $temparray['host'];
        }
        $result =   array();
        foreach($url_array_new as $k=>$v){
            $result[$v['host']]['urls'][]=$v['url'];//按域名对网址进行分组
        }           
        //var_dump($result);
        foreach($result as $k=>$v)
        {
            $msg = $this->post_to_baidu($k,$v);   //提交
            //$msg=json_decode($msg);
            var_dump($msg);
            if ($msg)
            {
                //echo "<br>成功推送的url条数：".$msg['success'];
            }
        }
    }
    
    //提交到百度的方法
    /*
    $host  网址的域名
    $urls  网址的数组
    */
    function post_to_baidu($host,$urls)
    {
        /*
          $urls = array(
            'http://www.example.com/1.html',
            'http://www.example.com/2.html',
          );
        */  
        $api = 'http://data.zz.baidu.com/urls?site='.$host.'&token='.$this->baidukey;
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urls),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;
    }



}
