<?php
    function get_content($url)
    {
        $buf=parse_url($url);
        if($buf['scheme']=="http")//如果是URL
        {
            $host=$buf['host'];
            $page=$buf['path'];
            if(trim($buf['query'])!=="") $page.="?".trim($buf['query']);

            $myHeader="GET $url HTTP/1.1\r\n";
            $myHeader.="Host: $host\r\n";
            $myHeader.="Connection: close\r\n";
            $myHeader.="Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5\r\n";
            $myHeader.="Accept-Language: zh-cn,zh;q=0.5\r\n";
            $myHeader.="Accept-Charset: gb2312,utf-8;q=0.7,*;q=0.7\r\n";
            $myHeader.="User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.7.6) Gecko/20050226 Firefox/1.0.1 Web-Sniffer/1.0.20\r\n";
            $myHeader.="Referer: http://$host/\r\n\r\n";

            $server=$host;
            $port=$buf['port']?$buf['port']:80;

            $res="";
            if($fp = @fsockopen ($server, $port, $errno, $errstr, 5))
            {
                @fputs ($fp, $myHeader);
                while (!@feof($fp)) $res.= @fgets ($fp, 1024);
                @fclose ($fp);
            }
            else
            {
                return false;
            }

            if(strlen($res)==0) return false;

            return $res;
        }
        else//如果是本地文件
        {
            $fileName=$url;
            if(false!==@file_exists($fileName))
            {
                if(false!==($buf=@implode("",file($fileName)))&&@strlen($buf)>0) 
                {

                    return $buf;
                }
                else return false;
            }
            else return false;
        }
    }
    
    function get_html_content($url)
    {
        //$content= get_content('http://news.tcsos.com/somelocalnews.php'); 
        $content= get_content($url); 
        $start=strpos($content,"\r\n\r\n");
        if($start)
        {
            $start=$start+strlen("\r\n\r\n");
        }
        $content=substr($content,$start);
        
        $start=strpos($content,"\r\n");
        if($start)
        {
            $start=$start+strlen("\r\n");
            $content=substr($content,$start);
        }
        //解决nginx的最后一个0的问题
        $end=strrpos($content,"0\r\n");
        if($end)
        {
            $content=substr($content,0,$end);
        }
        
        return $content;
    }
    
    function get_real_ip(){
    $ip=false;    
    if(!empty($_SERVER["HTTP_CLIENT_IP"])){
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
        if ($ip) { array_unshift($ips, $ip); $ip = FALSE; }
        for ($i = 0; $i < count($ips); $i++) {
            if (!eregi ("^(10|172\.16|192\.168)\.", $ips[$i])) {
                $ip = $ips[$i];
                break;
            }
        }
    }
    return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}


    $back="http://console.3156.test/back.php";
    $url = "http://s.3156.test/check.php?back={$back}";
    
    
    
    

header("Location:$url");

//print_r($_SERVER);
?>
