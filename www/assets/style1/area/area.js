//doc_area = null;

function change_location(value, target)
{
    handle = function (doc_area)
    {
        //alert(doc_area);
        //doc_area = xmlHttp.responseXML;
        //sublocation = $('#sublocation'); 
        //sublocation2 = $('#sublocation2');
        
        sublocation =document.getElementById('sublocation');
        sublocation2 =document.getElementById('sublocation2');
        
        //sublocation2.innerHTML = '<option value="0">--请选择--</option>';
        sublocation2.innerHTML = '';
        var y=document.createElement('option');
        y.text='--请选择--';
        y.value = 0;
        try
        {
            sublocation2.add(y,null); // standards compliant
        }
        catch(ex)
        {
            sublocation2.add(y); // IE only
        }

        if(target=='sublocation')
        {
            //sublocation.innerHTML = '<option value="0">--请选择--</option>';
            sublocation.innerHTML = '';
            var y=document.createElement('option');
            y.text = '--请选择--';
            y.value = 0;                
            try
            {
                sublocation.add(y,null); // standards compliant
            }
            catch(ex)
            {
                sublocation.add(y); // IE only
            }
        }
        var list = doc_area.getElementsByTagName("a");
        for(var i=0;i<list.length;i++)
        {
            id = list[i].getAttribute('id');
            if(id.substr(0,3) == value.substr(0,3))
            {
                var b = list[i].getElementsByTagName("b");
                for(var j=0;j<b.length;j++)
                {
                    if(target == 'sublocation')
                    {
                        sublocation.appendChild(getOption(b[j].getAttribute('id'),b[j].getAttribute('v')));
                    }
                    else
                    {
                        bid = b[j].getAttribute('id');
                        if(bid.substr(3,2) == value.substr(3,2))
                        {
                            var c = b[j].getElementsByTagName("c");
                            for(var k=0;k<c.length;k++)
                            {
                                sublocation2.appendChild(getOption(c[k].getAttribute('id'),c[k].getAttribute('v')));
                            }
                        }
                    }
                }
            }
        }
    };
    
    $.post('/assets/style1/area/area.xml',"",function(data){
       handle(data); 
    },'xml');
    //xmlHttpSend('/mods/data/area.xml',handle);
}

function location_get_value(obj)
{
    if(obj.value!='' && obj.value!='0')
    {
        $("#citycode").val(obj.value);
    }
    else
    {
        switch(obj.id)
        {
            case 'sublocation2':
            {
                alert($("#sublocation").val());
                $("#citycode").val($("#sublocation").val());
                break;
            }
            case 'sublocation':
            {
                $("#citycode").val($("#common_search_bar_work_location").val());
                break;
            }
            case 'common_search_bar_work_location':
            {
                $("#citycode").value = "";
                break;
            }
        }
    }
}

//得到select标签的option对象
function getOption(valueAtt,valueNode)
{
    var option=document.createElement("option");
    textNode=document.createTextNode(valueNode);
    option.appendChild(textNode);
    option.setAttribute("value",valueAtt);
    return option;
}