<?php
/**
* @name: 搜索广告 
* @author: baoxianjian
* @date: 20:14 2015/4/8
*/        

class Controller_admins extends Controller_basepage {
    
	//列表页
	function pageList($inPath){
        $this->checkPermission('admins','list');
        
        #得到当前页码
        $page=$this->getPageNumber();
        #实例化 搜索广告 数据模型
        $mdl_adm = new Model_sys_admins($this->saId);
        //$testModel->setReaddb('count');
        $mdl_adm->setCache(false);


        #收索
        $s_id=$this->request['id'];
        $s_gid=$this->request['gid'];
        $s_aname=$this->request['aname'];
        // $link_url=$this->request['link_url'];
         $srow=array('id'=>$s_id,'gid'=>$s_gid,'aname'=>$s_aname);

                        
        #得到搜索广告列表
        $data = $mdl_adm->getListAll($page,$srow);

        #数据临时处理
        $list=$data['list'];
        for($i=0;$i<count($list);$i++){
            $list[$i]['dateline']=SUtil::formatTime($list[$i]['dateline']);
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
    
    //管理员的编辑 增加/修改
	function pageEdit(){
       
        $id=intval($this->request['id']);
        $mdl_adm = new Model_sys_admins($this->saId);  

        $mdl_grp=new Model_sys_groups($this->saId);
        $list_grp=$mdl_grp->getRowsetAll();
        
        $this->ass('list_grp',$list_grp['list']);
        
        //如果有保存请求
        if($this->request['_ac']=='save')
        {
            $row=$this->request['row'];

            //与数据库类型一致
            $row['sg_id']=intval($row['sg_id']);

            $mylevel= $mdl_adm->getLevelById($this->saId);   
            $passed=true;
            //级别只能设置得比自己小
            if($row['sa_level']>$mylevel)
            {
                $this->showMessage('设置的权限级别不能大于自己的级别',3);
                $passed=false;
            }
                                
            if($id && $passed)
            {
                //修改
                $this->ass('TIP_NAME','修改'); 
                $result=$mdl_adm->updateRowById($row,$id);

                
                if($result)
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
                $result=$mdl_adm->addRow($row); 
                
                if($result)
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
                $row=$mdl_adm->getRowById($id);

                $this->ass('TIP_NAME','修改');
                $this->ass('AC_NAME','modify');
                
            }
            else
            {
                //添加初始化
                $this->ass('TIP_NAME','添加');
                $this->ass('AC_NAME','add');
                
            }  
        }

        if($row)
        {
            $grp_selected=array($row['sg_id']=>'selected="selected"');
            
            $this->ass('grp_selected',$grp_selected);
            $this->ass('row',$row);
        }

        
        $this->ass('id',$id); 
        
        return $this->template();
	}
	
    //管理员的赋权
    function pageGrant(){
       
        $id=intval($this->request['id']);
        
        $mdl_grp = new Model_sys_groups($this->saId);  
        $mdl_adm=new Model_sys_admins($this->saId);
        
        $row_adm=$mdl_adm->getRowById($this->saId);   
         
        //如果有保存请求
        if($this->request['_ac']=='save')
        {
            foreach ($this->request as $k=>$v) 
            {
                $k2=substr($k,1);
                #以#开头，并且是数组类型的，说明是权限数组
                if($k{0}=='#' && is_array($v))
                {
                    $myp[$k2]=$v;
                }
            }
            
            $myp_str=json_encode($myp);
            
            $row=$this->request['row'];
            $row['sa_permissions']=$myp_str;
            
            $passed=true;
            //级别只能设置得比自己小
            if($row['sa_level']>$row_adm['sa_level'])
            {
                $this->showMessage('设置的权限级别不能自己的级别',3);
                $passed=false;
            }
                                    
            if($id && $passed)
            {
                //修改
                $this->ass('TIP_NAME','赋权'); 
                
                $result=$mdl_adm->updateRowById($row,$id);

                
                if($result)
                {      
                    $this->showMessage('赋权成功');
                    
                }
                else
                {
                     $this->showMessage('赋权失败',3); 
                }

            }
            else
            {
                //没有添加
            }
            
        }
        else
        {
            if($id)
            {
                //修改初始化
                $row=$mdl_adm->getRowById($id);  
                
                $this->ass('TIP_NAME','赋权');
                $this->ass('AC_NAME','modify');
                
            }
            else
            {
                //添加初始化
            }  
        }


        $this->_prepareRow($row);

        
        
        $permissions=$mdl_grp->getPermissionsAll($row_adm['sa_permissions'],$row_adm['sa_level']);    
        $dic=$mdl_grp->getPermissionsDic();

        $this->ass('permissions',$permissions);
        $this->ass('dic',$dic);
        
        $this->ass('id',$id); 
        
        return $this->template();
    }

    //管理员的设置密码
    function pageSetPwd(){
       
        $this->ass('TIP_NAME','密码设置');  
        $id=intval($this->request['id']);
        
        $mdl_adm=new Model_sys_admins($this->saId);
        
         
        //如果有保存请求
        $passed=true;
        if($this->request['_ac']=='save')
        {
            $row=$this->request['row'];

            if($row['pwd']=='')
            {
                 $this->showMessage('密码不能为空',3); 
                 $passed=false;
            }
            if($row['pwd_temp']!=$row['pwd'])
            {
                 $this->showMessage('两次密码不一致',3); 
                 $passed=false;
            }
                                    
            if($id && $passed)
            {
                //修改
                $row_temp['sa_pwd']=md5($row['pwd']);
                $result=$mdl_adm->updateRowById($row_temp,$id);
 
                if($result)
                {      
                    $this->showMessage('密码设置成功');
                }
                else
                {
                     $this->showMessage('密码设置失败',3); 
                }
            }
            else
            {
                //没有添加
            }
            
        }
        else
        {
            if($id)
            {
                //修改初始化  
                
            }
            else
            {
                //添加初始化
            }  
        }

        $sa_name=$mdl_adm->getSaNameById($id);
        
        $this->ass('sa_name',$sa_name);
        
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
        $mdl_grp = new Model_sys_groups($this->saId);     
        $id=$this->request['id'];
        $result=$mdl_grp->deleteRowById($id);
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
    
    
    private function _prepareRow($row)
    {
        if($row['sa_permissions'])
        {
            $myp=json_decode($row['sa_permissions'],true);
            
            $myp_checked=array();
            foreach ($myp as $k=>$v) {
                foreach ($v as $v2) {
                    $myp_checked[$k][$v2]="checked='checked'"; 
                }
            }
            
            $this->ass('myp_checked',$myp_checked);
        }
        $this->ass('row',$row);     
    }
    
    private function showPermissionMessage($row,$msg,$type=0,$tourl)
    {
        $this->showMessage($msg,$type,$tourl,3000);
        $params['row']=$row;
        
        return $this->template($params);
    }
}