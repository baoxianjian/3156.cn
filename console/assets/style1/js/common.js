//返回变量是否被定义
function isDefined(variable) {
    return typeof variable == 'undefined' ? false : true;
}

//删除
function delConfirm(id,opUrl)
{
    opUrl=isDefined(opUrl)?opUrl:'del';
    $.layer({
        shade: [0],
        area: ['auto','auto'],
        dialog: {
            msg: '您确定删除？',
            btns: 2,                    
            type: 5,
            btn: ['确定','取消'],
            yes: function(){
                $.post(opUrl,{ajax:1,id:id},function(data){
                if(data.status==1) {if($('#tr_'+id)){$('#tr_'+id).remove();}}
                var temp=layer.alert(data.info, data.status, !1);
                var tostr='';   
                if(data.tourl=='_reload')
                    {tostr="window.location.reload();";}
                else
                    {tostr="window.location='"+tourl+"'";}
                
                setTimeout("layer.close("+temp+");"+tostr, data.timeout);
        },'json')
            }, 
        }
      });
      return false;
}