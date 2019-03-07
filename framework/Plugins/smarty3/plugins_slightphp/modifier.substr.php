<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
/**
 * 截取字符串
 * @param unknown $string
 * @param unknown $start
 * @param unknown $length
 * @param string $suffix
 * @return string
 */
function smarty_modifier_substr($string,$start,$length,$suffix='...')
{
    return SUtil::getSubStr($string,$start,$length,$suffix);
}
?>
