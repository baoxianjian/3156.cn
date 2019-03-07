<?php
/**
* @name: 搜索广告 
* @author: baoxianjian
* @date: 10:32 2015/3/20
*/        

class Controller_blacklist extends Controller_basepage {
    
	//列表页
	function pageList($inPath){
        #得到当前页码
        $page=$this->getPageNumber();
        #实例化 搜索广告 数据模型
        $mdl_blacklist=new Model_agent_blacklist();
        //$testModel->setReaddb('count');
        $mdl_blacklist->setCache(false);
 
        #收索
        $tel=$this->request['phone'];
    //    $phone=$this->request['phone'];
      if($row=$this->request['row']){  
/*      	         $phone=$row['phone'];
        	    if($mdl_blacklist->getRowByTel($phone)){
      		       $this->showMessage('该号码已加入黑名单!',3); 
      		       echo "该号码已加入黑名单!";     		       
     		       //exit();
        	    }    		     				            
           }else{                                
       if(preg_match("/^d*$/", $row['tel']))
		    {
*/			$result=$mdl_blacklist->addRow($row);
				if($result)
					 {
						$this->showMessage('加入黑名单成功',1);
					 }
					 else
					 {
					  	$this->showMessage('加入黑名单失败',3);
					 }									   		     		               

         }    
        #得到搜索广告列表
        
        $srow=array('tel'=>$tel);
        $data = $mdl_blacklist->getListAll($page,$srow);

        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($list);$i++){
            $list[$i]['start_time']=SUtil::formatTime($list[$i]['start_time']);
            $list[$i]['end_time']=SUtil::formatTime($list[$i]['end_time']);
            
            $this->request['id']=$list[$i]['abl_id'];
            $list[$i]['del_url']=$url=SRoute::createUrl('blacklist/del', array('page' => $page,'id'=>$list[$i]['abl_id']), $this->request); 
        }
        //unset($this->request['id']);
        
       // print_r($this->request);

        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);

        $param['srow']=$srow;
        
        //默认ad/ad_list.html
        #调用相应模版
        return $this->template($param);
	}     
    
    /**
    * 删除
    * 
    */
	function pageDel()
    {    
        //如果在列表页删除s数据
        $mdl_blacklist=new Model_agent_blacklist();    
        $id=$this->request['id'];
        $result=$mdl_blacklist->deleteRowById($id);
        if($result)
        {
            $this->showMessage('删除成功',1,'_reload');
        }
        else
        {
            $this->showMessage('删除失败',3);
        }
        return $this->template();
    }
  
    
    
}
