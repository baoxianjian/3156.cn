// JavaScript Document
(function($) {  
  
var div = document.createElement('div'),  
    rposition = /([^ ]*) (.*)/;  
    
if(div.style.backgroundPositionX !== '') {  
    $(['X', 'Y']).each(function( i, letter ) {  
        var property = 'backgroundPosition' + letter,  
            isX = letter == 'X';  
        $.cssHooks[property] = {  
            set: function(elem, value) {  
                var current = elem.style.backgroundPosition;  
                elem.style.backgroundPosition = (isX? value + ' ' : '' ) + (current? current.match(rposition)[isX+1] : '0') + (isX? '' : ' ' + value);  
            },  
            get: function(elem, computed) {  
                var current = computed?  
                    $.css(elem, 'backgroundPosition') :  
                    elem.style.backgroundPosition;  
                return current.match(rposition)[!isX+1];  
            }  
        };  
        $.fx.step[property] = function(fx) {  
            $.cssHooks[property].set(fx.elem, fx.now + fx.unit);  
        }  
    });  
}  
div = null;  
})(jQuery); 
$(function(){
	$('input[type=submit]').click(function(e) {
        if(($('input').eq(0).val()=='')||($('input').eq(1).val()=='')){
			$('.tip').css('display','block');
		}
    });
	var input=$('input');
	for(var i=0;i<input.length;i++)
	{
		placeHolder(input[i],true);
	}
	function placeHolder(obj, span) {
			if (!obj.getAttribute('placeholder')) return;
			var imitateMode = span===true?true:false;
			var supportPlaceholder = 'placeholder' in document.createElement('input');
			if (!supportPlaceholder) {
				var defaultValue = obj.getAttribute('placeholder');
				if (!imitateMode) {
					obj.onfocus = function () {
						(obj.value == defaultValue) && (obj.value = '');
						obj.style.color = '';
					}
					obj.onblur = function () {
						if (obj.value == defaultValue) {
							obj.style.color = '';
						} else if (obj.value == '') {
							obj.value = defaultValue;
							obj.style.color = '#ACA899';
						}
					}
					obj.onblur();
				} else {
					var placeHolderCont = document.createTextNode(defaultValue);
					var oWrapper = document.createElement('span');
					oWrapper.style.cssText = 'position:absolute; color:#ACA899; display:inline-block; overflow:hidden;';
					oWrapper.className = 'wrap-placeholder';
					oWrapper.style.fontFamily = getStyle(obj, 'fontFamily');
					oWrapper.style.fontSize = getStyle(obj, 'fontSize');
					oWrapper.style.marginLeft = parseInt(getStyle(obj, 'marginLeft')) ? parseInt(getStyle(obj, 'marginLeft')) + 3 + 'px' : 3 + 'px';
					oWrapper.style.marginTop = parseInt(getStyle(obj, 'marginTop')) ? getStyle(obj, 'marginTop'): 1 + 'px';
					oWrapper.style.paddingLeft = getStyle(obj, 'paddingLeft');
					oWrapper.style.width = obj.offsetWidth - parseInt(getStyle(obj, 'marginLeft')) + 'px';
					oWrapper.style.height = obj.offsetHeight + 'px';
					oWrapper.style.lineHeight = obj.nodeName.toLowerCase()=='textarea'? '':obj.offsetHeight + 'px';
					oWrapper.appendChild(placeHolderCont);
					obj.parentNode.insertBefore(oWrapper, obj);
					oWrapper.onclick = function () {
						obj.focus();
					}
					//绑定input或onpropertychange事件
					if (typeof(obj.oninput)=='object') {
						obj.addEventListener("input", changeHandler, false);
					} else {
						obj.onpropertychange = changeHandler;
					}
					function changeHandler() {
						oWrapper.style.display = obj.value != '' ? 'none' : 'inline-block';
					}
					function getStyle(obj, styleName) {
						var oStyle = null;
						if (obj.currentStyle)
							oStyle = obj.currentStyle[styleName];
						else if (window.getComputedStyle)
							oStyle = window.getComputedStyle(obj, null)[styleName];
						return oStyle;
					}
				}
			}
		}
	//图片最后一个去掉右边距
	$('.last').css('margin-right','0');
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
	$('.yao_ul li,.yao_ul1 li,.yao_ul2 li').mousemove(function(e) {
		$('.yao_ul').eq($(this).parents('ul').attr('number')).children('li').eq($(this).index()).children('a').css('border-color','red');
		$('.yao_ul').eq($(this).parents('ul').attr('number')).children('li').eq($(this).index()).parents('.yao_ul').next().children('li').eq($(this).index()).css('opacity','0.5');
		$('.yao_ul').eq($(this).parents('ul').attr('number')).children('li').eq($(this).index()).parents('.yao_ul').next().next().children('li').eq($(this).index()).css('opacity','1');
    }).mouseout(function(e) {
		$('.yao_ul').eq($(this).parents('ul').attr('number')).children('li').eq($(this).index()).children('a').css('border-color','#ededed');
		$('.yao_ul').eq($(this).parents('ul').attr('number')).children('li').eq($(this).index()).parents('.yao_ul').next().children('li').eq($(this).index()).css('opacity','0');
		$('.yao_ul').eq($(this).parents('ul').attr('number')).children('li').eq($(this).index()).parents('.yao_ul').next().next().children('li').eq($(this).index()).css('opacity','0');
    });
	$('.yao_ul1 li').mouseover(function(e) {
        $(this).children('li').css('opacity','0.5');
    });
	$('.yao_ul2 li').mouseover(function(e) {
        $(this).children('li').css('opacity','1');
    });
	
	//回到顶部
	$('.back_top').css('left',function(){
		
		return $(window).width()/2+610;
	})
	$(window).scroll(function(){
        if ($(window).scrollTop()>0){
            $(".back_top").fadeIn(1500);
        }
        else
          {
             $(".back_top").fadeOut(1500);
          }
       });
	$(".back_top").click(function(e) {
		$('body,html').animate({scrollTop:0},300);
    });
	//会员中心
	$('.c_left .active_li .o_img').css('display','inline-block');
	$('.c_left .active_li .g_img').css('display','none');
	$('.c_left .center a').mouseover(function(e) {
        $(this).children('.g_img').css('display','none');
		$(this).children('.o_img').css('display','inline-block');
    }).mouseout(function(e) {
        $(this).children('.g_img').css('display','inline-block');
		$(this).children('.o_img').css('display','none');
		$('.c_left .active_li .o_img').css('display','inline-block');
	$	('.c_left .active_li .g_img').css('display','none');
    });
	//代理详情
	$('.list6 .choose li input').click(function(e) {
		$('.list6 textarea').val('');
		for(var i=0;i<$('.list6 .choose li input').length;i++){
			if($('.list6 .choose li input').eq(i).prop('checked')){
				$('.list6 textarea').val($('.list6 textarea').val()+$('.list6 .choose li input').eq(i).next('label').text());
			}
		}        
    });
	//医药数据库
	$('.shujuku a').css('background-position-x','40px').css('background-position-y',function(){
		return -$(this).parents('li').index()*40+8;
	});
	$('.shujuku .left3 a').css('background-position-y',function(){
		return -$(this).parents('li').index()*40;
	});
	$('.shujuku ul').hide();
	$('.shujuku .d_show ul').show().prev('p').children('img').addClass('img2');
	$('.shujuku p img.img2').attr('src','img/Medicine%20database/jian.png');
	$('.shujuku p').click(function(e) {
		$(this).next('ul').slideToggle('slow');
		$(this).children('img').toggleClass("img2");
		$('.shujuku p img').attr('src','img/Medicine%20database/jia.png');
		$('.shujuku p img.img2').attr('src','img/Medicine%20database/jian.png');    
	});
	$('.middle_bottom a').mouseover(function(e) {
        $(this).children('span').css('color','#fb8200');
    }).mouseout(function(e) {
        $(this).children('span').css('color','#959595');
    });
	//VIP图片变换
	 $('.change_pic').mouseover(function(e) {
        $(this).children('.p_top').css({
			'top':-20,
			'left':-20,
			'width':400,
			'height':106
		}).css('box-shadow','10px 10px 20px #aaaaaa');
		$(this).children('img,.middle').css({
			'left':-20,
			'width':400,
			'height':238
		}).css('box-shadow','10px 10px 20px #aaaaaa');
    }).mouseout(function(e) {
        $(this).children('.p_top').css('box-shadow','none').css({
			'top':0,
			'left':0,
			'width':360,
			'height':86
		});
		$(this).children('img,.middle').css('box-shadow','none').css({
			'left':0,
			'width':360,
			'height':218
		});
    });
	//发布代理信息地址联动
	var ps = new Array();
	ps[0]='东城|西城|朝阳|丰台|石景山|海淀|门头沟|房山|大兴|昌平|顺义|通州|延庆县|怀柔区|密云县|平谷区';  //北京
	ps[1]='黄浦区|卢湾区|徐汇区|长宁区|静安区|普陀区|闸北区|虹口区|杨浦区|宝山区|闵行区|嘉定区|浦东新区|松江区|金山区|青浦区|南汇区|奉贤区|崇明县';  //上海
	ps[2]='和平区|南开区|河西区|河东区|河北区|红桥区|东丽区|西青区|津南区|北辰区|武清区|宝坻区|滨海新区|海县|宁河县|蓟县';  //天津
	/*ps[3]='渝中区|大渡口区|江北区|沙坪坝区|九龙坡区|南岸区|北碚区|綦江区|双桥区|渝北区|巴南区|万州区|涪陵区|黔江区|长寿区|江津区|合川区|永川区|南川区|辖县|綦江县|潼南县|铜梁县|大足县|荣昌县|璧山县|梁平县|城口县|丰都县|垫江县|武隆县|忠县|开县|云阳县|奉节县|巫山县|巫溪县|自治县|石柱土家族自治县|秀山土家族苗族自治县|酉阳土家族苗族自治县|彭水苗族土家族自治县'; */
	ps[3]='渝中区|大渡口区|江北区|沙坪坝区|九龙坡区|南岸区|北碚区|綦江区|双桥区|渝北区|巴南区|万州区|涪陵区|黔江区|长寿区|江津区|合川区|永川区|南川区';  //重庆
	ps[4]='石家庄|邯郸|邢台|保定|张家口|承德|廊坊|唐山|秦皇岛|沧州|衡水'; //河北
	ps[5]='太原|大同|阳泉|长治|晋城|朔州|吕梁|忻州|晋中|临汾|运城';  //山西 
	ps[6]='呼和浩特|包头|乌海|赤峰|呼伦贝尔盟|阿拉善盟|哲里木盟|兴安盟|乌兰察布盟|锡林郭勒盟|巴彦淖尔盟|伊克昭盟';  //内蒙 
	ps[7]='铁岭|大连|沈阳|锦州|鞍山|朝阳|营口|本溪|抚顺|盘锦|阜新|葫芦岛|辽阳';  //辽宁 
	ps[8]='吉林|长春|四平|辽源|通化|白山|松原|白城|延边';  //吉林 
	ps[9]='哈尔滨|齐齐哈尔|牡丹江|佳木斯|大庆|绥化|鹤岗|鸡西|黑河|双鸭山|伊春|七台河|大兴安岭';  //黑龙江 
	ps[10]='南京|镇江|苏州|南通|扬州|盐城|徐州|连云港|常州|无锡|宿迁|泰州|淮安';  //江苏 
	ps[11]='杭州|宁波|温州|嘉兴|湖州|绍兴|金华|舟山|台州|丽水|衢州';  //浙江 
	ps[12]='合肥|芜湖|蚌埠|马鞍山|淮北|铜陵|安庆|黄山|滁州|宿州|池州|淮南|巢湖|阜阳|六安|宣城|毫州';  //安徽 
	ps[13]='福州|厦门|莆田|三明|泉州|漳州|南平|龙岩|宁德';  //福建 
	ps[14]='南昌|景德镇|九江|鹰潭|萍乡|新馀|赣州|吉安|宜春|抚州|上饶';  //江西 
	ps[15]='济南|青岛|淄博|枣庄|东营|烟台|淮坊|济宁|泰安|威海|日照|莱芜|临沂|德州|聊城|滨州|菏泽';  //山东 
	ps[16]='郑州|开封|洛阳|平顶山|安阳|鹤壁|新乡|焦作|濮阳|许昌|漯河|三门峡|南阳|商丘|信阳|周口|驻马店|济源';  //河南 
	ps[17]='武汉|宜昌|荆州|襄樊|黄石|荆门|黄冈|十堰|恩施|潜江|天门|仙桃|随州|咸宁|孝感|鄂州';  //湖北 
	ps[18]='长沙|常德|株洲|湘潭|衡阳|岳阳|邵阳|益阳|娄底|怀化|郴州|永州|湘西|张家界';  //湖南 
	ps[19]='广州|深圳|珠海|汕头|东莞|中山|佛山|韶关|江门|湛江|茂名|肇庆|惠州|梅州|汕尾|河源|阳江|清远|潮州|揭阳|云浮';  //广东 
	ps[20]='南宁|柳州|桂林|梧州|北海|防城港|钦州|贵港|玉林|贺州|百色|河池';  //广西
	ps[21]='海口|三亚';  //海南 
	ps[22]='成都|绵阳|德阳|自贡|攀枝花|广元|内江|乐山|南充|宜宾|广安|达川|雅安|眉山|甘孜|凉山|泸州';  //四川 
	ps[23]='贵阳|六盘水|遵义|安顺|铜仁|黔西南|毕节|黔东南|黔南';  //贵州 
	ps[24]='昆明|大理|曲靖|玉溪|昭通|楚雄|红河|文山|思茅|西双版纳|保山|德宏|丽江|怒江|迪庆|临沧';  //云南 
	ps[25]='拉萨|日喀则|山南|林芝|昌都|阿里|那曲';  //西藏 
	ps[26]='西安|宝鸡|咸阳|铜川|渭南|延安|榆林|汉中|安康|商洛';  //陕西 
	ps[27]='兰州|彭峪关|金昌|白银|天水|酒泉|张掖|武威|定西|陇南|平凉|庆阳|临夏|甘南';  //甘肃 
	ps[28]='银川|石嘴山|吴忠|固原';  //宁夏 
	ps[29]='西宁|海东|海南|海北|黄南|玉树|果洛|海西';  //青海
	ps[30]='乌鲁木齐|石河子|克拉玛依|伊犁|巴音郭勒|昌吉|克孜勒苏柯尔克孜|博尔塔拉|吐鲁番|哈密|喀什|和田|阿克苏';  //新疆 
	ps[31]='香港';  //香港 
	ps[32]='澳门';  //澳门 
	ps[33]='台北|高雄|台中|台南|屏东|南投|云林|新竹|彰化|苗栗|嘉义|花莲|桃园|宜兰|基隆|台东|金门|马祖|澎湖';  //台湾
	//留言
	$('.list1 .select').click(function(e) {
        $('.list1 .for_select').css('display','block');
    });
	$('.list1 .for_select li').click(function(e) {
        $('.list1 .select').html($(this).html());
		$(this).parents('ul').css('display','none');
    });
	String.prototype.replaceAll = function(reallyDo, replaceWith, ignoreCase) {  
	   if (!RegExp.prototype.isPrototypeOf(reallyDo)) {  
		   return this.replace(new RegExp(reallyDo, (ignoreCase ? "gi": "g")), replaceWith);  
	   } else {  
		   return this.replace(reallyDo, replaceWith);  
	   }  
	}
	$('.list1 .for_select li').click(function(e) {
		$('.list1 .qu ul').html('');
        $('.list1 .qu').css('display','block');
		var pss=[];
		pss=ps[$(this).index()].split('|');
		for(var i=0;i<ps[$(this).index()].replaceAll("[\u4e00-\u9fa5]+", "").length+1;i++){
			$_input=$("<input type=checkbox>").attr('checked',false);
			$_li=$("<li></li>").append($_input).append($("<lable></lable>").text(pss[i]));
			$('.list1 .qu ul').append($_li);
		}
		$('.list1 .qu ul lable').map(function(index, element) {
			$(this).click(function(e) {
				$(this).prev('input').is(':checked')?$(this).prev('input').prop('checked',false):$(this).prev('input').prop('checked',true);
            });
        });
    });
	//健康资讯
	$('.sort .leftpic img,.sort .over,.sort .over_p').mouseover(function(e) {
        $('.sort .over_p').eq($(this).parents('.leftpic').attr('index')).css('display','block');
		$('.sort .over').eq($(this).parents('.leftpic').attr('index')).css('display','block');
    }).mouseout(function(e) {
        $('.sort .over_p').eq($(this).parents('.leftpic').attr('index')).css('display','none');
		$('.sort .over').eq($(this).parents('.leftpic').attr('index')).css('display','none');
    });
	//疾病查询
	$('.disease_list1').mousemove(function(e) {
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
	//药品招商弹出
	$('.c_ul1_left,.c_ul1_right').mouseover(function(e) {
        $(this).children('ul').show();
    }).mouseout(function(e) {
        $(this).children('ul').hide();
    });
	$('.c_ul1_right ul').mouseover(function(e) {
        $(this).show();
    })
	//疾病查询，疾病症状切换
	$('.list .title .fen').map(function(index, element) {
        $(this).click(function(e) {
            $('.list .title .fen').removeClass('fen_active');
			$(this).addClass('fen_active');
			$('.disease').hide();
			$('.disease').eq($(this).index()).show();
        });
    });
	$(".topbar .right").find(".img:eq(0)").mouseover(function(){
			$(".er_block").show();
		}).mouseout(function(){
			$(".er_block").hide();
	});
	//药品招商关键字
	$('.content .title .type li a').map(function(index, element) {
        $(this).click(function(e) {
           $('.content .title .type li a').removeClass('choose');
		   $(this).addClass('choose');
        })
    });
	$(' .c_left .border_none li:last').css('border','none');
	//图片轮播
	Qfast.add('widgets', { path: "js/terminator2.2.min.js", type: "js", requires: ['fx'] });  
	Qfast(false, 'widgets', function () {
		K.tabs({
			id: 'fsD1',   //焦点图包裹id  
			conId: "D1pic1",  //** 大图域包裹id  
			tabId:"D1fBt",  
			tabTn:"a",
			conCn: '.fcon', //** 大图域配置class       
			auto: 1,   //自动播放 1或0
			effect: 'fade',   //效果配置
			eType: 'click', //** 鼠标事件
			pageBt:true,//是否有按钮切换页码
			bns: ['.prev', '.next'],//** 前后按钮配置class                          
			interval: 3000  //** 停顿时间  
		}) 
	})
	
});