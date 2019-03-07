/* 备注 上传from id 固定 upForm 上传文件表单 class 固定 uploadImage 预览图片为上传表单id+"_Img"格式 隐藏域name固定为upPath[]*/
$(document).ready(function(){
	//=================================== 异步文件上传 JS 处理 Start ===================================//

		$('#upForm').on('change',".uploadImage",function(){

			var img_id = $(this).attr('id')+"_Img";//获取图片id
			$(document)
			.ajaxStart(function(){
				$("#loading").show();
			})
			.ajaxStop(function(){
				$("#loading").hide();
			});

			$.ajaxFileUpload
			(
				{
					url:'UploadFile',
					secureuri:false,
					fileElementId:$(this).attr('id'),
					data:{name:$(this).attr('name')},
					dataType: 'json',
	
					success: function (data)
					{	

						if( data.status == 0)
						{
							layer.alert(data.info);
						}
						else
						{
							$('#'+img_id).attr('src',data.path);//显示上传图片
							$("[name='upPath[]']").val(data.path);
						}
					},
					error: function ()
					{
						alert('文件上传失败');
					},
				}
			)


		})
	//=================================== 异步文件上传 JS 处理 End ===================================//
});

