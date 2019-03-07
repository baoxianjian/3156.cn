<?php
/**
* @name: 后台主页控制器
* @author: baoxianjian
* @date: 09:32 2015/3/21
*/

class Controller_main extends Controller_basepage {
		
	/**
	 * 后台首页
	 * @param unknown $inPath
	 * @return Ambigous <string, void, string>
	 */
	function pageIndex($inPath){
		return $this->render($this->tplFilePath);
	}

    /**
     * 控制器主体frame top视图
     */
    public function pageTop(){
    	
        $this->ass('sa_name',$this->saName);
        return $this->template();
    }
    
    /**
     * 控制器主体 frame left视图
     */
    public function pageLeft(){
        return $this->render($this->tplFilePath); 
    }
	
    public function pageLeft2(){
        return $this->render($this->tplFilePath); 
    }
	
	public function pageLeft3(){
        return $this->render($this->tplFilePath); 
    }
	
	public function pageLeft4(){
        return $this->render($this->tplFilePath); 
    }
	
	public function pageLeft5(){
        return $this->render($this->tplFilePath); 
    }
    
    /**
     * 控制器主体 frame center视图
     */
    public function pageCenter(){
        return $this->render($this->tplFilePath); 
    }
    
    
    /**
    * 同步数据
    * 
    */
    public function pageSyn()
    {
        return $this->template();
    }
    
    
    
  
    
    
} 
