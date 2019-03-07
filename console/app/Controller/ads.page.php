<?php
/**
* @name: 搜索广告 
* @author: baoxianjian
* @date: 10:32 2015/3/20
*/        

class Controller_ads extends Controller_basepage {
    
	//列表页
	function pageList($inPath){
        #得到当前页码
        $page=$this->getPageNumber();
        #实例化 搜索广告 数据模型
        $mdl_ads = new Model_search_ads();
        //$testModel->setReaddb('count');
        $mdl_ads->setCache(false);
        
        $ac=$this->request['ac'];


        #搜索
        $s_id=intval($this->request['id']);
        $s_title=$this->request['title'];
        $s_state=intval($this->request['state']);
        $srow=array('id'=>$s_id,'title'=>$s_title,'state'=>$s_state);
        
        $state_selected[$s_state]='selected="selected"';
                        
        #得到搜索广告列表
        $data = $mdl_ads->getListAll($page,$srow);

        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($list);$i++){
            $list[$i]['start_time']=SUtil::formatTime($list[$i]['start_time']);
            
            $list[$i]['state']= NOW > intval($list[$i]['end_time'])? "<font color='red'>过期</font>":"正常";
            
            $list[$i]['end_time']=SUtil::formatTime($list[$i]['end_time']);
            
            $this->request['id']=$list[$i]['sg_id'];
            $list[$i]['del_url']=$url=SRoute::createUrl('ads/del', array('page' => $page,'id'=>$list[$i]['sg_id']), $this->request); 
        }
        //unset($this->request['id']);
        
       // print_r($this->request);

        #设置好要放入模版的数据（变量）
        $param[LIST_VAR_NAME] = $list;
        $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);

        $param['srow']=$srow;
        $param['state_selected']=$state_selected;
        
        //默认ad/ad_list.html
        #调用相应模版
        return $this->template($param);
	}
    
    // 广告的编辑 增加/删除
	function pageEdit(){
       
        $id=intval($this->request['id']);
        $mdl_ads = new Model_search_ads();  

        $mdl_adp=new Model_search_adposition();
        $list_adp=$mdl_adp->getRowsetAll(); 
        
        
        //title后面价格id，方便看到已占用
        foreach ($list_adp['list'] as $k=>$v) {
            if($v['sg_id'])
            {
                 $list_adp['list'][$k]['title'].='/'.$v['sg_id'];
            }
            $list_adp['list'][$k]['title'].= "__{$v['width']}x{$v['height']}";
        }
        
        $this->ass('list_sgp',$list_adp['list'],false);
        
        //如果有保存请求
        if($this->request['_ac']=='save')
        {
            $row=$this->request['row'];
            $row=SUtil::html_arr($row);
            
            //与数据库类型一致
            $row['cmp_id']=intval($row['cmp_id']);
            $row['pdt_id']=intval($row['pdt_id']);
            $row['sgp_id']=intval($row['sgp_id']); 
            $row['order']=intval($row['order']);
            
            $row['start_time']=strtotime($row['start_time']);
            $row['end_time']=strtotime($row['end_time']);
            
            $passed=true;           
            //一系列检查
           if(!$row['title'])
            {
                $this->showMessage("提交失败，标题不能为空！",3);
                $passed=false;
            }
            if(!$row['keywords'])
            {
           	    $this->showMessage("提交失败，标题不能为空！",3); 
           	    $passed=false;
            }
            /*
            if(!$row['link_url'])
            {
                $this->showMessage("提交失败，标题不能为空！",3); 
            	$passed=false;
            }
                           
            if(!$row['order'] || $row['order']<=0  )
            {
            	$this->showMessage("提交失败，排序为空或格式不正确！",3);
            	$passed=false;  
            } */ 
            if(!$row['start_time'])
            {            	
            	$this->showMessage("提交失败，开始时间不能为空！");
            	$passed=false;
            }
            if(!$row['end_time'])
            {
            	$this->showMessage("提交失败，结束时间不能为空！");
            	$passed=false; 
            }
            
            if($row['end_time']<$row['start_time']){
            	
            	$this->showMessage("提交失败，结束时间不能小于开始时间！");
            	$passed=false; 
            }
            
            
                                    
            if($id && $passed)
            {
                //修改
                $this->ass('TIP_NAME','修改'); 
                $result=$mdl_ads->updateRowById($row,$id);
                
                print_r(array('sg_id'=>$id),$row['sgp_id']);
                $result2=$mdl_adp->updateRowById(array('sg_id'=>$id),$row['sgp_id']);
                
                if($result || $result2)
                {      
                    $this->showMessage('修改成功');
                    
                }
                else
                {
                     $this->showMessage('修改失败',3); 
                }

            }
            else if($passed)
            {
                //添加
                $this->ass('TIP_NAME','添加');
                $row['dateline']=NOW;
                $result=$mdl_ads->addRow($row); 
                
                $result2=$mdl_adp->updateRowById(array('sg_id'=>$result),$row['sgp_id']);
                
                if($result || $result2)
                {
                    $this->showMessage('添加成功'); 
                }
                else
                {
                    $this->showMessage('添加失败'); 
                }
            }
            
        }
        else
        {
            if($id)
            {
                //修改初始化
                $row=$mdl_ads->getRowById($id);

                $this->ass('TIP_NAME','修改');
                $this->ass('AC_NAME','modify');
                
            }
            else
            {
                //添加初始化
                $this->ass('TIP_NAME','添加');
                $this->ass('AC_NAME','add');
                $row['sg_type']=2;
                $row['recommend']=0;
                $row['sg_state']=1;
                
            }  
        }

        if($row)
        {
            //时间格式化输出
            if($row['start_time']){
            	
               $row['start_time']=SUtil::formatTime($row['start_time'],3);
            }
            if($row['end_time']){
              $row['end_time']=SUtil::formatTime($row['end_time'],3);
            }
            $row['img_url_temp']=UPLOADS_URL.$row['img_url'];;   
            
            //选中设置
            $sgp_id_selected=array($row['sgp_id']=>"selected='selected' ");
            $sg_type_checked=array($row['sg_type']=>"checked='checked' ");
            $recommend_checked=array($row['recommend']=>"checked='checked' ");
            $sg_state_checked=array($row['sg_state']=>"checked='checked' ");
            
            $this->ass('sgp_id_selected',$sgp_id_selected);
            $this->ass('sg_type_checked',$sg_type_checked);
            $this->ass('recommend_checked',$recommend_checked); 
            $this->ass('sg_state_checked',$sg_state_checked); 
            
            $this->ass('row',$row);
        }
        
        $this->ass('session_id',session_id());
        
        $this->ass('id',$id); 
        
        return $this->template();
	}
	
    
    /**
    * 删除
    * 
    */
	function pageDel()
    {    
        //如果在列表页删除s数据
        $mdl_ads = new Model_search_ads();     
        $id=$this->request['id'];
        $result=$mdl_ads->deleteRowById($id);
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
    
    
    
    function pageCheckId($inPath)
    {
        $type=$this->request['t'];
        $id=intval($this->request['id']);

        if($type==2)
        {
            $mod_cmp=new Model_cmp_company();
            $cname=$mod_cmp->getNameById($id);
            
            if($cname)
            {
                $this->showMessage("企业ID可以用，其名称为：{$cname}",1);
            }
            else
            {
                $this->showMessage("企业ID不存在！",3);
            }
        }
        else if($type==1)
        {
            $mod_pdt=new Model_pdt_products();
            
            $pname=$mod_pdt->getNameById($id);
            

            if($pname)
            {
                $this->showMessage("产品ID可以用，其名称为：{$pname}",1);
            }
            else
            {
                $this->showMessage("产品ID不存在！",3);
            }
            
        }        
        
        
    }
}
