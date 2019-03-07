<?php
 /**
* @name: 主营类别 
* @author: wukai
* @date: 12:03 2015/4/13
*/
class Model_cmp_type extends Base_model{
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
            1 => 'OTC',
            2 => '保健品',
            3 => '卫生用品',
            4 => '药妆',
            5 => '贴剂',
            6 => '中药材',
            7 => '中间体',
            8 => '消毒剂',
            9 => '原料药',
            10 => '药用辅料',
            11 => '药品包装',
            12 => '诊断试剂',
            13 => '计生用品',
            14 => '医疗设备',
            15 => '医疗器械',
            16 => '西药产品',
            17 => '中药产品'
        );
		return $data;
    }
}