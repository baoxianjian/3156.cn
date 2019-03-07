<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 20:51 2015/4/20
*/
class Model_block_keydata extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'bkd_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'block_keydata'; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
    
    
    /**
    * 得到广告列表所有
    * 
    * @param mixed $page
    * @return array|false
    */
    public function getListAll($page,$srow)
    {
        $condition="d.`is_del`=0";
        if(count($srow)>0)
        {
            if($id=intval($srow['id']))
            {
               $condition.=" AND d.{$this->_PK}={$id}"; 
            }
            if($bkwn=trim($srow['bkwn']))
            {
                $condition.=" AND k.`bkw_name` LIKE '%{$bkwn}%'";
            }
            
            if($bkwid=intval($srow['bkwid']))
            {
                $condition.=" AND k.`bkw_id`={$bkwid}";
            }
            
            if($title=trim($srow['title'])) //正常
            {                               
                $condition.=" AND d.`title` LIKE '%{$title}%'";
            }

        }
        
     
        $old_tn=$this->_tableName;
        $this->_tableName='block_keydata d';
        $leftjoin="left join block_keywords k ON d.bkw_id=k.bkw_id ";
        //$leftjoin=array('block_keywords k'=>'d.bkw_id=k.bkw_id');
        $data=$this->getList($condition,"k.bkw_name,k.mod_type,k.bkw_type,d.*",array('bkd_id'=>'desc'),$page,20,true,$leftjoin);          
        $this->_tableName=$old_tn;    
        return $data;
    }

    
    
    /**
    * 根据主键id删除一行数据
    * 
    * @param int $id
    * @return int|false
    */
    public function deleteRowById($id,$destroy=false)
    {
        if(!$id=intval($id)) return 0;
        
        return $this->update(array($this->_PK=>$id), array('is_del=1'));
        //return $this->delete(array($this->_PK=>$id));
    }
    

    /**
    * 添加一个用户 
    * 
    * @param array $row
    * @return int|false
    */
    public function addRow($row)
    {
        if(!$row) return false;
        return $this->insert($row);
        //return $this->delete(array($this->_PK=>$id));
    }
    
    /**
    * 修改一条记录（根据Id）
    * 
    * @param array $row
    * @param int $id
    * @return int|false
    */
    public function updateRowById($row,$id=0)
    {
    	//die(SUtil::P($row));
        if(!$row) return false;
        if(!$id=intval($id)){return false;}

        unset($row[$this->_PK]);
        return $this->update($this->_PK.'='.$id,$row);
        //return $this->delete(array($this->_PK=>$id));
    }
    


    /**
    * 得到类型
    * 
    */
    public function getTypes()
    {
        /*
        $a=array(
        '01'=>array(
            'name'=>'药品分类',
            'sub'=>array('0101'=>'中药','0102'=>'OTC','0103'=>'独家','0104'=>'医保','0105'=>'中标','0106'=>'西药','0107'=>'药妆','0108'=>'蒙药','0109'=>'藏药','0110'=>'临床','0111'=>'保健品','0112'=>'医疗器械'),
            ),
        '02'=>array(
            'name'=>'症状分类',
            'sub'=>array('0201'=>'贫血','0202'=>'鼻炎','0203'=>'感冒','0204'=>'腹泻','0205'=>'脚气','0206'=>'肥胖','0207'=>'痔疮','0208'=>'肝炎','0209'=>'肿瘤','0210'=>'便秘','0211'=>'高血压','0212'=>'关节炎','0213'=>'糖尿病','0214'=>'支气管炎')
            ),
        '03'=>array(
            'name'=>'科室分类',
            'sub'=>array('0301'=>'妇科','0302'=>'儿科','0303'=>'眼科','0304'=>'免疫','0305'=>'骨科','0306'=>'泌尿','0307'=>'皮肤','0308'=>'消化科','0309'=>'耳鼻喉科','0310'=>'心脑血管'),
            ),
        '04'=>array(
            'name'=>'药品剂型',
            'sub'=>array('0401'=>'胶囊','0402'=>'冲剂','0403'=>'丸剂','0404'=>'膏剂','0405'=>'喷剂','0406'=>'贴剂','0407'=>'洗液','0408'=>'片剂','0409'=>'注射液','0410'=>'口服液'),
            ),
        );
        */
        $a=array(1=>'药品分类',2=>'症状分类',3=>'科室分类',4=>'药品剂型');
        return $a;    
    }
    
    public function getModTypes()
    {
        $a=array('1'=>'代理商','2'=>'厂商','3'=>'产品');
        return $a;
    }
    

    

        public function getRowsetAll($srow)
        {
            $condition="d.is_del=0 ";
            if($bkwid=intval($srow['kwid']))
            {
                $condition.=" AND k.bkw_id={$bkwid}";
            }
            if($bkwm=trim($srow['kwm']))
            {
                $condition.=" AND k.bkw_mark='{$bkwm}'";
            }
            
            
            $odl_tn=$this->_tableName;
            $fields="d.bkd_id, d.bkw_id, d.mod_id, d.area, d.title, d.img_url, d.start_time, d.end_time,
                     k.bkw_name, k.bkw_mark, k.bkw_type, k.tpl_id, k.`order`, k.mod_type,
                     c.cmp_id, c.cmp_name, c.city_name, c.static_url AS cmp_static_url,c.web_url,c.page_url
                     ";
                     
            $this->_tableName='block_keydata d';
            
            //招商这里sql没写全
            $leftjoin="
                INNER JOIN block_keywords k ON k.bkw_id=d.bkw_id  
                INNER JOIN cmp_company c ON c.cmp_id=d.mod_id AND k.mod_type=2
                ";
             $condition.=" AND ". NOW." >= d.start_time AND ".NOW." <= d.end_time ";
            
            $data=$this->getList($condition,$fields,' ORDER BY d.`order` desc, d.bkd_id desc',1,1000,0,$leftjoin);          
            
            $this->_tableName=$odl_tn; 
            
            return $data['list']; 
        }
        
        public function getAreas()
        {
            return array(1=>'左侧列表区',2=>'右侧热门区');
        }
    
    
    
    
}