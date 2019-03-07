// JavaScript Document
$(function(){
	$('.list1').mousemove(function(e) {
        $(this).css('background','#ebf2fa');
		$(this).children('.list1_last').css('visibility','visible');
    }).mouseout(function(e) {
         $(this).css('background','');
		$(this).children('.list1_last').css('visibility','hidden');
    });
	$('.t_foot span').click(function(e) {
        $('.hide_li').toggle();
    });
	 $('.top .li_5 input[type=text]').focus(function(e) {
        $(this).val(null);
    }).blur(function(e) {
        if($(this).val().length==0){
			$(this).val('关键词');
		}
    });
	$('.top .li_1 .choose').css('background-position',function(){
		
		return $(this).width()+12;
	}).click(function(e) {
        $(this).hide();
    });
	$('.top .li_1 .del').click(function(e) {
        $('.top .li_1 .choose').hide();
    });
});
