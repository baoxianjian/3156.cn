<?php
//单元测试类
/**
 * @package STest
 * 用于单元测试.
 * by gongbin
 * 用例:
 * 	class STestbar extends STest {
 * 		public function testBasics(){
 * 			$a = new model_blog;//要测试的model中的方法
 * 			$this->assertFalse($a->getblog('1'));
 * 		}
 * 	}
 * 	print STest::run(array('STestbar'));
 * @精简单元测试框架.
 */
class STest {
	static protected $classes = array();
	static protected $tests = 0;
	static protected $passes = array();
	static protected $fails = array();
	static protected $exceptions = array();
	static protected $failing_methods = array();
	
	/**
     * 运行测试
	 * @param array $classes
	 * @param bool $return_html
	 * @return string
	 */
	static public function run($classes,$return_html = true){
		self::$classes = $classes;
		self::$tests = 0;
		self::$passes = array();
		self::$fails = array();
		self::$exceptions = array();
		self::$failing_methods = array();
		foreach($classes as $class_name){
			++self::$tests;
			self::$passes[$class_name] = 0;
			self::$fails[$class_name] = 0;
			self::$exceptions[$class_name] = array();
			self::$failing_methods[$class_name] = array();
			$methods = get_class_methods($class_name);
			$test_case = new $class_name();
			$test_case->setup();
			foreach($methods as $method_name){
				if(substr($method_name,0,4) == 'test'){
					try{
						$test_case->{$method_name}();
					}catch(Exception $e){
						self::$exceptions[$class_name][$method_name] = $e;
					}
				}
			}
			$test_case->teardown();
		}
		return self::printer($return_html);
	}

    //打印测试结果	
	static protected function printer($return_html){
		$exception_output = '';
		$main_output = '';
		$failing_methods_output = '';
		if($return_html){
			$main_output = '<style>.unit_tests{font-family:sans-serif;width:90%;background:#D0DCE0;color:#000000;margin:0;padding:0.2em 0.2em 0.2em 0.2em;border:1px solid #002777;}li{background:#FF8C38}</style><table class="unit_tests"><tr class="header"><td>测试类名</td><td>成功</td><td>失败</td><td>例外</td></tr>';
			foreach(self::$classes as $class_name){
				$main_output .= '<tr class="'.(self::$fails[$class_name] > 0 || count(self::$exceptions[$class_name]) || 0 ? '失败' : '成功').'"><td class="class_name">'.$class_name.'</td><td>'.self::$passes[$class_name].'</td><td>'.self::$fails[$class_name].'</td><td>'.count(self::$exceptions[$class_name]).'</td></tr>';
				foreach(self::$exceptions[$class_name] as $method_name => $exception)
					$exception_output .= '<h2>'.$class_name.'::'.$method_name.' threw this exception:</h2><hr/>'.Support::exceptionHandler($exception,true).'<hr/>';
			}
			$main_output .= '</table>';
			foreach(self::$failing_methods as $class_name => $fail_info)
				foreach($fail_info as $method_name => $fails)
					foreach($fails as $item)
						$failing_methods_output .= '<li>'.$class_name.'::'.$method_name.' '.$item['method'].' 出错于 '.$item['line'].' 行,位于文件 '.$item['file'].'中</li>';
			if($failing_methods_output != '')
				$failing_methods_output = '<ul>'.$failing_methods_output.'</ul>';
		}else{
			$main_output = 'STest::run()'.chr(10);
			foreach(self::$classes as $class_name){
				$main_output .= chr(9).$class_name.' '.self::$fails[$class_name].' 成功, '.self::$fails[$class_name].' 失败, '.count(self::$exceptions[$class_name]).' 例外.'.chr(10);
				foreach(self::$exceptions[$class_name] as $method_name => $exception)
					$exception_output .= $class_name.'::'.$method_name.' threw this exception:'.chr(10).picora_exception_handler($exception,true,false).chr(10).chr(10);
				foreach(self::$failing_methods[$class_name] as $method_name => $fails)
					foreach($fails as $info)
						$failing_methods_output .= ' - '.$class_name.'::'.$method_name.' '.$info['method'].' 失败于 '.$info['line'].' 行,位于文件 '.$info['file'].chr(10);
			}
		}
		return $main_output.$failing_methods_output.$exception_output;
	}
	
	/**
     * 断言为真
     * 此方法也加入了统计
	 * @param mixed statement
	 * @return void
	 */
	final protected function assertTrue($statement){
		if($statement){
			++self::$passes[get_class($this)];
		}else{
			$debug = debug_backtrace();
			foreach($debug as $i => $trace){
				if($trace['class'] != 'STest'){
					if(!isset(self::$failing_methods[$trace['class']][$trace['function']]))
						self::$failing_methods[$trace['class']][$trace['function']] = array();
					self::$failing_methods[$trace['class']][$trace['function']][] = array(
						'method' => $debug[$i - 1]['function'],
						'file' => $debug[$i - 1]['file'],
						'line' => $debug[$i - 1]['line']
					);
					break;
				}
			}
			++self::$fails[get_class($this)];
		}
	}
	
	/**
     * 断言为假
	 * @param mixed statement
	 * @return void
	 */
	final protected function assertFalse($statement){
		$this->assertTrue((!($statement)));
	}
	
	/**
     * 按预设/假想值进行对比测试,可对比字符、数字、对象、数组等
	 * @param mixed $a
	 * @param mixed $b
	 * @return void
	 */
	final protected function assertEqual($a,$b){
		$this->assertTrue(($a == $b));
	}
	
	/**
     * 对比为假,参数同上
	 * @param mixed $a
	 * @param mixed $b
	 * @return void
	 */
	final protected function assertNotEqual($a,$b){
		$this->assertTrue(($a != $b));
	}
	
	/**
	 * @return void
	 */
	protected function setup(){}
	
	/**
	 * @return void
	 */
	protected function teardown(){}
}

?>
