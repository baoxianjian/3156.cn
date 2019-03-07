<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
/**
 * 截图片地址
 * @param unknown_type $picuri 图片地址
 * @param unknown_type $size 大小
 */
function smarty_modifier_picsize($picuri,$size=""){
    return SUtil::picsize($picuri,$size);
}
?>