<?php
/**
* @name: 广告位的服务层 
* @author: baoxianjian
* @date: 9:22 2015/4/23
*/
class Service_block_keywords extends Base_service{
    
    public function getRowsetAll($srow)
    {
        $mdl_bkw=new Model_block_keywords();
        $rowset=$mdl_bkw->getRowsetAll($srow);

        //分类重组
        foreach ($rowset as $v) 
        {
            if(WEB_STATIC==1)
            {
                $v['bkw_url'] = "/{$v['bkw_mark']}.shtml";
            }
            else
            {
                $v['bkw_url'] = "/keywords/show-m-{$v['bkw_mark']}";
            }
            
            
            $rowset_temp[$v['bkw_type']][]=$v;
        }

        return $rowset_temp; 
    }
}