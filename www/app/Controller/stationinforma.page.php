<?php
/**
* @name: 代理信息控制器 
* @author: zhanghao
* @date: 16:22 2015/4/9
*/        
define('SYS_NAME','stationinforma');


class Controller_stationinforma extends Controller_basepage {
       
  /*   //企业中心首页
    function pageIndex($inPath){
        $mdl_user=new Model_user_user(); 
        
        $ru=$this->getUserFromSession();
                
        $row=$mdl_user->getRowById($ru['id']);
        
        //$this->buildTplFilePath('user.index','user');  
        
        $row['reg_time']=SUtil::formatTime($row['reg_time']);
        $params['info']=$row;
        return $this->template($params);
    }
 */
    /**
     * 显示账户信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
/*    
    function pageInfo()
    {
       $mdl_cmp=new Model_cmp_company();
       return $this->template(); 
    }
    
    function pageInfoEdit()
    {
        $this->getCompanyFromSession(1);
        
        
        $mdl_area=new Model_com_area();
        //得到地区编码
        $area_rowset = $mdl_area->getAllRowset(false,false);
        $super = 'A'.substr($row['citycode'],0,2).'0000';
        $area_sel[$super] = 'selected="selected"';
        
        $sub_area_rowset = $mdl_area->getAllRowset($super,true);
        $sub = 'A'.substr($row['citycode'],0,4).'00';
        $sub_area_sel[$sub] = 'selected="selected"';
        
        $min_sub_rowset =  $mdl_area->getAllRowset($sub,true);
        $min_sub_sel['A'.$row['citycode']] = 'selected="selected"';
        
        
        $this->ass('area_rowset',$area_rowset);
        
        $this->ass('session_id',session_id());
        
        return $this->template(); 
    }
    */
   
   
    
    /**
     * 显示 站内来电信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    function pageCall($inPath){
    	$page=$this->getPageNumber($inPath);
    	#实例化 关键字管理 数据模型
    	$mdl_stationinforma = new Model_stationinforma_stationinforma();
    	//$testModel->setReaddb('count');
    	$mdl_stationinforma->setCache(false);
    	#得到搜索
    	$start_time=$this->request['start_time'];
    	$end_time=$this->request['end_time'];
    	$srow=array('start_time'=>$start_time,'end_time'=>$end_time);
    	
    	#得到搜索关键字列表
    	$data = $mdl_stationinforma->getListAll($page, $srow);
    	#数据临时处理
    	$list=$data['list'];
    	for($i=0;$i<count($data['list']);$i++){
    			$this->request['id']=$list[$i]['ae_id'];
    			$list[$i]['del_url']=$url=SRoute::createUrl('stationinforma/call', array('page' => $page,'id'=>$list[$i]['ae_id']), $this->request);
    	}
    	#设置好要放入模版的数据（变量）
    	$param[LIST_VAR_NAME] = $list;
    	$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);
    	$param['srow']=$srow;
    	$this->preparRow($srow);
    	//return $this->render($this->tplFilePath,$param);
    	return $this->template($param);
    }
    /**
     * 分配 信息(用户中心)
     * @param array $row
     * @param string $msg
     * @param int $type
     * @param string $tourl
     * @return mixed
     */
    private function preparRow($srow)
    {
    	if($srow)
    	{
    		//选中设置    
    		$is_read_checked=array($srow['is_read']=>"checked='checked' ");    		
    		$this->ass('is_read_checked',$is_read_checked);
      	  
    		$this->ass('srow',$srow);
    	}
    }
    
    
        
}