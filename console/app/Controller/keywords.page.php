<?php
/**
* @name: 关键字管理
* @author: zhanghao
* @date: 11:00 2015/3/22
*/        

class Controller_keywords extends Controller_basepage {
	//首页
	function pageList($inPath){
        #得到当前页码
        $page=$this->getPageNumber($inPath);
        #实例化 关键字管理 数据模型
        $mdl_keywords = new Model_search_keywords();
        //$testModel->setReaddb('count');
        $mdl_keywords->setCache(false);
        
        #得到搜索 
        $kw_name=$this->request['kw_name'];
        $hot=$this->request['hot'];
        $type=$this->request['type'];
        $ss_state=$this->request['ss_state'];
        $srow=array('kw_name'=>$kw_name,'hot'=>$hot,'type'=>$type,'ss_state'=>$ss_state,);
        

        #得到搜索关键字列表
        $data = $mdl_keywords->getListAll($page,$srow);
        
        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($data['list']);$i++){
            $list[$i]['start_time']=SUtil::formatTime($list[$i]['start_time']);
            $list[$i]['end_time']=SUtil::formatTime($list[$i]['end_time']);
            
            $this->request['id']=$list[$i]['kw_id'];
            $list[$i]['del_url']=$url=SRoute::createUrl('keywords/del', array('page' => $page,'id'=>$list[$i]['kw_id']), $this->request);
        }

        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath,20);
        $param['srow']=$srow;


        //默认ad/ad_list.html
        #调用相应模版  
        return $this->render($this->tplFilePath,$param);
	}
	
	//编辑
	
    function pageEdit(){
	    //SUtil::error('提交成功');
            $id=intval($this->request['id']);
            $mdl_keywords = new Model_search_keywords();  
           
            //如果有保存请求
            if($this->request['_ac']=='save')
            {
                $row=$this->request['row'];
                $row=SUtil::html_arr($row);
                
                //与数据库类型一致
                $row['kw_id']=intval($row['kw_id']);
                $row['sk_count']=intval($row['sk_count']);
                $row['hot']=intval($row['hot']);
                   //一系列检查
                if(!$row['kw_name']) {
                //  SUtil::error('');
                //$this->showMessage("提交失败，关键字不能为空！");
                SUtil::error("提交失败，关键字不能为空！");
                exit;
                }
                
                if($row['hot']>255 ||$row['hot']<0) {
            	    SUtil::error("提交失败，热门程度应该在0—255之间！");
            	    exit;
                }
                                                                                  
                if($id) {
                    //修改
            	    $this->ass('TIP_NAME','修改');
                    $result=$mdl_keywords->updateRowById($row,$id); 
                    if($result)
                    {
                          $this->showMessage('修改成功');
                    }
                    else
                    {
                         $this->showMessage('修改失败');
                    }

                }
                else
                {
                    //添加
            	    //检查关键字是否已经存在
            	    if($mdl_keywords->getRowByName($row['kw_name'])){
            		    SUtil::error("提交失败，关键字已存在！");
            		    exit;
            	    }
            	    $this->ass('TIP_NAME','添加');
            	    $row['dateline']=NOW;
                //	$row['dateline']=SUtil::formatTime(NOW,3);
                    $result=$mdl_keywords->addRow($row); 
                    if($result)
                    {
                         $this->showMessage('添加成功');
                    }
                }
                
            }
            else
            {
                if($id)
                {
                    //修改初始化
                    $row=$mdl_keywords->getRowById($id);
                    //添加初始化
                    $this->ass('TIP_NAME','修改');
                    $this->ass('AC_NAME','modify');
                    
                }
                else
                {
                    //添加初始化
                    $this->ass('TIP_NAME','添加');
                    $this->ass('AC_NAME','add');
                    $row['type']=1;
                    $row['is_del']=0;
                    $row['ss_state']=1;
                    
                }  
            }

		    $this->preparRow($row);
            $this->ass('id',$id); 

            return $this->template();
	    }
        
	    
	    
    private function preparRow($row)	
    {	
	    if($row)
	    {
		    //选中设置

		    $type_checked=array($row['type']=>"checked='checked' ");
		    $is_del_checked=array($row['is_del']=>"checked='checked' ");
		    $ss_state_checked=array($row['ss_state']=>"checked='checked' ");
	    
		    $this->ass('type_checked',$type_checked);
		    $this->ass('is_del_checked',$is_del_checked);
		    $this->ass('ss_state_checked',$ss_state_checked);
	    
		    $this->ass('row',$row);
	    }
    }
    
    
    /**
    * 删除
    * add by baoxianjian 19:00 2015/3/28 删除单独了
    * 
    */
    function pageDel()
    {    
        //如果在列表页删除s数据
        $mdl_kw = new Model_search_keywords();     
        $id=$this->request['id'];
        $result=$mdl_kw->deleteRowById($id);
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
