<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if (!defined("IN_BAIGO")) {
    exit("Access Denied");
}

require(BG_PATH_INC . "common.inc.php");
$arr_set = array(
    "base"          => true,
    "db"            => true,
    "dsp_type"      => "result",
);
fn_chkPHP($arr_set);

require(BG_PATH_FUNC . "init.func.php");
fn_init($arr_set);

$ctrl_spec = new CONTROL_API_API_SPEC();

switch ($GLOBALS["act"]) {
    case "read":
    case "get":
        $ctrl_spec->ctrl_read();
    break;

    default:
        $ctrl_spec->ctrl_list();
    break;
}
