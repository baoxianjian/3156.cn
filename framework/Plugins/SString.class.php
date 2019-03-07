<?php
/**
* @name: 字符串函数 
* @author: baoxianjian
* @date: 9:00 2015/4/14
*/
class SString{ 
    //不去html标记 去空格 准确
    public static function dcutstr($string , $length , $suffix=false , $charset="utf-8" , $start=0 , $dot = '...')
    {
        if(!$string){return '';}
        if(!$length=intval($length)){return $string;}
        $str=str_replace(array(' ','&nbsp;',"\r\n",'\n','<br/>'),array('','','',"",''),$string);
        $str=html_entity_decode($str,ENT_QUOTES,$charset);// update by baoge 2010-12-21 实体转换utf-8编码 去空格
        $str=str_replace(array(' ','&nbsp;'),array('',''),$str);
      
        if(function_exists("mb_substr")){
            $strcut = mb_substr($str, $start, $length, $charset);
            $strcut = htmlspecialchars($strcut);
            if(mb_strlen($str,$charset) > $length){
                return $suffix ? $strcut.$dot : $strcut;
            }else{ 
                return $strcut;
            }
        }else{
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("",array_slice($match[0], $start, $length));
	        $strcut = $slice;
            //$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $slice);
            //$strcut = convert_amp($strcut); 
            return $suffix ? $strcut.$dot : $strcut;
        }    
    }
    
    
    //去html标记 去空格 准确
    public static function tcutstr($string , $length , $suffix=true , $charset="utf-8" , $start=0 , $dot = '...')
    {
        //&lt;p&gt;
        $string=htmlspecialchars_decode($string,ENT_NOQUOTES);
        $string=str_replace('<font color="red">','#HS#',$string);
        $string=str_replace('</font>','#HE#',$string);
        /*$string = preg_replace("/<span.*?>(.*?)<\/span>/", '\1', $string);*/
        /*$string = preg_replace("/<pre.*?>(.*?)<\/pre>/", '\1', $string); */
        $string=self::rip_tags($string);
        //$string=str_replace('<p>','',$string);

        $string= SString::dcutstr($string,$length,$suffix,$charset,$start,$dot);
        $string=str_replace('#HS#','<font color="red">',$string);
        $string=str_replace('#HE#','</font>',$string);
        return $string;
    }
    
    public static function rip_tags($string, $n = true) 
    {
    // ----- remove HTML TAGs -----
    $string = preg_replace ('/<[^>]*>/', ' ', $string);
   
    // ----- remove control characters -----
    $string = str_replace("\r", '', $string);    // --- replace with empty space
	if($n){
    	$string = str_replace("\n", ' ', $string);   // --- replace with space
	}else{
		$string = preg_replace("/([\s]{2,})/","\n",$string);;   // --- replace with space
	}
    $string = str_replace("\t", ' ', $string);   // --- replace with space
   
    // ----- remove multiple spaces -----
    $string = trim(preg_replace('/ {2,}/', ' ', $string));
   
    return $string;

    }
    
    

    function convert_amp($str)
    {
        $str = preg_replace("/\&amp;([a-z]+);/ies",'convert_amp_temp(\\1)',$str);    
        return $str;
    }
    function convert_amp_temp($value)
    {
        return "&{$value};";
    }
    
    static function getFirstChar($s0)
    {
        $firstchar_ord=ord(strtoupper($s0{0}));
        if (($firstchar_ord>=65 and $firstchar_ord<=91)or($firstchar_ord>=48 and $firstchar_ord<=57)) return $s0{0};
        $s=iconv("UTF-8","gb2312", $s0);
        $asc=ord($s{0})*256+ord($s{1})-65536;
        if($asc>=-20319 and $asc<=-20284)return "A";
        if($asc>=-20283 and $asc<=-19776)return "B";
        if($asc>=-19775 and $asc<=-19219)return "C";
        if($asc>=-19218 and $asc<=-18711)return "D";
        if($asc>=-18710 and $asc<=-18527)return "E";
        if($asc>=-18526 and $asc<=-18240)return "F";
        if($asc>=-18239 and $asc<=-17923)return "G";
        if($asc>=-17922 and $asc<=-17418)return "H";
        if($asc>=-17417 and $asc<=-16475)return "J";
        if($asc>=-16474 and $asc<=-16213)return "K";
        if($asc>=-16212 and $asc<=-15641)return "L";
        if($asc>=-15640 and $asc<=-15166)return "M";
        if($asc>=-15165 and $asc<=-14923)return "N";
        if($asc>=-14922 and $asc<=-14915)return "O";
        if($asc>=-14914 and $asc<=-14631)return "P";
        if($asc>=-14630 and $asc<=-14150)return "Q";
        if($asc>=-14149 and $asc<=-14091)return "R";
        if($asc>=-14090 and $asc<=-13319)return "S";
        if($asc>=-13318 and $asc<=-12839)return "T";
        if($asc>=-12838 and $asc<=-12557)return "W";
        if($asc>=-12556 and $asc<=-11848)return "X";
        if($asc>=-11847 and $asc<=-11056)return "Y";
        if($asc>=-11055 and $asc<=-10247)return "Z";
        return null;
    }
    
    //得到一串中文的手字母
    static function getFirstChars($str)
    {
        if(!$str=trim($str)){return false;}
      
        $i=0; 
        $str_out = "";
        while($str_temp = self::dcutstr($str,1,false,'utf-8',$i++))
        {
            $str_out .= self::getFirstChar($str_temp);  
            if($i>100){break;} 
        } 
        echo strtolower($str_out);
        
        return $out_str;
    }

 
    
}


?>
