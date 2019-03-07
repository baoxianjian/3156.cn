<?php
 /**
* @name: 部位模块 
* @author: wukai
* @date: 12:03 2015/4/13
*/
class Model_ill_buwei extends Base_model{
	public function setPrimaryKey() {
        $this->_PK = '';//设置主键
    }
    //@override
    public function setTableName() {
        $this->_tableName = ''; //设置表名
        
    }
    //@override
   	public function setCache($status=false){
    	$this->_cache = $status;
    }
	
    //@override
    public function getData() {
        $data = array(
            1 => '全身',
            15 => '下肢',
            16 => '四肢',
            17 => '鼻',
            18 => '耳',
            19 => '口',
            20 => '头',
            21 => '眼',
            22 => '牙',
            23 => '眉',
            24 => '毛发',
            14 => '上肢',
            13 => '皮肤',
            12 => '盆腔',
            2 => '头部',
            3 => '颈部',
            4 => '胸部',
            5 => '背部',
            6 => '腰部',
            7 => '腹部',
            8 => '臀部',
            9 => '生殖部位',
            10 => '男性股沟',
            11 => '女性盆骨',
            25 => '其他'
        );
		return $data;
    }
    
}