
// Cloud Float...
    var $main = $cloud = mainwidth = null;
    var offset1 = 450;
	var offset2 = 0;
	
	var offsetbg = 0;
    
    $(document).ready(
        function () {
            $main = $("#mainBody");
			$body = $("body");
            $cloud1 = $("#cloud1");
			$cloud2 = $("#cloud2");
			
            mainwidth = $main.outerWidth();

            /*
            //================== 控制台异步登录处理 ====================//
                $('#ensure').click(function(){
                    var aa=$('form').serialize();

                    $.post('./dologin',aa+"&ajax=1",function(data){
                        if ( data['status'] == 1 ){//登录成功
                            layer.alert(data['info'],1);
                            location.href = '/main/index';
                        }else{
                            layer.alert(data['info']);
                        }
                    },'json');
                    return false;
                })
            //================== 控制台异步登录处理 ====================//
            */
        }


    );

    /// 飘动
    setInterval(function flutter() {
        if (offset1 >= mainwidth) {
            offset1 =  -580;
        }

        if (offset2 >= mainwidth) {
			 offset2 =  -580;
        }
		
        offset1 += 1.1;
		offset2 += 1;
        $cloud1.css("background-position", offset1 + "px 100px")
		
		$cloud2.css("background-position", offset2 + "px 460px")
    }, 70);
	
	
	setInterval(function bg() {
        if (offsetbg >= mainwidth) {
            offsetbg =  -580;
        }

        offsetbg += 0.9;
        $body.css("background-position", -offsetbg + "px 0")
    }, 90 );




	