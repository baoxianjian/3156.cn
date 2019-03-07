<?php
/**
 * @author tong
 * @time 2014-2-11 11:28:13 
 */

header("Content-type:text/html;charset=utf8");
class Controller_search extends Controller_basepage {
	
	/**
	 * 搜索首页视图方法
	 * @return Ambigous <string, void, string>
	 */
	public function pageIndex(){
		$status = isset($_GET['status']) ? $_GET['status'] : 'sreach_list';//默认搜索产品
		return $this->render("search/index.html",array('status'=>$status));//传递搜索类型
	}
	
	
	/**
	 * 搜索产品列表视图方法
	 */
	public function pageSreach_list(){
		$seek = htmlspecialchars($_GET['seek'],ENT_QUOTES);
		$listData = 8;//每页显示数据条数
		$seekPage = ($_GET['start']-1) * $listData;
		$pageStr = isset($_GET['start']) ? "start=".htmlspecialchars($seekPage,ENT_QUOTES)."&rows=".$listData : "start=0&rows=".$listData ;//API分页请求字符串设置
		//$contentJson = file_get_contents("http://211.147.6.220:18087/solr/product/select?q=".urlencode($seek)."&wt=json&".$pageStr);//获取api搜索Json数据并解析
		
		//====================== CURL API请求数据 Start ==========================//
			$ch = curl_init();//开启curl
			curl_setopt($ch, CURLOPT_URL, "http://211.147.6.220:18087/solr/product/select?q=id%3A85929&wt=json&indent=true");//get请求api
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$contentJson = curl_exec($ch);//执行并读取
			$contentJson = json_decode($contentJson,true);//数据json解析
            
          //  exit("http://211.147.6.220:18087/solr/product/select?q=".urlencode($seek)."&wt=json&".$pageStr);
			//die(SUtil::P($contentJson));
		//====================== CURL请求数据  End===============================//
		
		//====================== CURL API请求相关搜索 Start ====================//
			curl_setopt($ch, CURLOPT_URL,"http://211.147.6.220:18087/solr/keywords/select?q=".urlencode($seek)."&wt=json");//get请求api
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$keyJson = curl_exec($ch);//执行并读取
			$keyJson = json_decode($keyJson,true);
			//die(SUtil::P($keyJson));
		//====================== CURL请求相关搜索  End=========================//
		
		//====================== CURL API请求推广 Start ====================//
			$generalize_num = 4;
			curl_setopt($ch, CURLOPT_URL,"http://211.147.6.220:18087/solr/spread/select?q=".urlencode($seek)."&sort=order+desc&wt=json&rows=".$generalize_num);//get请求api
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$generalizeJson = curl_exec($ch);//执行并读取
			curl_close($$ch);
			$generalizeJson = json_decode($generalizeJson,true);
	//		die(SUtil::P($generalizeJson));
		//====================== CURL请求推广  End=========================//		
	//	die(SUtil::P($contentJson).SUtil::P($generalizeJson));
		//推广、产品数据合并
		if ( $_GET['start'] == 1 ){
		/* 暂注释 	$contentJson['response']['docs'] = array_merge($generalizeJson['response']['docs'], $contentJson['response']['docs']);//合并推广与产品数组
			$contentJson['highlighting'] = array_merge($contentJson['highlighting'],$generalizeJson['highlighting']); */
		}
		//die(SUtil::P($contentJson));
		//===================== 封装Json数据 Start ================================//
			foreach ( $contentJson['response']['docs'] as $k=>$v ){
				if ( isset($contentJson['highlighting'][$v['id']]) && $contentJson['highlighting'][$v['id']] != NULL ){//高亮替换
					foreach ( $contentJson['highlighting'][$v['id']] as $k2=>$v2 ){
						$contentJson['response']['docs'][$k][$k2] = $v2[0];
					}
					$v['function'] == "" && $contentJson['response']['docs'][$k]['function'] = '暂无商品简介';
					$contentJson['response']['docs'][$k]['static_url'] = "http://www.3156.test".$v['static_url'];//重组连接地址
					$contentJson['response']['docs'][$k]['img'] = !empty($contentJson['response']['docs'][$k]['img']) ? "http://www.3156.test/".$v['img'] : NULL;
					mb_strlen( $v['content'],'utf8') > 100 && $contentJson['response']['docs'][$k]['content'] = mb_substr($v['content'], 0, 100, 'utf8')."...";//搜索内容截取
					mb_strlen( $v['name'],'utf8') > 30 && $contentJson['response']['docs'][$k]['name'] = mb_substr($v['name'], 0, 30, 'utf8')."...";//搜索标题截取
					
				}
			}
			unset($contentJson['highlighting']);//卸载高亮
//			die(SUtil::P($contentJson));
	//===================== 封装Json数据 End ====================================//
	
		//获取分页
	//	$generalizeJson['response']['numFound'] < $generalize_num && $generalize_num = $generalizeJson['response']['numFound'];//获取推广条数
		$totalRows = $contentJson['response']['numFound'];
		$pageShow = SUtil::Page($totalRows,$listData);
		$contentJson['response']['numFound'] == NULL && $contentJson['response']['numFound'] = 0;
			
		//=================== 关键字保存 Start ==============================//
			 if ( !isset($_GET['start']) ){
				$key_db = new Model_search_searchkeywords();
				//$key_db->setCache(false);
				$results = $key_db->getOne("kw_name='{$seek}'",'search_count');
				$results != NULL ? $key_db->update("kw_name='{$seek}'", array("search_count"=>$results['search_count']+1)) : $key_db->insert(array("kw_name"=>$seek,"search_count"=>1,"is_delete"=>0));
			}  
		//=================== 关键字保存 End ============================//
			
		return $this->render("search/serach_list.html",array(
				'contentJson'=>$contentJson['response']['docs'],
				"numFound"=>$contentJson['response']['numFound'],
				"pageShow"=>$pageShow,
				'keyJson'=>$keyJson['response']['docs'],
				'seek'=>$_GET['seek']
		));
	}
	
	/**
	 * 搜索公司列表视图
	 */
	public function pageCompany_list(){
		return $this->render("search/company_list.html");
	}
	
} 