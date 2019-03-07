// JavaScript Document
$(function(){
	//图片最后一个去掉右边距
	$('.last').css('margin-right','0');

	//搜索转跳路径全局变量
	var urlPath = 'sreach_list';//默认为产品搜索

	//foot选项卡切换
	$('.title').map(function(index, element) {
		$(this).mouseover(function(e) {
            $('.title').removeClass('span_active');
			$('.zixun_ul').removeClass('zixun_ul_active');
			$('.title').eq(index).addClass('span_active');
			$('.zixun_ul').eq(index).addClass('zixun_ul_active');
        });
    });
	$('.search_input .search_s').mouseover(function(e) {
        $('.select_ul').css('display','block');
    }).mouseout(function(e) {
		$('.select_ul').css('display','none');
    });
	$('.select_ul li').click(function(e) {
        $('.search_input span').text($(this).text());
        //产品
        if ( $(this).attr("id") == 'product' ){
        	urlPath = "sreach_list";
        }else{
        	urlPath = "company_list";
        }
		$(this).parents('ul').hide();
    });
	$('.select_ul li').eq(1).click(function(e) {
        $('.search_input input').val('请输入要查找的公司名称');
    });
	$('.select_ul li').eq(0).click(function(e) {
        $('.search_input input').val('请输入要查找的产品名称');
    });
	$('.search_input input').focus(function(e) {
        $(this).val(null);
    }).blur(function(e) {
        if($(this).val().length==0){
			$(this).val('请输入要查找的药品名称');
		}
    });
	$('.topbg').width($(window).width());
	//招商
	$('.yao_ul li').mousemove(function(e) {
		$(this).children('a').css('border-color','red');
		$(this).parents('.yao_ul').next().children('li').eq($(this).index()).css('opacity','0.5');
		$(this).parents('.yao_ul').next().next().children('li').eq($(this).index()).css('opacity','1');
    }).mouseout(function(e) {
		$(this).children('a').css('border-color','#ededed');
		$(this).parents('.yao_ul').next().children('li').eq($(this).index()).css('opacity','0');
		$(this).parents('.yao_ul').next().next().children('li').eq($(this).index()).css('opacity','0');
    });
	$('.yao_ul1 li').mouseover(function(e) {
        $(this).children('li').css('opacity','0.5');
    });
	$('.yao_ul2 li').mouseover(function(e) {
        $(this).children('li').css('opacity','1');
    });

	//搜索
    $('#search').click(function(){	
    	window.location.href = "/search/"+urlPath+"?seek="+$('#seekVal').val();
    })

    //键盘回车敲击
    $(window).keydown(function(data){
    	if ( data.keyCode == 13 ){
    		window.location.href = "/search/"+urlPath+"?seek="+$('#seekVal').val();
    	}
    })

})
