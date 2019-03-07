<?php 
	define('SYS_NAME','pdt');  
	/**
	 * 产品控制器
	 * @author Administrator
	 *
	 */
	header('Content-type:text/html;charset=utf-8');
	class Controller_product extends Controller_basepage {
		
		/**
		 * 药品库视图
		 */
		public function pageIndex($inPath){
            global $srow;
            
            $page = $this->getPageNumber(); 
            
			//地区
			$mdl_area=new Model_com_area();
			$area_list = $mdl_area->getAllRowset(false,false);
            $this->ass('area_list',$area_list);
						
			//药剂类型
			$mdl_pdt = new Model_pdt_products();
			$md_type_list = $mdl_pdt->getMedicamentTypes();
            $this->ass('md_type_list',$md_type_list);
            
            //中标类型
            $zb_type_list = $mdl_pdt->getZbTypes();
            $this->ass('zb_type_list',$zb_type_list);
			
            //医保类型
            $medicare_type_list = $mdl_pdt->getMedicareTypes();
            
            
			//产品类型。
			$mdl_pdtype=new Model_pdt_pdtTypes();

			$list_pdtype=$mdl_pdtype->getListAlltp();
			
			foreach ($list_pdtype['list'] as $v)
			{
				$list_pdtype_1[$v['pt_id']]=$v;
			}

			foreach ($list_pdtype['list'] as $v)
			{
				$list_pdtype_temp[$v['parent_id']][]=$v;
			}
			$this->ass('list_pdtype',$list_pdtype_temp);
            
            #处理搜索 
            $s_type1_id=intval($this->request['t1']);      //$row['type1_id']
            $s_type2_id=intval($this->request['t2']);
            $s_area=trim($this->request['area']);    
            $s_mdt= $this->request['mdt'];   // medicament_type
            $s_k=trim($this->request['k']);
            $s_f=$this->request['f'];
            
            $srow=array('t1'=>$s_type1_id,'t2'=>$s_type2_id,'area'=>$s_area,'mdt'=>$s_mdt,'k'=>$s_k,'f'=>$s_f);
             
            $s_type1_name = $list_pdtype_1[$s_type1_id]['pt_name'];
            $s_type2_name = $list_pdtype_1[$s_type2_id]['pt_name']; 
            
            $this->ass('s_type1_name',$list_pdtype_1[$s_type1_id]['pt_name']);
            $this->ass('s_type2_name',$list_pdtype_1[$s_type2_id]['pt_name']);   
            $this->ass('srow',$srow);
            
            //$remote_content = SRemote::getHtmlContent('http://211.147.6.220:18087/solr/productall/select?q=%E8%8D%AF%E5%93%81&wt=json');
            
            
            $r_data=$mdl_pdt->searchListAll($page,$srow,12);
            $r_list=$r_data['list'];

           
            foreach ($r_list as $k=>$v) 
            {     
                $r_list[$k]['name_temp']=$v['name'];
                
           
                $r_list[$k]['spec']=str_replace(' ',null,$v['spec']);       
   
                if($v['zb_type']!=3)
                {
                    $r_list[$k]['zb_type_temp']=$zb_type_list[$v['zb_type']];
                }
           
                $r_list[$k]['medicament_type']=trim($v['medicament_type']);
                if(intval($v['medicament_type']))
                {
                    $r_list[$k]['medicament_type']=$md_type_list[$v['medicament_type']];
                }
                if(intval($v['medicare_type']))
                {
                    $r_list[$k]['medicare_type']=$medicare_type_list[$v['medicare_type']];                    
                }
                //$cmp_id=$list[$i]['cmp_id'];
                //$res=$mdl_cmp->getRowById($cmp_id);
               // $list[$i]['cmp_name']=$res['cmp_name'];
                //$list[$i]['type_name_1']=$list_pdtype_1[$list[$i]['type1_id']];
               // $list[$i]['type_name_2']=$list_pdtype_1[$list[$i]['type2_id']];
               // $list[$i]['type_name_3']=$list_pdtype_1[$list[$i]['type3_id']];
                
                         
            }
             // $data = $mdl_pdt->getListAllpdt($page,$srow);      

	    
           // print_r($r_list);
           #设置好要放入模版的数据（变量）
            $param[LIST_VAR_NAME] = $r_list;
            $param[PAGE_VAR_NAME] = $this->pageBar($r_data['count'], $page, $inPath, $limit=12, $style = 'style1', false);
                       

           $title="{$s_area}_{$s_type1_name}_{$s_type2_name}_{$s_mdt}药品招商_产品信息_医药企业信息_产品分类搜索_3156医药网_全国药品网";
           $keywords="{$s_area}_{$s_type1_name}_{$s_type2_name}_{$s_mdt}医药招商,药品招商,OTC招商,处方药招商,保健品招商,医疗器械,全国药品网";
           $description="3156医药网是专业的医药招商平台,提供综合全面的招商产品信息和企业信息。3156作为医药信息服务商,提供{$area}{$type1_id}{$medicament_type}医药招商、保健品招商、药品招商、药妆招商等信息,为您提供最贴心的招商服务,保证让您招商满意！";

           
            $this->setSEO($title,$keywords,$description); 
          
            return $this->template($param);
	
    /*
			//产品主表model
			$mdl_pdt=new Model_pdt_products();  
        	$page=$this->getPageNumber($inPath);
        	//$testModel->setReaddb('count');
			$mdl_pdt->setCache(false);       		 
        	
		
			
			//$main_products=$this->request['main_products'];
			$type1_id=$this->request['type1_id'];
			$type2_id=$this->request['type2_id'];
			$area=$this->request['area'];	
			$medicament_type=$this->request['medicament_type'];	
            $k=$this->request['k'];
            $f=$this->request['f'];
           
            //得到药品类别名称
            $res1=$mdl_pdtype->getRowById($type1_id);
            $res2=$mdl_pdtype->getRowById($type2_id);
            $drug1_type= $res1['pt_name'];
            $this->ass('drug1_type', $drug1_type);
            $drug2_type= $res2['pt_name'];
            $this->ass('drug2_type', $drug2_type);
			//创建公司类
            $mdl_cmp=new Model_cmp_company();
            
			$srow=array('type1_id'=>$type1_id,'type2_id'=>$type2_id,'area'=>$area,'medicament_type'=>$medicament_type,'k'=>$k,'f'=>$f);
            $data = $mdl_pdt->getListAllpdt($page,$srow);
             	       	
        	$list=$data['list'];
        	
        	
        
        	for($i=0;$i<count($data['list']);$i++)
        	{
        		$this->request['id']=$list[$i]['pdt_id'];        		
        		$list[$i]['name_temp']=SString::tcutstr($list[$i]['name'], 10);
        		$cmp_id=$list[$i]['cmp_id'];
        		$res=$mdl_cmp->getRowById($cmp_id);
        		$list[$i]['cmp_name']=$res['cmp_name'];
        		$list[$i]['type_name_1']=$list_pdtype_1[$list[$i]['type1_id']];
        		$list[$i]['type_name_2']=$list_pdtype_1[$list[$i]['type2_id']];
        		$list[$i]['type_name_3']=$list_pdtype_1[$list[$i]['type3_id']];
        		
        				 
        	}
        	

        	
        	
        	#设置好要放入模版的数据（变量）
        	$param[LIST_VAR_NAME] = $list;
        	$param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);
        	$param['srow']=$srow;
        	
        	
        	$this->preparRow($row);	
        	
        	$this->ass('num', $data['count']);
        	$this->ass('ye', ceil($data['count']/12));
        	$this->ass('page', $page);
        	$this->ass('row', $srow);	
        	$this->ass('area',$area_rowset);
        	$this->ass('medicamenttype',$medicamentType);
        	
        	if(count($srow)!=''){
        	           if($type1_id==1){
        	           	$type1_id='OTC';
        	           }elseif ($type1_id==2){
        	           	$type1_id='保健品';
        	           }elseif ($type1_id==3){
        	           	$type1_id='卫生用品';
        	           }elseif ($type1_id==4){
        	           	$type1_id='药妆';
        	           } elseif ($type1_id==5){
        	           	$type1_id='贴剂';
        	           }elseif ($type1_id==6){
        	           	$type1_id='中药材';
        	           }elseif ($type1_id==7){
        	           	$type1_id='中间体';
        	           }elseif ($type1_id==8){
        	           	$type1_id='消毒剂';
        	           }elseif ($type1_id==9){
        	           	$type1_id='原料药';
        	           }elseif ($type1_id==10){
        	           	$type1_id='药用辅料';
        	           }elseif ($type1_id==11){
        	           	$type1_id='药品包装';
        	           }elseif ($type1_id==12){
        	           	$type1_id='诊断试剂';
        	           }elseif ($type1_id==13){
        	           	$type1_id='计生用品';
        	           }elseif ($type1_id==14){
        	           	$type1_id='医疗设备';
        	           }elseif ($type1_id==15){
        	           	$type1_id='医疗器械';
        	           }elseif ($type1_id==16){
        	           	$type1_id='西药产品';
        	           }elseif ($type1_id==17){
        	           	$type1_id='中药产品';
        	           }
        	          
        	          if($f==1){
        	          	 $td='医保药品';
        	          }elseif($f==2){
        	          	$td='中标药品';
        	          }elseif($f==3){
        	          	$td='基本药品';
        	          }elseif($f==4){
        	          	$td='中药保护';
        	          }elseif($f==5){
        	          	$td='专利产品';
        	          }  
        	           
        		       $title="{$area}{$type1_id}{$medicament_type}药品招商_产品信息_医药企业信息_产品分类搜索_3156医药网_全国药品网";
        		       $keywords="{$area}{$type1_id}{$medicament_type}医药招商,药品招商,OTC招商,处方药招商,保健品招商,医疗器械,全国药品网";
        		       $description="3156医药网是专业的医药招商平台,提供综合全面的招商产品信息和企业信息。3156作为医药信息服务商,提供{$area}{$type1_id}{$medicament_type}医药招商、保健品招商、药品招商、药妆招商等信息,为您提供最贴心的招商服务,保证让您招商满意！";
        		
        	
        	}else{  

        		
        		$title='药品招商_产品信息_医药企业信息_产品分类搜索_3156医药网_全国药品网';
        		$keywords='医药招商,药品招商,OTC招商,处方药招商,保健品招商,医疗器械,全国药品网';
        		$description='3156医药网是专业的医药招商平台,提供综合全面的招商产品信息和企业信息。3156作为医药信息服务商,提供医药招商、保健品招商、药品招商、药妆招商等信息,为您提供最贴心的招商服务,保证让您招商满意！';
        	}
 
        	$this->setSEO($title,$keywords,$description);
       	
        	//return $this->render($this->tplFilePath,$param);
        	$this->ass('list_pdtype',$list_pdtype_temp);
			return $this->template($param);
            */
		}
		
		
		/**
		 * 产品对比
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageCompare(){
			
			if ( count($this->request['check']) > 3 ) SUtil::error('对比商品不能超过三个');
			
			if ( count($this->request['check']) < 2 ) SUtil::error('请至少选择两个产品作为对比');
			
			//产品主表model
			$mdl_products = new Model_pdt_mainProducts();
			$mdl_products->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//产品分类model
			$mdl_pdtType = new Model_pdt_pdtTypes();
			$mdl_pdtType->setCache(SUtil::C('sqlCache'));//设置缓存
			
			$questArr = SUtil::html_arr($this->request);
			
			//获取对比数据
			foreach ( $questArr['check'] as $v ){
				
				$product[] = $mdl_products->getProductById($v); 
				
			}
			
			//获取数据分类并重组
			foreach ( $product as &$v ){
				
				$v['type1_id'] = $mdl_pdtType->getParent($v['type1_id']);
				$v['type2_id'] = $mdl_pdtType->getParent($v['type2_id']);
				
				if ( $v['medicare_type'] == 3 ){
					
					$v['medicare_type'] = '医保类型乙类型';
					
				}elseif ( $v['medicare_type'] == 2 ){
					
					$v['medicare_type'] = '医保甲类型';
					
				}else{
					
					$v['medicare_type'] = '非医保类型';
					
				}
				
			}
			
	//		die(SUtil::P($product));
			
			$tplArr = array(
					 
					'product'=>$product
					 
			);
			
			return $this->template($tplArr);
			
		}
		
		
		public function pageProductDetails(){
			
			$questArr = SUtil::html_arr($this->request);
			
			//产品主表model
			$mdl_products = new Model_pdt_mainProducts();
			$mdl_products->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//产品分类model
			$pdtTypes_db = new Model_pdt_pdtTypes();
			$pdtTypes_db->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//获取数据
			$productsDetails = $mdl_products->associatedData($questArr['id']);
		//	die(SUtil::P($productsDetails));
			//获取一级分类
			$productsDetails['type1_id'] = $pdtTypes_db->getParent($productsDetails['type1_id']);
			
			//获取二级级分类
			$productsDetails['type2_id'] = $pdtTypes_db->getParent($productsDetails['type2_id']);
			
			//医保
			if ( $productsDetails['medicare_type'] == 2 ){
				
				$productsDetails['medicare_type'] = '医保甲类型';
				
			}elseif ( $productsDetails['medicare_type'] == 3 ){
				
				$productsDetails['medicare_type'] = '医保乙类型';
				
			}else{
				
				$productsDetails['medicare_type'] = '非医保';
				
			}
			
			//中标
			if ( $productsDetails['zb_type'] == 1 ){
			
				$productsDetails['zb_type'] = '中标';
			
			}elseif ( $productsDetails['zb_type'] == 3 ){
			
				unset($productsDetails['zb_type']);
			
			}else{
			
				$productsDetails['zb_type'] = '不中标';
			
			}
			
		//	die(SUtil::P($productsDetails));
			
			//die(SUtil::P($this->request));
			$tplArr = array(
					
					'list'=>$productsDetails
					
			);
			
			return $this->template($tplArr);
			
		}
		
		
		public function pageAjaxGroup(){
			
			SUtil::isAjax() || die('error');
			
			//产品分类model
			$mdl_pdtTypes = new Model_pdt_pdtTypes();
			$mdl_pdtTypes->setCache(SUtil::C('sqlCache'));//设置缓存
			
			//获取父级id
			$pid = htmlspecialchars($this->request['pid'], ENT_QUOTES);
			
			$pdtTypes_results = $mdl_pdtTypes->getGroup($pid);
			
			die(json_encode($pdtTypes_results['list']));
			
			
		}
		/**
		 * 产品详情页
		 * @return Ambigous <mixed, string, void, string>
		 */
		public function pageInfo(){
				        	  
			 //接收id获得药品信息
			 $id=intval($this->request['id']);
             
             #id不存在
             if(!$id){return false;}

			 $type1_id=$this->request['type1_id'];  
			 $this->ass('type1_id', $type1_id);
	 		 
		//	 $cmp_name=$this->request['na'];
			 $mdl_pdt=new Model_pdt_products(); 
			
			 $row_pdt=$mdl_pdt->getRowById($id);
             
             #记录不存在
             if(!$row_pdt){return false;}
             
	     #已被删除
             if($row_pdt['is_del']==1)
             {
                 return false;
             }
             
             #未通过审核
             if($row_pdt['audit_state']!=2 && $row_pdt['audit_state']!=4)
             {
                 return false;
             }
             
             
             
		//	 $row_pdt['cmp_name']=$cmp_name;
			 $name=$row_pdt['name'];
			 $function=$row_pdt['function'];
			 $label=$row_pdt['label'];		
			 $this->ass("recruit_state", $row_pdt['recruit_state']);
		//	 print_r($row_pdt['recruit_state']);
			
			 
		 //通过产品中的cmp_id找到该公司其他产品	 
			 $data= $mdl_pdt->getListAllnewpdt($page,$row_pdt['cmp_id'],$id);			 
			 $list_newpdt=$data['list'];
			 $this->ass('newpdt', $list_newpdt);
			 
		//调出14个和该页面上的产品同类别的其他产品
            // $tpdata= $mdl_pdt->getListAlltypepdt($page,$type1_id,$row_pdt['cmp_id']);
             $list_typepdt=$tpdata['list'];           
       //将产品对应的公司名存入列表中   
       
    
             for($i=0;$i<count($tpdata['list']);$i++)
             {                                     
               $cmp_id=$list_typepdt[$i]['cmp_id'];
               $mdl_cmp=new Model_cmp_company();
	        
               $res=$mdl_cmp->getRowById($cmp_id);
	  
               $list_typepdt[$i]['cmp_name']=$res['cmp_name'];  
               $list_typepdt[$i]['cmp_id']=$res['cmp_id'];
             }
                          
             $this->ass('typepdt', $list_typepdt);
                        
			 $cmp_id=$row_pdt['cmp_id'];
			 $mdl_cmp=new Model_cmp_company();
			 $row_cmp=$mdl_cmp->getRowById($cmp_id);
			 $row_pdt['cmp_name']=$row_cmp['cmp_name'];
             $row_pdt['city_name']=$row_cmp['city_name'];
             $row_pdt['cmp_addr']=$row_cmp['cmp_addr'];
             $row_pdt['fax']=$row_cmp['fax'];
              
             
			 $cmp_name=$row_pdt['cmp_name'];
			 $type1_id=$row_pdt['type1_id'];
             $label=$row_pdt['label'];
             

             
             $row_pdt['postcode']=$row_cmp['postcode']; 
             
             if(!$row_pdt['link_tel'] && !$row_pdt['link_man']  && !$row_pdt['link_mp']) //独立显示没得 则 使用公司联系方式
             {
                 $row_pdt['link_cmp_name']=$row_cmp['cmp_name'];
                 $row_pdt['link_tel']=$row_cmp['telephone'];
                 $row_pdt['link_mp']=$row_cmp['mobile'];
                 $row_pdt['link_email']=$row_cmp['cmp_email'];
                 $row_pdt['link_man']=$row_cmp['link_man'];
                 $row_pdt['postcode']=$row_cmp['postcode']; 
                 $row_pdt['web_url']=$row_cmp['web_url']; 
             }
	     
	     if(!$row_pdt['link_cmp_name']) //没得联系公司，用公司名
	     {
		     $row_pdt['link_cmp_name']=$row_cmp['cmp_name'];
	     }
	     
			 		
			 $this->ass('row_cmp',$row_cmp);

			 
			 $ru=$this->getCompanyFromSession(0);
			 $cvip=$ru['cvip'];			 			 
			 $this->ass("cvip", $cvip);
			 
	
			 $row_pdt['name_temp']=SString::tcutstr( $row_pdt['name'],50);	
			 $row_pdt['name_stemp']=SString::tcutstr( $row_pdt['name'],8);
             
            if(intval($row_pdt['medicament_type']))
            {
                $md_type_list = $mdl_pdt->getMedicamentTypes(); 
                $row_pdt['medicament_type']=$md_type_list[$row_pdt['medicament_type']];
            }
            /*
            if(intval($row_pdt['medicare_type']))
            {
                $medicare_type_list = $mdl_pdt->getMedicareTypes(); 
                $row_pdt['medicare_type']=$medicare_type_list[$row_pdt['medicare_type']];
            }            
            */
             
             
			 $this->ass('row', $row_pdt);
			 			 
			 
			 if ($type1_id==1){
			 	$type='保健品';}
			 	elseif($type1_id==2){
			 	$type='药品包装';}
			 	elseif($type1_id==5){
			 	$type='中药产品';}
			 	elseif ($type1_id==6){
			 	$type='西药产品';}
			 	elseif ($type1_id==7){
			 	$type='医疗器械 ';}
			 	elseif($type1_id==8){
			 	$type='诊断试剂';}
			    elseif($type1_id==9){
			 	$type='中间体';}
			 	elseif($type1_id==10){
			 	$type='中药材';} 
			 	elseif($type1_id==241){
			 	$type='OTC';}
			    elseif ($type1_id==581){
			    $type='计生用品';}
			    elseif  ($type1_id==621){
			    $type='消毒剂';}
			    elseif ($type1_id==641){
			    $type='药妆';}
			    elseif  ($type1_id==736){
			    $type='原料药';}
			    elseif  ($type1_id==759){
			    $type='药用辅料';}
			    elseif ($type1_id==791){
			    $type='医疗设备';}
			     elseif  ($type1_id==862){
			     $type='贴剂';}
			     elseif  ($type1_id==1162){
			     $type='卫生用品';
			     }
	     	
			 
			 $title="{$name}_{$cmp_name}_3156医药网";
			 $keywords="{$name}招商,{$cmp_name}招商,{$label},{$type}";
			 $description="{$cmp_name}的{$name},{$function}";
			 $this->setSEO($title,$keywords,$description);
			 
			return $this->template();
		}
		
		 //默认选中设置
	     private function preparRow($row)
	     {
	    	  if($row)
	    	    {
	    		  //选中设置
	    		  $f_checked=array($row['f']=>"checked='checked'");
	    		
	    		  $this->ass('f_checked',$f_checked);
	    		
	    		  $this->ass('row',$row);
	    
	    	   }
	       }
           
         
         	
		
		
		
		
		
		
		
		
		
	}
    
    
    
    function purl($cmd)
    {
        global $srow;
        
        $srow2=$srow;

        $params=cmd_parser($cmd);

        foreach ($params as $k=>$v)
        {
            $srow2[$k]=urlencode($v); 
        }
        
        foreach ($srow2 as $k=>$v) 
        {
            if($v)
            {
                $str.='-'.$k.'-'.$v;
            }
        }
        return '/product/index'.$str;
            
        //return "/product/index-type1_id-{|$row['type1_id']|}-type2_id-{|$row['type2_id']|}-area-{|$row['area']|}-medicament_type--f-{|$row['f']|}";
        //$params['first'] = SRoute::createUrl($rule, array('page' => 1), $pars);
           
    }
    
    
	
  //  echo purl($srow,type2_id,'');
  //  exit;
?>
