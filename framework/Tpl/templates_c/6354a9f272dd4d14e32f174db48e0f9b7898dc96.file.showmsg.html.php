<?php /* Smarty version Smarty 3.1.4, created on 2015-03-23 14:10:19
         compiled from "../framework/Tpl\templates\msg\showmsg.html" */ ?>
<?php /*%%SmartyHeaderCode:3188550fae4b8ddcd7-43133862%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6354a9f272dd4d14e32f174db48e0f9b7898dc96' => 
    array (
      0 => '../framework/Tpl\\templates\\msg\\showmsg.html',
      1 => 1426816273,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3188550fae4b8ddcd7-43133862',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty 3.1.4',
  'unifunc' => 'content_550fae4ba26dc',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_550fae4ba26dc')) {function content_550fae4ba26dc($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>跳转提示</title>
</head>
<body>
<div class="system-message">
<h1><!--{if $state==1}-->恭喜您!<!--{else}-->抱歉,出错啦!<!--{/if}--></h1>
<p class="error"><!--{$msg}--></p>
<p class="detail"></p>
<p class="jump">
<b id="wait"><!--{$second}--></b> 秒后页面将自动跳转
</p>
<div>
    <a id="href" id="btn-now" href="<!--{$url}-->">立即跳转</a> 
    <button id="btn-stop" type="button" onclick="stop()">停止跳转</button> 
</div>
</div>
<script type="text/javascript">
(function(){
 var wait = document.getElementById('wait'),href = document.getElementById('href').href;
 var interval = setInterval(function(){
     	var time = --wait.innerHTML;
     	if(time <= 0) {
     		location.href = href;
     		clearInterval(interval);
     	};
     }, 1000);
  window.stop = function (){
         console.log(111);
            clearInterval(interval);
 }
 })();
</script>
</body>
</html>
<?php }} ?>