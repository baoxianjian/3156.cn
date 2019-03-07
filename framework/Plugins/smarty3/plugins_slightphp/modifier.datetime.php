<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * 格式化时间戳
 * @param unknown $timestamp 时间戳
 * @param unknown $format 格式
 * @return string
 */
function smarty_modifier_datetime($timestamp,$format)
{
    return date($format,$timestamp);
}
?>
