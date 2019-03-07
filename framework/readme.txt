Library 				数据层及业务逻辑层
Test    				用于单元测试(基于STest.class.php类)，测试业务逻辑中Service类中方法的测试用例
Plugins 				插件类及公用方法等
Tools					一些外来API的包所在目录 例如支付，登陆等
Tpl						公用模板 404页面 公用提示页面 公用分页模板等
SlightPHP.php			主框架程序

框架支持的功能

1、图片跨服务器上传 Imagick图片处理
2、Memcache
3、Redis
4、单元测试
5、Gearman队列
6、验证码
7、rest、soap、rpc


pdo_mysql
数据库读/写/统计 分离（这些都可以在数据库中件间中解决,如没有则使用）
数据库事务处理
MongoDB

模板引擎
	模板引擎里写法和PHP的写法一样，只是<?php ?>换成了{||}而已
	模板方法使用和smarty一样，例如{|$test|default:'0'|}



RabbitMQ暂未添加（如有需要，就会添加）
不重要的任务处理使用 Gearman
列表可以使用redis