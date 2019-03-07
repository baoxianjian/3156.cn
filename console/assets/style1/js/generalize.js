$(document).ready(function () {
	//JS 复选框控制 ----------------------------------------------- Statr//
		$("[name='check[]']").click(function(){
			if( $("[name='check[]']").length == $("[name='check[]']:checked").length ){
				$('#checkAll').prop('checked','checked');//开启全选
			}else{
				$('#checkAll').removeAttr('checked');//关闭全选
			}
		})

		$('#checkAll').click(function(){
			if( $("[name='check[]']").length == $("[name='check[]']:checked").length ){
				$("[name='check[]']").removeAttr('checked');


			}else{
				$("[name='check[]']").prop('checked','checked');
				$('#delAll').removeAttr('disabled');
			}
		});
	//JS 复选框控制 ----------------------------------------------- End//



});