<?php
/**
* 得到地区列表
*/

class Model_com_area extends Base_model
{        
    var $count=0;
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'xx';
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'xx';
        
    }
    //@override
       public function setCache($status=false){
        $this->_cache = $status;
    }    
    
    
    public $area_doc_dom=null;
    
    public function getAllRowset($codeid=false,$child=false)
    {
        /*
        $cacheObj=cache_cmp_read('company_rela');
        if(!$cacheObj){
            $db=get_cmpdb();
            $dataRow=$db->exe_select_rowset(TRADE_TABLE,"`tid`,`tname`",'','','','tid');
            cache_cmp_write('company_rela',$dataRow);
            return $dataRow;
        }
        else
            return $cacheObj;
        */
        $xmlpath=FRAME_DIR.'/Library/Model/com/area.xml'; 
        $r=array();
        
        //根据id查询
        if($codeid)
        {
            if($codeid{0}!='A')
            {
                $codeid='A'.$codeid;
            }
            
            //得到该值$codeid
            if($this->area_doc_dom)
            {
                //已经加载过了。直接使用
                $doc = $this->area_doc_dom;
            }
            else
            {
                //重新加载
                $xmlstr = file_get_contents($xmlpath);
                $doc = new DOMDocument();
                $doc->preserveWhiteSpace = false;
                
                $doc->load($xmlpath);   
                $this->area_doc_dom = $doc;
            }
            
            //没有得到对象,退出
            if(!$e=$doc->getElementById($codeid)){return false;}
            
            $r['codeid'] = $e->getAttribute('id');
            $r['name'] = $e->getAttribute('v');
            
            if($child && $e->childNodes->length)
            {
                //要求得到一级子集且一级子集有值
                $r['child'] = array();
                foreach($e->childNodes as $node)
                {
                    $temp = array('codeid'=>$node->getAttribute('id'),'name'=>$node->getAttribute('v'));
                    array_push($r['child'],$temp);
                }            
            }
        }
        else
        {
            // 得到当前所有父级
            $doc = simplexml_load_file($xmlpath);
            
            foreach($doc->a as $node)
            {
                $attrs = $node->attributes();
                $temp = array('codeid'=>strval($attrs['id']),'name'=>strval($attrs['v']));
                
                if($child && $node->b)
                {
                    $temp['child'] = array();
                    foreach($node->b as $node2)
                    {
                        $attrs=$node2->attributes();
                        $temp2=array('codeid'=>strval($attrs['id']),'name'=>strval($attrs['v']));
                        if($node2->c)
                        {
                            $temp2['child'] = array();
                            foreach($node2->c as $node3)
                            {
                                $attrs=$node3->attributes();
                                $temp3=array('codeid'=>strval($attrs['id']),'name'=>strval($attrs['v']));
                                array_push($temp2['child'],$temp3);
                            }
                        }
                        array_push($temp['child'],$temp2);
                    }
                }
                array_push($r,$temp);
            }
        }
        
         return $r; 
    }      
        
    /*****************************地区代码area表***********************************/

    /**
    * 得到地区列表
    * 一个父级得到直属下级
    */
    function getRowsetByCodeId($codeid)
    {
        if($codeid)
        {
            $r = array();
            $area_row = $this->getAllRowset($codeid,true);
            if($area_row)
            {
                array_push($r,array('codeid'=>$area_row['codeid'],'name'=>$area_row['name']));
                if($area_row['child'])
                {
                    $r = array_merge($r,$area_row['child']);
                }
                return $r;
            }
        }
        return false;
    }

    /**
    * 得到地区路径
    * 
    * @param int $codeid
    * @param bool $rstring 返回字符串
    * @return string
    */
    function getPathByCodeId($codeid,$rstring=true)
    {
       return $this->getPathRowsetByCodeId($codeid,$rstring); 
    }
    
    /**
    * 得到地区名称，根据地区编码
    * 
    * @param string $codeid
    * @return String
    */
    function getNameByCodeId($codeid)
    {
        if(!$codeid=trim($codeid)){return false;}
        $row = $this->getAllRowset($codeid,false); 
        return $row['name'];
    }
    
    
    function getParentNameByCodeId($codeid)
    {
        if(!$codeid=trim($codeid)){return false;}
        $a = $this->getSplitedCodeIds($codeid);
        $p_codeid=$a[0].'0000';
        return $this->getNameByCodeId($p_codeid);
    }
    
    function clearAreaNamePostfix($name)
    {
        $name=str_replace(' ','',$name);
        $name_bak=$name;
        $name=str_replace(array('省','市','区','县','县级','自治区','自治县','自治州','辖','市辖','县辖','自治'),'',$name);
        if(strlen($name) < 4) //替换得只有一个字了
        {
            $name=$name_bak;
        }
        if($name=='市辖区' || $name=='请选择')
        {
            return '';
        }
        return $name;
    }
    

    /**
    * 得到地区列表
    * 子级得到父级
    * @param int $codeid 空为全部第一级
    */
    function getParentRowsetsByCodeId($codeid=false) 
    {           
        $area_row = $this->getAllRowset(false,true);
        if($codeid{0}!='A')
        {
            $codeid='A'.$codeid;
        }
        $r = array();
        if($area_row)
        {
            $r[0] = array();
            foreach($area_row as $v1)
            {
                array_push($r[0],array('codeid'=>$v1['codeid'],'name'=>$v1['name']));
                if(substr($v1['codeid'],0,3)==substr($codeid,0,3))
                {
                    //同属于一个一级父级
                    $r[1] = array();
                    array_push($r[1],array('codeid'=>$v1['codeid'],'name'=>$v1['name']));
                    foreach($v1['child'] as $v2)
                    {
                        array_push($r[1],array('codeid'=>$v2['codeid'],'name'=>$v2['name']));
                        if(substr($v2['codeid'],0,5)==substr($codeid,0,5))
                        {
                            //在第二组相同
                            $r[2] = array();
                            array_push($r[2],array('codeid'=>$v2['codeid'],'name'=>$v2['name']));
                            foreach($v2['child'] as $v3)
                            {
                                array_push($r[2],array('codeid'=>$v3['codeid'],'name'=>$v3['name']));
                            }
                        }
                    }
                    if(!$r[2])     
                    {
                        $r[2] = array();
                        array_push($r[2],array('codeid'=>$v1['codeid'],'name'=>$v1['name']));
                    }
                }
            }   
        }
        return $r;
    }
    
    /**
    * 根据地区编码得到全地址
    * @param bool $rstring 返回数组还是字符串
    * @param mixed $codeid
    */
    function getPathRowsetByCodeId($codeid,$rstring=false) 
    {           
        $codeids = $this->split_append_limit($codeid,2);
        $rowset = array();
        $lastcodeid = '0';
        foreach($codeids as $codeid)
        {
            $codeid = $this->fill_zero($codeid,'00',6);
            if($codeid==$lastcodeid)
            {
                continue;
            }
            $row = $this->getAllRowset($codeid,false);
            $rowset[] = array('codeid'=>$row['codeid'],'name'=>$row['name']);
            $lastcodeid = $codeid;
        }
        if($rstring)
        {
            $citystr = '';
            if($rowset)
            {
                foreach($rowset as $v)
                {
                    if($v['name']) $citystr .= '-'.$v['name'];
                }
            }
            return trim($citystr,'-');
        }
        return $rowset;
    }
    
    /**
    * 把地区编码分割成3个子编码(省,市,县)
    * 
    * @param string $codeid
    * @return array
    */
    function getSplitedCodeIds($codeid)
    {
        if(!$codeid){return false;}
        $codeid=str_replace('A','',$codeid);
        return $this->split_limit($codeid,2);  
    }
    
    function getLikeStrByCodeId($codeid,$stub="?")
    {
        if(!$codeid){return false;}
        $a = $this->getSplitedCodeIds($codeid);
        foreach ($a as $k=>$v)
        {
            $str_code.=str_replace('00',$stub.$stub,$v);
        }
        return $str_code;
    }
    
    
    /**
    * 按个数分割
    * 
    * @param mixed $str
    * @param mixed $limit
    */
    private function split_limit($str,$limit)
    {
        $result=array();
        $start=0;
        $str_len=strlen($str); //字符串长度
        while($start <= $str_len-2)
        {
            $splited_str=substr($str,$start,$limit);
            array_push($result,$splited_str);
            $start+=$limit;
        }
        return $result;
    }

    /**
    * 按个数追加分割
    * 
    * @param mixed $str
    * @param mixed $limit
    */
    private function split_append_limit($str,$limit)
    {
        $result=array();
        $start=0;
        $str_len=strlen($str); //字符串长度
        while($start <= $str_len-2)
        {
            $splited_str=substr($str,0,$start+$limit);
            array_push($result,$splited_str);
            $start+=$limit;
        }
        return $result;
    }

    /**
    * 填充零
    * 
    * @param mixed $str
    * @param mixed $fillstr
    * @param mixed $len
    */
    private function fill_zero($str,$fillstr,$len)
    {
        $filestrlen=strlen($fillstr);
        for($i=strlen($str);$i<$len;$i+=$filestrlen)
        {
            $str.=$fillstr;
        }
        return $str;
    }
    
    
    
    
    
    
    
    /*
    function getAll($list)
    {
        foreach ($list as $v) 
        {
            if($v['child'])
            {
               $this->getAll($v['child']);  
            }
            $this->count++;
            //echo  $v['codeid']."\t".$v['name'];
            
            $this->getUpdateSql($v);
        }
    }
    */
    
    
    function getUpdateSql2($v)
    {
        //去掉一级的
        if(strpos($v['codeid'],'0000'))
        {
            return ;
        }
        
        $code=str_replace('A','',$v['codeid']);
         //$v_1['codeid'],":",$v_1['name']
        $a = $this->getSplitedCodeIds($code); 
        $name=$this->clearAreaNamePostfix($v['name']);
        
        $name_p=$this->getNameByCodeId($a[0].'0000');
        $name_p=$this->clearAreaNamePostfix($name_p);
        if($name)
        {
            echo "UPDATE cmt_comments set areas_code='{$code}',areas_code1='{$a[0]}' where cmt_id<10909530 AND province LIKE '%{$name_p}%'  AND areas LIKE '%{$name}%';\r\n";
        }
    }
    
    function getUpdateSql1($v)
    {
        $code=str_replace('A','',$v['codeid']);
         //$v_1['codeid'],":",$v_1['name']
        $a = $this->getSplitedCodeIds($code); 
        $name=$this->clearAreaNamePostfix($v['name']);

        if($name)
        {
            echo "UPDATE cmt_comments set areas_code='{$code}',areas_code1='{$a[0]}' where cmt_id<10909530 AND province LIKE '%{$name}%';\r\n";
        }
    }
    
    function buildAreaSql($list_1,$type=1)
    {
       
        //$a=$mdl_area->getSplitedCodeIds('201010');
        //print_r($a);
        
        //$list_1=$mdl_area->getAllRowset($id,true);


        foreach ($list_1 as $v_1)
        {
            if($type==1)
            {
                $this->getUpdateSql1($v_1);   
            }
            else
            {
               $this->getUpdateSql2($v_1); 
            }
            if($v_1['child'])
            {
                // echo  $v_1['codeid'],":",$v_1['name'],'<br/>'; 
                
                 $this->buildAreaSql($v_1['child'],$type);
            }
            
        }
    }
}
    

