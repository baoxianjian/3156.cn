$(document).ready(function(){
    $(".search-select").bind({
        mouseover: function() {
            $(this).find("dl").show();
            $(".search-trigger span").css("background","url("+"http://img1.3156.cn/res/img/2.0/trigger.png"+")"+" "+"no-repeat"+" "+"48px"+" "+"center");
        },
        mouseleave: function() {
            $(this).find("dl").hide();
            $(".search-trigger span").css("background","url("+"http://img1.3156.cn/res/img/2.0/search_trigger.png"+")"+" "+"no-repeat"+" "+"48px"+" "+"center")/*.css('background','url(../../img/2.0/search_trigger.png) ')*/;
        }
    });
    $(".search-selec dl").mouseleave(function() {
        $(this).hide();
    });
    $(".search-select dl>dd").bind({
        mouseover: function() {
            $(this).addClass("searchOn").css("color", "#fff").siblings().removeClass("searchOn").css("color", "#999");
        },
        click: function() {
            $("#search-form").attr("action", "http://s.3156.cn/" + $(this).attr("s-type"));
			if(($("#search-form input[name=seek]").val()=='输入您要找的公司')||($("#search-form input[name=seek]").val()=='输入您要找的产品')){
				$("#search-form input[name=seek]").attr("def", "输入您要找的" + $(this).text());
            	$("#search-form input[name=seek]").val("输入您要找的" + $(this).text());
			}
/*			else{
				$(".search-select span").text();
			}
            
*/			$(".search-select span").text($(this).text());
            $(this).parent().hide();
        }
    });
    $("#search-form").submit(function() {
        if ($("input[name='kw']").val() == $("input[name='kw']").attr("def")) {
            $("input[name='kw']").val('')
        }
    });
/*    $("#search-form input[name=kw]").val("输入您要找的项目");
    $(".search-select dl>dd").eq(0).click();
*/


    $(".list_of li:lt(3) span").css("color","red");
    $(".right_OTC li").each(function(){
        if($(this).index()<=2){
            $(this).find("span").css("background","red")
        }
    })
    $(".small_link a").each(function(){
        $(this).find("span:eq(1)").css("color","red")
    })
    $(".small_link a").hover(function(){
        $(this).find("span").css("text-decoration","underline")
    },function(){
        $(this).find("span").css("text-decoration","none")

    })







    $(".Quick").mouseenter(function(){
        $(".options").show()
    });
    $(".msg").mouseleave(function(){
        $(".options").hide()
    });
    $(".options").mouseleave(function(){
        $(".options").hide()
    });

    var ck=$(".checkbox");
    ck.each(function(){
        $(this).click(function(){
            if($(this).get(0).checked==true){
                var msg=$(this).parent("p").text();
                $("#text").css("text-indent","0").val( $("#text").val()+msg+"\n");
            };
            if($(this).get(0).checked==false){
                var msg = $("#text").val();
                $("#text").val(msg.replace($(this).parent("p").text()+"\n",""));
            }
        })
    });







    $(".text1 .txt").focus(function(){
        var text=$(this).val();
        if(text=="在此输入你的手机或电话"){
            $(this).val("")
        }
    });
    $(".text1 .txt").blur(function(){
        var text=$(this).val();
        if(text==""){
            $(this).val("在此输入你的手机或电话")

        }


    });

    $(".sel-trigger").toggle(function(){
        $(".sel-trigger>dl").css("display","block")
    },function(){
        $(".sel-trigger>dl").css("display","none")

    });



    $(".sel dd").click(function(){
        txt_=$(this).text();
        $(".sel-trigger span").text(txt_)
    });

    $(".Drug_class .drug_list li:odd").css("text-align","right");



    //医药代理

    $(".agent_table tbody tr:even").css("background","#eaf0e4");
    $(".IP tbody tr:odd").css("background","#F9F9F9");


    $(".xq_right>li").css("width","785px");


    //医药数据库

    $(".database_sel").bind({
        mouseover: function() {
            $(this).find("dl").show();
        },
        mouseleave: function() {
            $(this).find("dl").hide();
        }
    });
    $(".database_sel dl").mouseleave(function() {
        $(this).hide();
    });
    $(".database_sel dl>dd").bind({
        hover: function() {
            $(this).css({
                color:"#fff",
                background:"#fb8200",
                width:"149",
                cursor:"pointer"

            }).siblings().css({
                color:"#000",
                background:"#fff"
            });
        },
        click: function() {
            $(".search_database #search-form").attr("action", "http://www.3156.cn/" + $(this).attr("s-type") + ".html");
            $("#search-form input[name=kw1]").attr("def", "输入您要找的" + $(this).text());
            $("#search-form input[name=kw1]").val("输入您要找的" + $(this).text());
            $(".database_sel span").text($(this).text());
            $(".database_sel span").prepend("<i></i>");
            $(this).parent().hide();
        }
    });
    $("input[name='kw1']").click(function(){
        //alert($(this).attr("def"))
        if($("input[name='kw1']").val()==$(this).attr("def")){
            $("input[name='kw1']").val('');
        };
    });
    $("input[name='kw1']").blur(function(){
        //alert($(this).attr("def"))
        if($("input[name='kw1']").val()==""){
            $("input[name='kw1']").val($(this).attr("def"));
        };
    });


    $(".agent_Options .txt").focus(function(){
        if($(this).val()=="关键词"){
            $(this).val("")
        }
    }).blur(function(){
        if($(this).val()==""){
            $(this).val("关键词")

        }
    });





    /*医药公司列表*/

    /*$(".Rec_M .con a").each(function(){

        var index_=$(this).index();
        var i;
        for(i=0;i<=$(".Rec_M .con a").length;i+=4){

            if(index_==i){
                $(this).css("color","red")

            }
        }

    })*/

    /*药品库*/

/*$(".drug_list .drug_info").find("li:odd").css({
    marginLeft:"10px",
    marginRight:"10px"
});
*/





    /*用户中心*/
    $(".topbar .right .cur").mouseover(function(){
		//alert('dg');
        $(".down_menu").css("display","block");
    },function(){
        $(".down_menu").css("display","none")
    });

    $(".warp .user_menu").find("li:last").find(".menu_title").css("border","none");
    $(".Service_con span:eq(0)").css({
        left:25,
        top:0
    })
    $(".Service_con span:eq(1)").css({
        right:25,
        top:0
    });

    $(".data_query .con li").each(function(){
        var index_=$(this).index();
        var i;
        for(i=3;i<=$(".data_query .con li").length;i+=4){
            if(i==index_){
              $(this).css("margin-right","0")
            }
        }
    });

    /*广告位刷新*/
    $(".user_right").find(".AD_refresh:last").css("border","none")
    /*我的代理信息*/
    $(".Refresh .help").mouseover(function(){
        $(this).attr("src","img/help_hover.png");
        $(".Refresh .help_info").css("display","block")
    })
    $(".Refresh .help").mouseout(function(){
        $(this).attr("src","img/help.png");
        $(".Refresh .help_info").css("display","none")
    });
    $(".product_list_ table tbody tr").each(function(){
        $(this).find("td:last a").css("color","#333").mouseover(function(){
            $(this).css("color","#f00");
            $(this).parent("td").css("position","relative");
            $(this).parent("td").find(".Remarks").css("display","block")
        }).mouseout(function(){
            $(this).css("color","#333");
            $("table tbody tr").find(".Remarks").css("display","none")
        })
    });
    $(".Ordinary table tbody tr").each(function(){
        $(this).find("td").eq($(this).size()-4).find("a").css("color","red").mouseover(function(){
            $(this).css("text-decoration","underline")
        }).mouseout(function(){
            $(this).css("text-decoration","none")
        })
    });
    $(".Ordinary .star").toggle(function(){
        $(this).css("backgroundPosition","left -48px")
    },function(){
        $(this).css("backgroundPosition","left 0")
    });


    /*自主建站*/
    $('.ul_self img').click(function(e) {
        $('.middle_img').html($('.big_img').eq($(this).parents('li').index())).css('z-index','5');
        $('.middle_img, .close').show();
    });
    $('.close_pic').click(function(e) {
        $('.middle_img').hide();
        $(this).hide();
    });
    $('.del').click(function(e) {
        $(this).parents('tr').remove();
    });


});


