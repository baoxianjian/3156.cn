// JavaScript Document
$(function(){
	$('.step_ul').eq(0).css('display','block');
	$('.step .old_img').eq(0).css('display','none');
	$('.step .active_img').eq(0).css('display','block');
	$('.step_ul li input').focus(function(e) {
        $(this).css('border','1px solid #f07c00');
    });
/*	//确认账号
	var mark1=false,mark2=false,mark3=false,mark4=false,mark5=false;
	$('.step_ul .name').blur(function(e) {
        if(!($(this).val().length<=20&&$(this).val().length>=5)){
			$('.end').eq(0).text('用户名格式错误').css('color','#cc0000');
		}
		else{
			$('.end').eq(0).text('注册时使用的用户名').css('color','#999999');
			mark1=true;
		}
    });
	$('.step_ul .message').blur(function(e) {
        var regValid=/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/;
		if(!regValid.test($(this).val())){
			$('.end').eq(1).text('用户名格式错误').css('color','#cc0000');
		}
		else{
			$('.end').eq(1).text('注册时使用的邮箱').css('color','#999999');
			mark2=true;
		}
    });
	$('.step_ul input[type=submit]').eq(0).click(function(e) {
		if($('.step_ul .message').val()==''){
		}
		if($('.step_ul .name').val()==''){
		}
		if(mark1&&mark2){
			$('.step .active_img').eq(0).css('display','none');
			$('.step_ul').eq(0).css('display','none');
			$('.step .old_img').eq(0).css('display','block');
			$('.step .old_img').eq(1).css('display','none');
			$('.step .active_img').eq(1).css('display','block');
			$('.step_ul').eq(1).css('display','block');
		}
    });
	$('.step_ul .message1').blur(function(e) {
        if(!($(this).val().length==6)){
			$('.end').eq(2).text('验证码格式错误').css('color','#cc0000');
		}
		else{
			$('.end').eq(1).text('');
			mark3=true;
		}
    });
	$('.step_ul input[type=submit]').eq(1).click(function(e) {
        if($('.step2 .message1').val()==''){
			$('.step_ul .message').focus();
		}
		if(mark3){
			$('.step .active_img').eq(1).css('display','none');
			$('.step_ul').eq(1).css('display','none');
			$('.step .old_img').eq(1).css('display','block');
			$('.step .old_img').eq(2).css('display','none');
			$('.step .active_img').eq(2).css('display','block');
			$('.step_ul').eq(2).css('display','block');
		}
    });
	$('.step_ul .password1').blur(function(e) {
		var pa= /[a-zA-Z0-9]{6,20}/;
        if(!(pa.test($(this).val()))){
			$('.end').eq(3).text('密码格式错误').css('color','#cc0000');
		}
		else{
			$('.end').eq(3).text('6-20个字符，区分大小写').css('color','#999999');
			$('.step_ul .password2').focus();
			mark4=true;
		}
    });
	$('.step_ul .password2').blur(function(e) {
		var pa= /[a-zA-Z0-9]{6,20}/;
        if(!(pa.test($(this).val()))){
			$('.end').eq(4).text('密码格式错误').css('color','#cc0000');
		}
		if($('.step_ul .password1').val()!=$('.step_ul .password2').val()){
			$('.end').eq(4).text('密码不一致').css('color','#cc0000');
		}
		else{
			mark5=true;
		}
    });
	$('.step_ul input[type=submit]').eq(2).click(function(e) {
        if($('.step2 .password1').val()==''){
			$('.step2 .password1').focus();
		}
		if($('.step2 .password2').val()==''){
			$('.step2 .password2').focus();
		}
		if(mark4&&mark5){
			$('.step_ul').eq(2).css('display','none');
			$('.step_ul').eq(3).css('display','block');
		}
    });
	*/
});
