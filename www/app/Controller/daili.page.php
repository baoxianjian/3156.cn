<?php
/**
* @name: 医药代理控制器 
* @author: zhanghao
* @date: 12:00 2015/4/15
*/        
define('SYS_NAME','daili');

class Controller_daili extends Controller_basepage {

     //代理留言列表
    function pageIndex($inPath){
    	//得到区域数组；
    	$mdl_area=new Model_com_area();
    	$area_rowset = $mdl_area->getAllRowset(false,false);
    	
    	//获得一级产品类型
    	$mdl_pdtype=new Model_pdt_pdtTypes();
    	$data=$mdl_pdtype->getListAlltypeone($page);
    	$tplist=$data['list'];
    	$this->ass('tplist', $tplist);
    	
    	
    	#实例化 关键字管理 数据模型
    	$mdl_books = new Model_agent_books();    
           	//当前页面
        	$page=$this->getPageNumber($inPath);
        	//$testModel->setReaddb('count');
			$mdl_books->setCache(false);       		 
        	#得到搜索关键字列表
        	
			$time=$this->request['time'];
			$pdt_type1=$this->request['type'];
			$areas=$this->request['areas'];
			$agent_type=$this->request['at'];
			$k=$this->request['k'];
			
			$srow=array('k'=>$k,'time'=>$time,'pt1'=>$pdt_type1,'areas'=>$areas,'at'=>$agent_type);
			
        	//$data = $mdl_books->getListMedicagent($page,$srow);
            
            $data = $mdl_books->searchListAll($page,$srow,25);
        	
        	$list=$data['list'];
	
        	for($i=0;$i<count($data['list']);$i++)
        	{
        	$this->request['id']=$list[$i]['id'];
        	 
        	$list[$i]['tel_temp']=substr($list[$i]['tel'], 0, 3);
        	if(WEB_STATIC==1)
        	{
        		$list[$i]['view_url'] = "/daili/{$list[$i]['id']}.shtml";
        	}
        	else
        	{
        	    $list[$i]['view_url'] = "/daili/msg-id-{$list[$i]['id']}";
        	}
        	
        	
        	}
        	
        	 /*
        	$mdl_area = new Model_com_area();
        	foreach($data['list'] as $key=>$value){        		
        		if($value)
        			$areas_array = $areas_arrays = array();
        		if($value['type'] == 2){
        			$areas_array = explode(',', $value['areas']);
        			foreach($areas_array as $k=>$val){
        				$_areas = $mdl_area->getNameByCodeId($val);
        				$areas_arrays[$k] = $_areas;
        			}
        			$list[$key]['area_list'] = implode(',', $areas_arrays);
        		}
        			       	
        	}
            */
        	
        	
        	
        	#设置好要放入模版的数据（变量）
        	$param[LIST_VAR_NAME] = $list;
        	$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath,25,'style1',false);
        	$param['srow']=$srow;
        	
        	$this->ass('row', $srow);	
        	$this->ass('area',$area_rowset);
        	
        	
        	$ru=$this->getCompanyFromSession(0);
        	$cid=$ru['cid'];
        	$uid=$ru['uid'];
        	        	
        	$this->ass('cid',$ru['cid']);
        	$this->ass('uid',$ru['uid']);
        	        	           	
        	
        	if($srow['pdt_type1']!='' || $srow['areas']!='' ){
        		foreach($tplist as $v){
        		if($srow['pdt_type1']==$v['pt_id']){
        		     $pdt_type1=$v['pt_name'];      		     
        		   }
        		}    
        		//$this->ass('pdt_type1', $pdt_type1);		        		       		
        		$title="{$areas}{$pdt_type1}代理信息_{$areas}{$pdt_type1}代理—3156医药网";
        		$keywords="{$areas}{$pdt_type1}代理信息,{$areas}{$pdt_type1},3156医药网";
        		$description="3156医药网,是专业的医药行业电子商务网站,为您提供{$areas}{$pdt_type1}代理信息。相信通过3156庞大的医药代理信息数据库,医药代理将不再是个难题！";
        	
         	}else{
        	    $title='医药代理_药品代理_代理商信息_3156医药网_全国药品网';
        	    $keywords='医药代理信息,药品代理信息,保健品代理信息,医疗器械代理信息搜索列表：地区产品类别代理信息,地区产品类别,3156医药网';
        	    $description='3156医药网,是专业的医药行业电子商务网站,拥有海量的药品代理信息,包括医药代理产品、代理商、保健品代理、药品代理等信息。相信通过3156庞大的医药代理信息数据库,医药代理将不再是个难题!';
        	}	             	
        	$this->setSEO($title,$keywords,$description);
        	
        	return $this->render($this->tplFilePath,$param);
     	}
     	
     	//免费发布代理信息
        //update by baoxianjian 11:39 2015/5/12
     	function pageFbxq($inPath)
        {   //获得一级产品类型
        	$mdl_pdtype=new Model_pdt_pdtTypes();
        	$data=$mdl_pdtype->getListAlltypeone($page);
            
        	$list=$data['list'];
        	$this->ass('list', $list);
        	//获取账户名
    //    	$ru=$this->getUserFromSession(0);
    //   	print_r($ru);
   //     	$uname=$ru['name'];
   //     	$mdl_user=new Model_user_user();
   //     	$us=$mdl_user->getRowByUserName($uname);
   //     	$this->ass("us", $us);
        	$ip=SRemote::getRealIp();
        	
        	
	     	$mdl_books= new Model_agent_books();
			$row=$this->request['row'];
			//$row['areas']=$_POST['areas'];    
   
			//保存修改
			if($this->request['ac']=='save')
			{
				$channel=$this->request['channel'];//一个数组；
				$channel=implode(",", $channel);//一个字符串；
		     	$ru=$this->getUserFromSession(0);
		     	
                $mdl_area = new Model_com_area();
                
                //地区处理
                if($row['areas'])
                {
                    foreach ($row['areas'] as $v)
                    {
                         $areas_code .=','.$v;
                         $areas_str .=','.$mdl_area->getNameByCodeId($v);
                    }
                    $areas_code=trim($areas_code,',');
                    $areas_str=trim($areas_str,',');
                    
                    $a = $mdl_area->getSplitedCodeIds($row['areas'][0]);
                    
                    $row['province'] = $mdl_area->getNameByCodeId($a['0'].'0000');
                    $row['areas'] =  $areas_str;
                    $row['areas_code'] = $areas_code;
                    $row['areas_code1']=$a[0];
                }
              
                if(!$row['areas'])
		{
                  	$this->showMessage('代理区域不能为空!',1);
                 	exit('area can not be null');
                }
		
		$row['channel']=$this->request['channel'];
		if(!$row['channel'])
		{
                  	$this->showMessage('代理渠道不能为空!',1);
                 	exit('channel can not be null');
		}
		if(!$row['pdt_name'])
		{
                  	$this->showMessage('意向产品不能为空!',1);
                 	exit('pdt_name can not be null');
		}
               
                
                //处理代理渠道
                if($row['channel'])
                {
                    $row['channel']=implode(',',$row['channel']);
                }
     
				$row['link_man']=trim($row['link_man']);
				$row['end_time']=intval($row['end_time']);
				$row['pdt_type1']=intval($row['pdt_type1']);							    								
				$row['tel']=trim($row['tel']);
				$row['fax']=trim($row['fax']);
				$row['qq']=trim($row['qq']);
				$row['channel']=$channel;
				$row['user_id']=intval($ru['uid']);
				$row['ip']=$ip;
				//判断同一ip是否对某一产品已经发布代理信息，如果已经发布，状态为未审核
				$res=$mdl_books->getRowByIpandName($row['ip'],$row['pdt_name']);
				if($res){
					$row['audit_state']=1;   //若存在 说明已经对这产品发布过   需要审核
				}else{
					$row['audit_state']=2;  //否则 默认第一条审核通过
				}
				
				
				
					$row['dateline']=NOW;
					$result=$mdl_books->addRow($row);
					if($result)
					   {	
							//$this->showMessage('代理信息添加成功','511');
							
							//SUtil::success("添加成功！");
							
						
							
					   
						$this->buildTplFilePath('daili.success');
						$this->pageSuccess();
							//$this->redirect($url)
							
					   }
		 
				}
				else 
				{
					//添加初始化
					$row['agent_type']=1;				
				}
				
				
		$title='医药代理_发布需求-3156医药网';
		$keywords='医药代理,发布需求,意向产品,保健品,OTC,医疗器械';
		$description='3156医药网为您提供医药代理平台,在这里发布药品代理信息,让药企主动来找您。';
		$this->setSEO($title,$keywords,$description);
				
				
				
			 $this->preparRow($row);
     		 return $this->template();
     	}
     	//详情页
     	function pageMsg($inPath){
     		 $id=intval($this->request['id']);
     		 $type=$id > 10000000 ? 2 : 1; //$this->request['t'];
		 		
     		 $ru=$this->getCompanyFromSession(0); 
    		 $this->ass('ru', $ru);    		     
     // 	   print_r($ru);		
     		 $cid=$ru['cid'];
     	 	 $uid=$ru['uid'];
     	 	 $cvip=$ru['cvip'];
     		 $this->ass("cid", $cid); 
     		 $this->ass("uid", $uid);
     		 $this->ass("cvip", $cvip);
     		     		 
       //获得一级产品类型
    	$mdl_pdtype=new Model_pdt_pdtTypes();
    	$data=$mdl_pdtype->getListAlltypeone($page);
    	$tplist=$data['list'];
    	#$this->ass('tplist', $tplist);
    	
     		 

     		 
     // 代理商和厂商 及留言的各种逻辑判断
     		 // 1.游客。。。
     		 // 2.判断是否是代理商自己留的言
     		 
            if($type==1){
            	$mdl_books = new Model_agent_books();           	 
            	$row=$mdl_books->getRowById($id);            	            	
		/*
            	if(!$row['qq']){
            		$row['qq']='';
            	}
            	if(!$row['fax']){
            		$row['fax']='';
            	}
		*/
 	            	
            }else{
            	$mdl_cmt=new Model_cmt_comments();
            	$row=$mdl_cmt->getRowById($id);
		/*
            	if(!$row['qq']){
            		$row['qq']='';
            	}
            	if(!$row['fax']){
            		$row['fax']='';
            	} 
		*/          	                  
           }
	   if(!$row){return false;}
       
       
       
       if($row['pdt_type_names'])
       {
            $pdt_type_name=$row['pdt_type_names'];
       }
       else
       {
           foreach ($tplist as $v)
           {
               $tplist_temp[$v['pt_id']]=$v;
           }

           $pdt_type_name = $tplist_temp[$row['pdt_type1']]['pt_name'];
           if(!$pdt_type_name)
           {
               $pdt_type_name = $tplist_temp[$row['pdt_types']];  
           }
       }

      $this->ass('pdt_type_name',$pdt_type_name);    
       
       
	   
	   $this->ass('user_id',$row['user_id']);          
          
          print_r($type);
           print_r("----");
           print_r('留言id:');
           print_r($row['cmt_id']);
           print_r("----");
           print_r(date('Y-m-d H:i:s',$row['dateline']));
           print_r("----");
           print_r(date('Y-m-d H:i:s',NOW));
           print_r("----");
           print_r((NOW-$row['dateline'])/86400);
           print_r("----");
           
      //判断是否是普通会员自己的留言        
            
     		 $mdl_user=new Model_user_user();
     		 $user=$mdl_user->getRowById($ru['uid']);
     		     				 
     		 $mdl_cmp=new Model_cmp_company();
     		 $row_cmp=$mdl_cmp->getRowById($ru['cid']);
    		 $this->ass('row_cmp', $row_cmp);
    		 
 		 print_r($row_cmp['cmp_lv']);
        	 print_r("----");
    		 
    		 //意向公司信息
    		 $row_cmp2=$mdl_cmp->getRowById($row['cmp_id']); 
    		if($row_cmp2['cmp_type']==6){  		
    		 print_r('意向公司是收费的');
    		}elseif($row_cmp2['cmp_type']!=6){
    			print_r('意向公司是免费的');
    	    }else{
    	    	print_r('无意向公司');
    	    }
 		 
    		 
    		 
    		 
     		  
     		 $b=array(1=>"连锁药店",2=>"批发物流",3=>"零售终端",4=>"医药临床",5=>"会议营");
     		 $a=explode(',', $row['channel']);     	
  //   		 print_r($a);
    // 		 foreach ( $a as $v ){
     		 	
   //  		 	echo $b[$v]."、";
   //  		 }
     		 
     		 $this->preparRow($row);
     		 $param['user']=$user;
     		 $param['row']=$row;
     		 $param['com']=$com;
     		 $param['a']=$a;
     		 $param['b']=$b;
	     		  	   
                       
                         		
	      $title="{$pdt_type_name}_{$row['pdt_name']}{$row['pdt_names']}_医药代理_保健品代理_OTC代理_医疗器械代理-3156医药网";
	      $keywords="{$pdt_type_name},{$row['pdt_name']}{$row['pdt_names']},3156医药网,全国药品网";
	      $description="3156医药网为您提供{$pdt_type_name}{$row['pdt_name']}{$row['pdt_names']}代理。";
	      $this->setSEO($title,$keywords,$description);
	     		 
     		return $this->template($param);
     	}
     	
     	//默认选中设置
     	private function preparRow($row)
     	{
     		if($row)
     		{
     			//选中设置
     			$agent_type_checked=array($row['agent_type']=>"checked='checked'");
     			$end_time_checked=array($row['end_time']=>"checked='checked'");
     			$channel_checked=array($row['channel']=>"checked='checked'");
     			 
     			$list=explode(',', $row['channel']); //0=>1,1=>2,2=>3
     //			foreach ($list as $v)
     //			{
    // 				$list[$v]="checked='checked'";
     //			}
            
     	
     			$this->ass('agent_type_checked',$agent_type_checked);
     			$this->ass('end_time_checked',$end_time_checked);
     			$this->ass('channel_checked',$list);
     			$this->ass('row',$row);
     	
     		}
     	}
     	
     	//发布成功
     function pageSuccess($inPath){
     	
 //    	$s_kwm=trim($this->request['m']);
     	 
 //    	if(!$s_kwm)
  //   	{
  //   		$this->redirect('http://www.3156.cn/404.shtml');
  //   	}
     	
     	$srv_bkw = new SService('Service_block_keywords');
     	$mdl_bkw=new Model_block_keywords();
     	$mdl_bkd=new Model_block_keydata();
     	$mdl_pdt=new Model_pdt_products();
     	
     	$list_bkw = $srv_bkw->getRowsetAll();
     	$list_types=$mdl_bkw->getTypes();
     	
     	$bkw_name=$mdl_bkw->getNameByMark($s_kwm);
     	
     	$srow=array('kwm'=>$s_kwm);
     	
     	$list_bkd=$mdl_bkd->getRowsetAll($srow);
     	
     	foreach ($list_bkd as $k=>$v)
     	{
     		$list_bkd[$k]['cmp_static_url']=$this->buildGotoUrl($v);
     		$rowset = $mdl_pdt->getRowsetByCmpId($v['cmp_id']);
     		 
     		foreach ($rowset as $k2=>$v2) {
     			$rowset[$k2]['static_url']=$this->buildGotoUrl($v2);
     		}
     		 
     		$list_bkd[$k]['recruit']=$rowset;
     	}
     	
     	
     	
     	$mark_selected[$s_kwm]='class="choose"';
     	
     	
     	
     	$params['bkw_name']=$bkw_name;
     	$params['list_bkw']=$list_bkw;
     	$params['list_types']=$list_types;
     	$params['list_bkd']=$list_bkd;
     	$params['mark_selected']=$mark_selected;
     	
     	
     	$title="{$bkw_name}药品招商代理信息_{$bkw_name}医药招商企业信息_全国药品网-3156医药网";
     	$keywords="{$bkw_name}药品招商,{$bkw_name}医药招商,{$bkw_name}药品代理,{$bkw_name}企业信息,3156药品招商网";
     	$description="3156医药网是一家专业的{$bkw_name}药品招商信息发布平台,提供全面的{$bkw_name}药品招商代理信息和{$bkw_name}医药招商企业药品信息,为药品生产企业和药品代理商搭建一座沟通的桥梁,欢迎医药代理商前来咨询和洽谈。";
     	$this->setSEO($title,$keywords,$description);
     	
     		

     
       return $this->template($params);
     }
     
     //组合goto路径
     private function buildGotoUrl($row)
     {
     	return GOTO_URL.
     	'-url-http://www.3156.cn/'.$row['cmp_static_url'].
     	'-mid-2'.
     	'-ktid-'.$row['bkw_type'].
     	'-kwid-'.$row['bkw_id'].
     	'-kdid-'.$row['bkd_id'];
     }
     /**
      *
      * 判断各种等级的会员查看代理信息的时间
      * @param $dateline,$cmp_lv  时间  会员等级
      * @return int|false
      */
     private function getlookstate($dateline,$cmp_lv){
     	
	     	if($cmp_lv==2){         //判断铜牌会员是否能查看信息
	     		  $time=NOW; 
	     		  $time_temp=date('Y-m-d',$time);
	     		  $time=strtotime($time_temp);
	     		  $dateline_temp=date('Y-m-d',$dateline);
	     		  $dateline=strtotime($dateline);
	     		  if($time-$dateline>=86400*5){
	     		  	 return 1;
	     		  }else{
	     		  	 return 2;
	     		  }
	     		 
	     	}elseif($cmp_lv==3){     //判断银牌会员是否能查看信息
	     		$time=NOW;
	     		$time_temp=date('Y-m-d',$time);
	     		$time=strtotime($time_temp);
	     		$dateline_temp=date('Y-m-d',$dateline);
	     		$dateline=strtotime($dateline);
	     		if($time-$dateline>=86400*3){
	     			return 1;
	     		}else{
	     			return 2;
	     		} 
	     		 
	     	}elseif($cmp_lv==4){      //判断金牌会员是否能查看信息
	     		$time=NOW;
	     		$time_temp=date('Y-m-d',$time);
	     		$time=strtotime($time_temp);
	     		$dateline_temp=date('Y-m-d',$dateline);
	     		$dateline=strtotime($dateline);
	     		if($time-$dateline>=86400*2){
	     			return 1;
	     		}else{
	     			return 2;
	     		} 
	     		 
	     	}elseif($cmp_lv==5){      //判断钻石会员是否能查看信息
	     		  
	     		$time=NOW;
	     		$time_temp=date('Y-m-d',$time);
	     		$time=strtotime($time_temp);
	     		$dateline_temp=date('Y-m-d',$dateline);
	     		$dateline=strtotime($dateline);
	     		if($time-$dateline>=86400){
	     			return 1;
	     		}else{
	     			return 2;
	     		}
	     	}    
     } 
     
     /*
      * 通过ajax得到查看的信息
      * 
      */	
     
     function  pagelookmsg(){  
     	  	
     	$spwd=$this->request['spwd']; 
     	$time=$this->request['time'];
     	$msgid=$this->request['msgid'];
     	
     	//通过接收的留言id（$msgid） 判断该条留言的意向公司是否为收费厂商
     	$mdl_cmp=new Model_cmp_company();
     	$row_cmpmsg=$mdl_cmp->getRowById($msgid);
     	
     	
     	
     	$ru=$this->getCompanyFromSession(0);
     	$mdl_user=new Model_user_user();
     	$user=$mdl_user->getRowById($ru['uid']);
     	
     	$mdl_cmp=new Model_cmp_company();
     	$row_cmp=$mdl_cmp->getRowById($ru['cid']);
     	
     	$t=NOW;
     	
     	$lookstate=$this->getlookstate($time, $row_cmp['cmp_lv']);
    	    	
     	$mdl_user=new Model_user_user();     	
     	$res=$mdl_user->getRowById($ru['uid']);
     	
     	if($res['user_pwd2']==md5($spwd)){     		
     		// $this->showMessage('密码正确',1,'_reload');
     		 $data=array('status'=>1,'info'=>"密码正确",'lookstate'=>$lookstate,'err'=>'密码不正确','time'=>$time,'cmp_lv'=>$row_cmp['cmp_lv'],'cmp_type'=>$row_cmpmsg['cmp_type'],'msgid'=>$msgid);
             echo json_encode($data);                                                         //密码正确
     	}else{
     		    		
     		 $data=array('status'=>$type,'info'=>$msg,'tourl'=>$tourl,'timeout'=>$timeout);
             echo json_encode($data);                                                                                //密码错误
     	//	$this->showMessage('密码错误',3);
     	}
     	     	
     	
     }
     
     //提示消息页面
     function pagetip(){
     return $this->template();
     }
     
     function pagetipyp(){
     	return $this->template();
     }
        
}
