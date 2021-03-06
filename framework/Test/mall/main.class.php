<?php
/**
 * Service mall main 单元测试类
 * @author tong
 *
 */
class Test_mall_main extends STest{
	public function testIsOne(){
		$testServ = new Service_mall_main();//要测试的model中的方法
		$this->assertTrue($testServ->isOne('1'));
		$this->assertFalse($testServ->isOne('0'));
	}
	public function testIsTwo(){
		$testServ = new Service_mall_main();//要测试的model中的方法
		$this->assertTrue($testServ->isTwo('2'));
		$this->assertFalse($testServ->isTwo('1'));
	}
}