<?php
/**
 * @package SGeoip
 */
class SGeoip{
	
        /**
          * params mix $strs 网址或ip,type,0二位国家代码CN,1国家简称China
          * return 无内容 null,正常string
          **/
        public static function get($strs,$type='0'){
                if($strs){
			$strs=trim($strs);
			switch($strs){
				case 0://二位国家代码
					$s = geoip_country_code_by_name($strs);//2位国家代码cn
					break;
				case 1://三位国家代码
					$s = geoip_country_code3_by_name($strs);//3位国家代码cha
					break;
				case 2://国家简称
					$s = geoip_country_name_by_name($strs);//国家单词china
					break;
				default:
					$s = geoip_country_code_by_name($strs);//2位国家代码cn
					break;
			}
			return $s;
		}else{
			return null;
		}
	}
}
?>
