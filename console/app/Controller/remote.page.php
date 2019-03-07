<?php
/**
* @name: 远程操作 
* @author: baoxianjian
* @date: 15:18 2015/3/30
*/
class Controller_remote extends Controller_basepage {
	//数据同步
	function pageDatasyn($inPath){

        print_r($this->request);

        $name=$this->request['n'];
        $type=$this->request['t'];
        $clean_str="&clean=true";
        if($type=='delta')
        {
            $clean_str="";
        }
        
        $url=SEARCH_URL."/{$name}/dataimport?command={$type}-import&optimize=true".$clean_str;

       // $this->showMessage('请求已发送，请等2分钟后到前台收缩页看效果！');

        $result=$this->get_content($url);
        echo $url,'<br/>',$result;

       return $this->template();
	}
    
    
    function pageDetect()
    {

        $gtpid=intval($_GET['id'])*1000000;
          ///echo $this->get_content("http://www.3156.test/product/200711/44743.shtml");
        //$ltpid=  (intval($_GET['id'])+1)*1000000;
          
          
           ini_set('memory_limit','1024M');
           ini_set('max_execution_time','86400');
        $mdl_pdt=new Model_pdt_products();
        
        //public function getList($condition, $item='*', $order = '', $page = 1, $limit = 10, $iscount=true, $leftjoin="") { 
         
        $condition="static_url_valid=0 AND is_del!=2";
   
        $data=$mdl_pdt->getList($condition,'pdt_id,static_url,is_del','',0,1,true);
        $count=$data['count'];

        for($i=0;$i<$count;$i++){
           $data=$mdl_pdt->getList($condition,'pdt_id,static_url,is_del','',$i,1,false);
           $row=$data['list'][0];
           $html_str=$this->get_content("http://www.3156.test/{$row['static_url']}");
           if(strpos($html_str,"<div id=\"cpbody\">")>0)
           {
               //有效
               $row_temp['static_url_valid']=1;
               $mdl_pdt->updateRowById($row_temp,$row['pdt_id']);
           }
           else
           {
               //无效
               $row_temp['static_url_valid']=2;
               $mdl_pdt->updateRowById($row_temp,$row['pdt_id']);
           }
        }
        exit('执行完毕');

    }
    
    
    
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
}