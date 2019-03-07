// JavaScript Document
window.onload=function(){
	$('.xieyi').click(function(e) {
        $('.bodybg').css('display','block');
		center($('.tip'));
		$('.tip').click(function(e) {
            $('.bodybg,.tip').css('display','none');
        });
    });
	$('input').eq(0).blur(function(e) {
        if(!($('input').eq(0).val().length<=20&&$('input').eq(0).val().length>=5)){
			$('.ul_text').eq(0).text("用户名格式错误").css('color','red');
		}
		else{
			$('.ul_text').eq(0).text("用户名长度5-20个字符").css('color','#cccccc');
		}
    });
	$('input').eq(1).blur(function(e) {
        var regValid=/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/;
		if(!regValid.test($(this).val())){
			$('.ul_text').eq(1).text("邮箱格式错误").css('color','red');
		}
		else{
			$('.ul_text').eq(1).text("账户激活需要邮箱").css('color','#cccccc');
		}
    });
	$('input').eq(2).blur(function(e) {
        var pa=/[a-zA-Z0-9]{6,20}/;
		if(!pa.test($(this).val())){
			$('.ul_text').eq(2).text("密码格式错误").css('color','red');
		}
		else{
			$('.ul_text').eq(2).text("6-20个字符，区分大小写").css('color','#cccccc');
		}
    });
	$('input').eq(3).blur(function(e) {
        var pa=/[a-zA-Z0-9]{6,20}/;
		if(!pa.test($(this).val())){
			$('.ul_text').eq(3).text("密码格式错误").css('color','red');
		}
		if(!($(this).val()==$('input').eq(2).val())){
			$('.ul_text').eq(3).text("两次密码不一致").css('color','red');
		}
		else{
			$('.ul_text').eq(3).text("6-20个字符，区分大小写").css('color','#cccccc');
		}
    });
	$('.c_left .title').click(function(e) {
        $('.c_left .title').removeClass('t_active');
		$(this).addClass('t_active');
    });
	function center(obj) {
		var screenWidth = $(window).width(), screenHeight = $(window).height();  //当前浏览器窗口的宽高
		var scrolltop = $(document).scrollTop();//获取当前窗口距离页面顶部高度
		var objLeft = (screenWidth - obj.width())/2;
		var objTop = (screenHeight - obj.height())/2 + scrolltop;
		obj.css({left: objLeft + 'px', top: objTop + 'px','display': 'block'});
				//浏览器窗口大小改变时
		$(window).resize(function() {
		screenWidth = $(window).width();
		screenHeight = $(window).height();
		scrolltop = $(document).scrollTop();
		objLeft = (screenWidth - obj.width())/2 ;
		objTop = (screenHeight - obj.height())/2 + scrolltop;
		obj.css({left: objLeft + 'px', top: objTop + 'px','display': 'block'});
				});
				//浏览器有滚动条时的操作、
		$(window).scroll(function() {
		screenWidth = $(window).width();
		screenHeight = $(widow).height();
		scrolltop = $(document).scrollTop();
		objLeft = (screenWidth - obj.width())/2 ;
		objTop = (screenHeight - obj.height())/2 + scrolltop;
		obj.css({left: objLeft + 'px', top: objTop + 'px','display': 'block'});
				});
		}
		

};
	