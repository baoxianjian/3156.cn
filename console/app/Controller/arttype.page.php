<?php
/**
* @name: 资讯分类
* @author: wukai
* @date: 08:32 2015/4/13
*/        

class Controller_arttype extends Controller_basepage {
    
	//列表页
	function pageList($inPath){
            #得到当前页码
            $page=$this->getPageNumber();

            #实例化 分类名称 数据模型
            $mdl_arttype = new Model_news_type();
            $mdl_arttype->setCache(false);


            #搜索
            $srow['id']=$this->request['id'];
            $srow['name']=$this->request['name'];
            if(!empty($srow['id']) || !empty($srow['name'])){
                $condition = 'is_del = 0';
                if(!empty($srow['id'])){
                    $condition .= ' and nt_id = '.$srow['id'];
                }
                if(!empty($srow['name'])){
                    $condition .= ' and name like "%'.$srow['name'].'%"';
                }
            }else{
                $condition = array('is_del'=>0, 'parent_id'=>0);
            }
            
            #得到搜索列表
            $data = $mdl_arttype->getListAll($page, $condition);
			$data['count'] = $mdl_arttype->count($condition);
            #数据临时处理
            $list=$data['list'];
            foreach($list as $key=>$value){
                $list[$key]['dateline'] = SUtil::formatTime($value['dateline']);
                $list[$key]['del_url'] = $url=SRoute::createUrl('arttype/del', array('page' => $page,'id'=>$value['nt_id']), $this->request);
                $subdata = $mdl_arttype->getListAll($page, array('is_del'=>0, 'parent_id'=> $value['nt_id']));
                $sublist = $subdata['list'];
                foreach($sublist as $k=>$val){
                    $list[$key]['sublist'][$k] = $val;
                    $list[$key]['sublist'][$k]['dateline'] = SUtil::formatTime($val['dateline']);
                    $list[$key]['sublist'][$k]['del_url'] = $url=SRoute::createUrl('arttype/del', array('page' => $page,'id'=>$val['nt_id']), $this->request);
                }
            }
            #设置好要放入模版的数据（变量）
            $param[LIST_VAR_NAME] = $list;
            $param[PAGE_VAR_NAME] = $this->pageBar($data['count'], $page, $inPath);

            $param['srow']=$srow;

            return $this->template($param);
	}
    
        //增加
	function pageEdit(){
            $id=intval($this->request['id']);
            $mdl_arttype = new Model_news_type();
            $savetype = $this->request['type'];
            //如果有保存请求
            if($this->request['_ac']=='save'){
                $row = $this->request['row'];
				if(empty($row['en_name'])){
					$row['en_name'] = SString::getFirstChars($row['name']);
				}
				$isdata = $mdl_arttype->getRowByEn($row['en_name'], $id);
				if($isdata['nt_id']){
					$this->showMessage('别名已经存在！');
				}
                $passed = true;    
                if($id && $passed){
					
                    //修改
                    $this->ass('TIP_NAME','修改');
                    $result=$mdl_arttype->updateRowById($row,$id);
                    if($result){      
                        $this->showMessage('修改成功');
                    }else{
                         $this->showMessage('修改失败',3); 
                    }
                }else if($passed){
                    //添加
                    $this->ass('TIP_NAME','添加');
					if(empty($row['name'])){
							$this->showMessage('分类名称不能为空',3);
					}else if(empty($row['seo'])){
							$this->showMessage('SEO标题不能为空',3);
					}else if(empty($row['keywords'])){
							$this->showMessage('关键词不能为空',3);
					}else if(empty($row['desc'])){
							$this->showMessage('描述不能为空',3);
					}
                    $row['dateline']=NOW;
                    $result=$mdl_arttype->addRow($row); 

                    if($result){
                        $this->showMessage('添加成功'); 
                    }else{
                        $this->showMessage('添加失败'); 
                    }
                }   
            }else{
                if($id){
                    //修改初始化
                    $row=$mdl_arttype->getRowById($id);
                    if($savetype != "sub"){
                        $this->ass('TIP_NAME','修改');
                        $this->ass('AC_NAME','modify');
                    }

                }else{
                    //添加初始化
                    $this->ass('TIP_NAME','添加');
                    $this->ass('AC_NAME','add');

                }  
            }
            if($row){
                $this->ass('row',$row);
            }


            $this->ass('id', $id);
            $this->ass('savetype', $savetype); 
            return $this->template();
	}
    
	/**
	* 删除
	* 
	*/
	function pageDel(){
		$mdl_arttype = new Model_news_type();
		$id=$this->request['id'];
		$ids=$this->request['check'];
		if(is_array($ids)){
			//删除
			$mdl_arttype->deleteRowById($ids, array('is_del'=>1)) > 0 ? ( SUtil::isAjax() ? die(json_encode(array('status'=>1,'info'=>'删除成功'))) : SUtil::success('删除成功') ) : ( SUtil::isAjax() ? die(json_encode(array('status'=>0,'info'=>'删除失败'))) : SUtil::error('删除失败') );
		}else{
			$result=$mdl_arttype->deleteRowById($id);
			if($result){
				$this->showMessage('删除成功',1,'_reload');
			}else{
				$this->showMessage('删除失败',3);
			}
			return $this->template();
		}
	}
	
	
	//列表页
	function pageListjson(){
		#实例化 分类名称 数据模型
		$get['jsoncallback'] = $this->request['callback'];
		$mdl_arttype = new Model_news_type();
		$mdl_arttype->setCache(false);
		$condition = array('is_del'=>0, 'parent_id'=>0);
		$data = $mdl_arttype->getListAll($page, $condition);
		#数据临时处理
		$list=$data['list'];
		$jsondata = array();
		foreach($list as $key=>$value){
			$jsondata[$value['nt_id']]['name'] = $value['name'];
			$subdata = $mdl_arttype->getListAll($page, array('is_del'=>0, 'parent_id'=> $value['nt_id']));
			$sublist = $subdata['list'];
			foreach($sublist as $k=>$val){
				$jsondata[$value['nt_id']]['items'][$val['nt_id']]['name'] = $val['name'];
				$jsondata[$value['nt_id']]['items'][$val['nt_id']]['flag'] = $val['type_flag'];
			}
		}
		#设置好要放入模版的数据（变量）
		echo SUtil::apiOut($jsondata, '', $get);
		exit();
	}
	
	function pageTcount(){
		$id = $this->request['id'];
		#实例化 分类名称 数据模型
		$mdl_arttype = new Model_news_type();
		$mdl_article_news = new Model_news_news();
		$row = $mdl_arttype->getRowById($id);
		if($row['parent_id']){
			$counts = $mdl_article_news->count(array('type_id2'=>$row['nt_id']));
			$mdl_arttype->updateRowById(array('count'=>$counts), $row['nt_id']);
		}else{
			$counts = $mdl_article_news->count(array('type_id1'=>$row['nt_id']));
			$mdl_arttype->updateRowById(array('count'=>$counts), $row['nt_id']);
		}
		$jsondata = array('msg'=>'统计成功', 'data'=>$counts, 'status'=>true);
		echo SUtil::getJson($jsondata);
		exit();
	}
}
