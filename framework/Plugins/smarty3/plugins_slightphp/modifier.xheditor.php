<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * xheditor调用
 * @param string $type 1前台显示 2后台显示
 * @return string
 */
function smarty_modifier_xheditor($content='',$type=1,$inputname='content_xheditor',$width='600',$height='200'){
	//获取两个URL的地址
	$coreUrl = ASSETS_URL.'/min/index.php?f=/i/frame.js,/i/plugin/xheditor/xheditor-1.2.1.min.js,/i/plugin/xheditor/xheditor_lang/zh-cn.js,/i/plugin/xheditor/xheditor_plugins/ubb.js';
	$editorRoot = ASSETS_URL.'/i/plugin/xheditor/';
    $stringHtml = "<script type='text/javascript' src='$coreUrl'></script>
<textarea name='$inputname' class='con_xheditor' style='width:{$width}px;height:{$height}px'>$content</textarea>
<script type='text/javascript'>
$(function() {
	//$('.con_xheditor').live('click',function(){
    ";
    if ($type==1) {//前台显示
    	$upImgUrl = WWW_URL.'/ajax/editorupload';
    	$stringHtml .= "
	var editor = $('.con_xheditor').xheditor({
    	    upImgUrl:'{$upImgUrl}',
            upImgExt:'jpg,jpeg,gif,png',
            beforeSetSource:ubb2html,
            beforeGetSource:html2ubb,
            tools:'Paste,Pastetext,|,Blocktag,Fontface,FontSize,Bold,Italic,Underline,FontColor,Removeformat,Outdent,Indent,Align,List,|,Link,Unlink,Img,Flash,Preview,Fullscreen',
            upMultiple:1,
            skin:'default',
            html5Upload:false,
            editorRoot:'{$editorRoot}',
            width:{$width},
            height:{$height}
        });";
    }elseif ($type==2){//后台使用
    	$upImgUrl = ADMIN_URL.'/ajax/editorupload';
    	$upfileUrl = ADMIN_URL.'/ajax/filesupload';
    	$stringHtml .= "
	var editor = $('.con_xheditor').xheditor({
    	    upImgUrl:'{$upImgUrl}',
            upImgExt:'jpg,jpeg,gif,png',
            upLinkUrl:'{$upfileUrl}',
            upLinkExt:'doc,docx,ppt,pptx,xls,xlsx,zip,rar',
            beforeSetSource:ubb2html,
            beforeGetSource:html2ubb,
            tools:'Paste,Pastetext,|,Blocktag,Fontface,FontSize,Bold,Italic,Underline,FontColor,Removeformat,Outdent,Indent,Align,List,|,Link,Unlink,Img,Flash,Source,Preview,Fullscreen',
            upMultiple:1,
            skin:'default',
            html5Upload:false,
            editorRoot:'{$editorRoot}',
            width:{$width},
            height:{$height}
        });";
    }else{
    	return '无此类编辑器';
    }
    $stringHtml .='
    		editor.focus();
	//})
});
</script>';
    return $stringHtml;
}

/**
 * 完整按钮表：
|：分隔符
/：强制换行
Cut：剪切
Copy：复制
Paste：粘贴
Pastetext：文本粘贴
Blocktag：段落标签
Fontface：字体
FontSize：字体大小
Bold：粗体
Italic：斜体
Underline：下划线
Strikethrough：中划线
FontColor：字体颜色
BackColor：字体背景色
SelectAll：全选
Removeformat：删除文字格式
Align：对齐
List：列表
Outdent：减少缩进
Indent：增加缩进
Link：超链接
Unlink：删除链接
Anchor：锚点
Img：图片
Flash：Flash动画
Media：Windows media player视频
Hr：插入水平线
Emot：表情
Table：表格
Source：切换源代码模式
Preview：预览当前代码
Print：打印
Fullscreen：切换全屏模式
About：关于xhEditor
 */
?>
