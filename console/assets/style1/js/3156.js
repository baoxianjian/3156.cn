$(document).ready(function(){


    $(".search-select").bind({
        mouseover: function() {
            $(this).find("dl").show();
            $(".search-trigger span").css("background","url("+"img/trigger.png"+")"+" "+"no-repeat"+" "+"48px"+" "+"center");
        },
        mouseleave: function() {
            $(this).find("dl").hide();
            $(".search-trigger span").css("background","url("+"img/search_trigger.png"+")"+" "+"no-repeat"+" "+"48px"+" "+"center");
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
            $("#search-form").attr("action", "http://www.baidu.com/" + $(this).attr("s-type") + ".html");
            $("#search-form input[name=kw]").attr("def", "输入您要找的" + $(this).text());
            $("#search-form input[name=kw]").val("输入您要找的" + $(this).text());
            $(".search-select span").text($(this).text());
            $(this).parent().hide();
        }
    });
    $("#search-form").submit(function() {
        if ($("input[name='kw']").val() == $("input[name='kw']").attr("def")) {
            $("input[name='kw']").val('')
        }
    });
    $("#search-form input[name=kw]").val("输入您要找的项目");
    $(".search-select dl>dd").eq(0).click();



    $(".list_of li:eq(0) span").css("color","red");
    $(".list_of li:eq(1) span").css("color","red");
    $(".list_of li:eq(2) span").css("color","red");



       /* $('.list_of').mouseover(function(e){
            var _target =  $(e.target);

            if(_target.hasClass('text')){
                dogo(_target);
            }else if(_target.parents('p.text').length){
                dogo(_target.parents('p.text'))
            }
        })
         dogo = function(dom){
                 dom.attr('s','e');
                 dom.attr('t','0');
                 var i = 1;
                 var s = 'e';

                  var Time = setInterval(function () {
                      i = parseInt(dom.attr('t'));
                      s = dom.attr('s');
                      if( s == 'e') {
                          if (i >= 1) {
                              dom.attr('t', '0');
                              dom.attr('s', 'd');
                              dom.siblings("p.text_info").slideDown(function(){
                                  dom.parent().hover(function(){
                                  },function(){
                                      dom.siblings("p.text_info").slideUp();
                                            dom.attr('s','l');
                                    })
                              });
                              clearInterval(Time);
                         }
                          i++;
                          dom.attr('t', i);
                      }else if(s == 'd'){
                          dom.attr('t', '0');
                          clearInterval(Time);
                      }else{
                          dom.attr('s', 'l');
                          dom.attr('t', '0');
                          clearInterval(Time);
                      }
                  }, 1000);

             dom.mouseleave(function(){
                 if(dom.attr('s') == 'd' ){

                 }else if(dom.attr('s') == 'e' ){
                     dom.attr('s','l');
                     dom.attr('t','0');
                     clearInterval(Time);
                 }
             });
         }
        var Time = null;*/


       /*t.find("p.text").mouseleave(function(){
     $(".list_of li .text_info").slideUp()
     })*/





    $(".Quick").mouseenter(function(){
        $(".options").show()
    });
   $(".msg").mouseleave(function(){
       $(".options").hide()
   });
    $(".options").mouseleave(function(){
        $(".options").hide()
    });
    $(".options p").toggle(function(){
      
       $(this).css("background","url("+"img/option_bg.png"+")"+" "+"no-repeat"+" "+"0"+" "+"5px");
        var msg = $(this).text();
        var text = $("#text").val();
        $("#text").val(text+msg);

    },function(){
       $(this).css("background","url("+"img/option_bg2.png"+")"+" "+"no-repeat"+" "+"0"+" "+"5px");
        var msg = $("#text").val();
         $("#text").val(msg.replace($(this).text(),""));
        
    })


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


    })

});


