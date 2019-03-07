// JavaScript Document
$(function(){
	$('.content_bg').width($(window).width());
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

});