<?php
 /**
* @name: 区块分组的数据模型 
* @author: baoxianjian
* @date: 09:16 2015/4/24
*/
class Model_block_groups extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'bg_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'block_groups'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    
    /**
    * 得到分组列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page,$srow)
    {
        $condition="`is_del`=0";
        if(count($srow)>0)
        {
            if($id=intval($srow['id']))
            {
               $condition.=" AND {$this->_PK}={$id}"; 
            }
            if($gn=trim($srow['gn']))
            {
               $condition.=" AND bg_name LIKE '%{$gn}%'"; 
            }
            if($gc=trim($srow['gc']))
            {
               // $code_str=$this->_buildLikeStr($gc);  
                $condition.=" AND bg_code LIKE '%{$gc}%'"; 
            }
            if($pc=trim($srow['pc']))
            {
                $code_str=$this->_buildLikeStr($pc);
                $condition.=" AND bg_code LIKE '%{$code_str}%' AND bg_code!='{$pc}'";      
            }
        }
        
        $data=$this->getList($condition,"*",array($this->_PK=>'desc'),$page,10,true,$leftjoin);          
 
        return $data;
    }
   
    /**
    * 得到分组数据集（默认返回顶级所有）
    * 
    * @param string $pc 
    * @return mixed
    */
    public function getRowsetAll($srow=array())
    {
        $condition="`is_del`=0";
        if(!$pc=trim($srow['pc'])){$pc='0000';}
        
        $code_str=$this->_buildLikeStr($pc);
        $condition.=" AND bg_code LIKE '%{$code_str}%'";      
    
        
        $data=$this->getList($condition,"*",array($this->_PK=>'desc'),$page,100,true,$leftjoin);
        return $data['list'];
    }
   
   /**
    * 得到分组名称（根据分组id）
    * 
    * @param int $id
    * @return array|false
    */
    public function getNameById($id)
    {
        if(!$id=intval($id)){return false;}
        $row= $this->getOne("{$this->_PK}={$id}",'bg_name');
        return $row['bg_name']; 
    }
    
    /**
    * 得到父级的编码(根据一个编码)
    * 
    * @param string $codeid
    */
    public function getParentCodeId($codeid)
    {
        //010000 父级为000000
        //010100 父级为010000
        //010101 父级为010100
        $list_c=$this->_splitLimit($codeid,2);
       // $list_c= _splitLimit('010100',2);
        $length=count($list_c);
        
        for($i=$length-1; $i>=0 ;$i--)
        {
            if($list_c[$i]!='00')
            {
                $list_c[$i]='00';
                break;
            }
        }                 
        
        foreach ($list_c as $v)
        {
            $code_str.=$v;
        }
        return $code_str;
    } 
    
    private function _buildLikeStr($codeid)
    {
        $list_c=$this->_splitLimit($codeid,2);
        
        #0000 0100 0101（pc里不存在） 000000 000001(code不存在) 010100 010000
        #分组后第一次出现的连续两个00子级 换为__

        foreach ($list_c as $k=>$v)
        {
            if($v=='00')
            {
                $list_c[$k]="__";
                break;
            }
        }
        
        foreach ($list_c as $v)
        {
            $code_str.=$v;
        }
        return $code_str;
    }
    
    /**
    * 生成codeid，根据父级codeid
    * 
    * @param string $pc codeid 
    */
    private function _buildChildCodeId($pc)
    {
        if(!$pc=trim($pc)){$pc='0000';} //默认顶级

        $code_str=$this->_buildLikeStr($pc);
        
        
        $condition="bg_code LIKE '{$code_str}'";
        $rowset=$this->getList($condition,'bg_code',array('bg_code'=>'DESC'),1,1,false);
        
        $code_cur=$rowset['list'][0]['bg_code'];
        if(!$code_cur)
        {
            $code_cur='0000';
        }
        
        #0101 空
        #01__
        $index=strpos($code_str,'__');
        $num_cur=substr($code_cur,$index,2);
        $num_cur++;

        $num_cur=$this->_fillZero($num_cur,2,'left','0');
        
        $code_new=str_replace('__',$num_cur,$code_str);

        return $code_new;
    }
    
    /**
    * 添加一条记录 
    * add by baoxianjian 10:20 2015/4/25  
    * @param array $row
    * @return int|false
    */
    public function addRow($row)
    {
        if(!$row) return false;
        $gb_pc=trim($row['bg_pc']); // 0000 一级
        unset($row['bg_pc']);
        $row['bg_code']=$this->_buildChildCodeId($gb_pc);

        $r=$this->insert($row);
        
        return array($r,$row['bg_code']);
        
    }
    
    /**
    * 修改一条记录（根据Id）
    * add by baoxianjian 10:20 2015/4/25  
    * @param array $row
    * @param int $id
    * @return int|false
    */
    public function updateRowById($row,$id=0)
    {
        //die(SUtil::P($row));
        if(!$row) return false;
        if(!$id=intval($id)){return false;}
        $gb_pc=trim($row['bg_pc']); // 0000 一级
        unset($row['bg_pc']);
        $row['bg_code']=$this->_buildChildCodeId($gb_pc);

        unset($row[$this->_PK]);
        $r=$this->update($this->_PK.'='.$id,$row);
        return array($r,$row['bg_code']);
        //return $this->delete(array($this->_PK=>$id));
    }
    
        
    /**
    * 按个数分割
    * 
    * @param mixed $str
    * @param mixed $limit
    */
     private function _splitLimit($str,$limit)
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
    private function _splitAppend_limit($str,$limit)
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
     private function _fillZero($str,$len,$direction='right',$fillstr='0')
    {
        $filestrlen=strlen($fillstr);
        
        if($direction=='right')
        {
            for($i=strlen($str);$i<$len;$i+=$filestrlen)
            {
                $str.=$fillstr;
            }
        }
        else
        {
            for($i=strlen($str);$i<$len;$i+=$filestrlen)
            {
                $str=$fillstr.$str;
            }   
        }
        return $str;
    }
    

    
    

   
    
    
}