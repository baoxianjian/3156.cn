$(document).ready(function(){


    $(".search-select").bind({
        mouseover: function() {
            $(this).find("dl").show();
            $(".search-trigger span").css("background","url("+"http://img1.3156.cn/res/img/2.0/trigger.png"+")"+" "+"no-repeat"+" "+"48px"+" "+"center");
        },
        mouseleave: function() {
            $(this).find("dl").hide();
            $(".search-trigger span").css("background","url("+"http://img1.3156.cn/res/img/2.0/search_trigger.png"+")"+" "+"no-repeat"+" "+"48px"+" "+"center");
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
            $("#search-form input[name=seek]").attr("def", "输入您要找的" + $(this).text());
            $("#search-form input[name=seek]").val("输入您要找的" + $(this).text());
            $(".search-select span").text($(this).text());
            $(this).parent().hide();
        }
    });
    $("#search-form").submit(function() {
        if ($("input[name='seek']").val() == $("input[name='seek']").attr("def")) {
            $("input[name='seek']").val('')
        }
    });
    $("#search-form input[name=seek]").val("输入您要找的产品");
    $(".search-select dl>dd").eq(0).click();



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
            $(".search_database #search-form").attr("action", "http://www.baidu.com/" + $(this).attr("s-type") + ".html");
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

    $(".Rec_M .con a").each(function(){

        var index_=$(this).index();
        var i;
        for(i=0;i<=$(".Rec_M .con a").length;i+=4){

            if(index_==i){
                $(this).css("color","red")

            }
        }

    })

	/*网站地图*/
    $(".map_line").each(function(){
        var this_=$(this);
        var height_=this_.find(".Classification_info").height();
        var height_1=this_.find(".Channel_info").height();
        var Diff=height_-height_1;
        this_.find(".Channel_info").css({
            paddingTop:Diff/1.1
        })

    });
    $(".map_database").each(function(){
        var database_height=$(this).height();
        var prev_=$(this).prev().height();
        var result_=database_height-prev_;
        $(this).prev().css("padding-top",result_/1.7)

    });
    $(".map_line:last").css("border-bottom","none")
	
    /*药品库*/

$(".drug_list .drug_info").find("li:odd").css({
    marginLeft:"10px",
    marginRight:"10px"
});


    /*用户中心*/

    $(".topbar .right .cur").hover(function(){
        $(".down_menu").css("display","block")
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
    })
	/*药品库子分类*/
	
	$(".sub_style_click>a").each(function(){
		var id=$(this).attr("id");
		var num=id.replace("yp_","");

		$(this).click(function(){
			
			$(".sub_style_click>a").removeClass("change_");
			$(this).addClass("change_");
			
			$(".sub_drug").slideUp();
			
			$("#sub_drug_"+num).slideDown();
			
			
			
		})
	
	})
	$(".Pack").toggle(function(){
		//$(".sub_area").removeClass("an");
		$(".Pack").text("收起").css("background-position","35px -14px");
		$(".sub_area").addClass("an");
		
	
	},function(){
		
			$(".Pack").text("展开").css("background-position","35px 5px");
			$(".sub_area").removeClass("an")
			
	
	})
	
	if($(".conditions_sub .txt_1").val()=="请输入你要查询的药品名称"){
		$(".conditions_sub .txt_1").css("color","#a3a3a3")
	};
	$(".conditions_sub .txt_1").focus(function(){
		if($(this).val()=="请输入你要查询的药品名称"){
			$(this).val("")
		}
	
	}).blur(function(){
		if($(this).val()==""){
			$(this).val("请输入你要查询的药品名称")
		}
	})
	
		//$(".sub_click a").each(function(){
		//	$(this).click(function(){
		//	if($(this).hasClass("selected_click")){
		//		$(this).removeClass("selected_click")
		//	}else{
		//		$(this).addClass("selected_click")
		//	}
		//})
		//
		//})
	$('.conditions_sub label a').toggle(function(){
		$(this).prev('input').prop('checked','checked');
	},function(){
		$(this).prev('input').removeAttr("checked")
	})
	$(".sub_drug_click").each(function(){
		var this_=$(this).find("a")
		this_.click(function(){
		this_.removeClass("selected_click");
		$(this).addClass("selected_click")
	})
	
	})
	
	
	/*自主建站*/
	$('.ul_self img').click(function(e) {
        $('.middle_img').html($('.big_img').eq($(this).parents('li').index())).css('z-index','5');
		$('.middle_img, .close').show();
    });
	$('.close').click(function(e) {
        $('.middle_img').hide();
		$(this).hide();
    });
	$('.del').click(function(e) {
        $(this).parents('tr').remove();
		
    });
	
	
	$(".latest_msg .hot_product:last").css("border-bottom","none");
	$(".enterprise_list .list_01:last").css("border-bottom","none");
	$(".main_pru").mouseover(function(){
		$(".all_show").show();
	
	}).mouseout(function(){
		$(".all_show").hide();
	})
	
	
});


