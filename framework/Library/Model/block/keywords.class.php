<?php
 /**
* @name: 用户的数据模型 
* @author: baoxianjian
* @date: 20:51 2015/4/20
*/
class Model_block_keywords extends Base_model{
    //@override
    public function setPrimaryKey() {
        $this->_PK = 'bkw_id';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = 'block_keywords'; //设置表名
        
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
        $condition="`is_del`=0";
        if(count($srow)>0)
        {
            if($id=intval($srow['id']))
            {
               $condition.=" AND {$this->_PK}={$id}"; 
            }
            
            if($bkwn=trim($srow['bkwn']))
            {
                $condition.=" AND `bkw_name` LIKE '%{$bkwn}%'";
            }
            if($type=intval($srow['type'])) //正常
            {
                $condition.=" AND `bkw_type`={$type}";
            }
        }
        
        return $this->getList($condition,array('*'),array($this->_PK=>'desc'),$page,10,1);
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

    
    /**
    * 得到区块关键字名称（根据id）
    * 
    * @param int $cid
    * @return array|false
    */
    public function getNameById($id)
    {
        if(!$id=intval($id)){return false;}
        $row= $this->getOne("{$this->_PK}={$id}",'bkw_name');
        return $row['bkw_name']; 
    }
    
    /**
    * 得到区块关键字名称（根据id）
    * 
    * @param int $cid
    * @return array|false
    */
    public function getNameByMark($mark)
    {
        if(!$mark=trim($mark)){return false;}
        $row= $this->getOne("bkw_mark='{$mark}'",'bkw_name');
        return $row['bkw_name']; 
    }
   
     
    public function getRowsetAll($srow=array())
    {
        $condition='is_del=0';
        $fields="bkw_id,bkw_name,bkw_mark,mod_type,bkw_type,`order`";
        $data=$this->getList($condition,$fields,' ORDER BY `order` desc, bkw_id desc',1,1000,0); 
        
        $this->_tableName=$odl_tn; 
        
        return $data['list']; 
    }
    
    
    public function searchListAll($page,$srow,$limit=10)
    {
        $page=intval($page);
        if(!$limit=intval($limit))
        {
             $limit=10;
        }

       if(count($srow)>0)
       {
            if($kw=trim($srow['kw']))
            {
               $q.="medicament_type:{$kw} OR type_name1:{$kw} OR name:{$kw}";
            }

            //$q=ltrim($q,' AND');
           // $fq=ltrim($fq,' AND');
        }
        if(!$q)
        {
            $q="*:*";
        }

        $sort="cmp_type desc, cmp_lv desc, dateline desc";
        
        $q=urlencode($q);
        $fq=urlencode($fq);
        $sort=urlencode($sort);
        
        $qstr="q={$q}&sort={$sort}";
        //$qstr="q={$q}";

        
        //  $limit=10;
        $start=($page-1)*$limit;    //0,10   10,10  20,10
        
        $url=SOLR_URL."/productall/select?{$qstr}&wt=json&start={$start}&rows={$limit}";

        
        $rc=SRemote::getCurlContent($url);
        
        $r_list = json_decode($rc,true);
        $r_list = $r_list['response'];
        $r_list['count'] = $r_list['numFound'];
        $r_list['list'] = $r_list['docs']; 
        
        unset($r_list['docs']);
        return $r_list;
    }
    
    
    
    
}