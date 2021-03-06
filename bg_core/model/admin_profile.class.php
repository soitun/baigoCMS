<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if (!defined("IN_BAIGO")) {
    exit("Access Denied");
}

/*-------------管理员模型-------------*/
class MODEL_ADMIN_PROFILE extends MODEL_ADMIN {

    function __construct() { //构造函数
        $this->obj_db = $GLOBALS["obj_db"]; //设置数据库对象
    }

    function mdl_refresh($num_adminId, $str_accessToken, $tm_accessExpire) {

        $_arr_adminUpdate = array(
            "admin_access_token"    => $str_accessToken,
            "admin_access_expire"   => $tm_accessExpire,
        );
        $_num_mysql = $this->obj_db->update(BG_DB_TABLE . "admin", $_arr_adminUpdate, "`admin_id`=" . $num_adminId); //更新数据
        if ($_num_mysql > 0) {
            $_str_rcode = "y020103"; //更新成功
        } else {
            $_str_rcode = "x020103"; //更新成功
        }

        return array(
            "rcode" => $_str_rcode, //更新成功
        );
    }


    /** 修改个人信息
     * mdl_info function.
     *
     * @access public
     * @param mixed $num_adminId
     * @return void
     */
    function mdl_info($num_adminId) {

        $_arr_adminData = array(
            "admin_nick" => $this->infoInput["admin_nick"],
        );

        $_num_mysql = $this->obj_db->update(BG_DB_TABLE . "admin", $_arr_adminData, "`admin_id`=" . $num_adminId); //更新数据
        if ($_num_mysql > 0) {
            $_str_rcode = "y020103"; //更新成功
        } else {
            $_str_rcode = "x020103"; //更新失败
        }

        return array(
            "rcode"  => $_str_rcode, //成功
        );
    }


    function mdl_prefer() {
        foreach ($this->arr_prefer as $_key=>$_value) {
            foreach ($_value as $_key_s=>$_value_s) {
                fn_cookie("prefer_" . $_key . "_" . $_key_s, "mk", $_value_s, 86400 * 365 * 10);
            }
        }

        $_arr_adminRow["rcode"] = "y020112";

        return $_arr_adminRow;
    }


    /** 个人信息输入
     * input_info function.
     *
     * @access public
     * @return void
     */
    function input_info() {
        if (!fn_token("chk")) { //令牌
            return array(
                "rcode" => "x030206",
            );
        }

        $_arr_adminPass = validateStr(fn_post("admin_pass"), 1, 0);

        switch ($_arr_adminPass["status"]) {
            case "too_short":
                return array(
                    "rcode"     => "x010212",
                );
            break;

            case "ok":
                $this->infoInput["admin_pass"] = $_arr_adminPass["str"];
            break;
        }

        $_arr_adminNick = validateStr(fn_post("admin_nick"), 0, 30);
        switch ($_arr_adminNick["status"]) {
            case "too_long":
                return array(
                    "rcode" => "x020216",
                );
            break;

            case "ok":
                $this->infoInput["admin_nick"] = $_arr_adminNick["str"];
            break;
        }

        $this->infoInput["rcode"] = "ok";

        return $this->infoInput;
    }


    /** 修改密码输入
     * input_pass function.
     *
     * @access private
     * @return void
     */
    function input_pass() {
        if (!fn_token("chk")) { //令牌
            return array(
                "rcode" => "x030206",
            );
        }

        $_arr_adminPass = validateStr(fn_post("admin_pass"), 1, 0);
        switch ($_arr_adminPass["status"]) {
            case "too_short":
                return array(
                    "rcode" => "x010212",
                );
            break;

            case "ok":
                $_arr_adminPass["admin_pass"] = $_arr_adminPass["str"];
            break;
        }

        $_arr_adminPassNew = validateStr(fn_post("admin_pass_new"), 1, 0);
        switch ($_arr_adminPassNew["status"]) {
            case "too_short":
                return array(
                    "rcode" => "x010222",
                );
            break;

            case "ok":
                $_arr_adminPass["admin_pass_new"] = $_arr_adminPassNew["str"];
            break;
        }

        $_arr_adminPassConfirm = validateStr(fn_post("admin_pass_confirm"), 1, 0);
        switch ($_arr_adminPassConfirm["status"]) {
            case "too_short":
                return array(
                    "rcode" => "x010224",
                );
            break;

            case "ok":
                $_arr_adminPass["admin_pass_confirm"] = $_arr_adminPassConfirm["str"];
            break;
        }

        if ($_arr_adminPass["admin_pass_new"] != $_arr_adminPass["admin_pass_confirm"]) {
            return array(
                "rcode" => "x010225",
            );
        }

        $_arr_adminPass["rcode"] = "ok";

        return $_arr_adminPass;
    }


    function input_mailbox() {
        if (!fn_token("chk")) { //令牌
            return array(
                "rcode" => "x030206",
            );
        }

        $_arr_adminPass = validateStr(fn_post("admin_pass"), 1, 0);
        switch ($_arr_adminPass["status"]) {
            case "too_short":
                return array(
                    "rcode" => "x010243",
                );
            break;

            case "ok":
                $this->mailboxInput["admin_pass"] = $_arr_adminPass["str"];
            break;
        }

        $_arr_adminMailNew = validateStr(fn_post("admin_mail_new"), 1, 300, "str", "email");

        switch ($_arr_adminMailNew["status"]) {
            case "too_short":
                return array(
                    "rcode" => "x010206",
                );
            break;

            case "too_long":
                return array(
                    "rcode" => "x010207",
                );
            break;

            case "format_err":
                return array(
                    "rcode" => "x010208",
                );
            break;

            case "ok":
                $this->mailboxInput["admin_mail_new"] = $_arr_adminMailNew["str"];
            break;
        }

        $this->mailboxInput["rcode"] = "ok";

        return $this->mailboxInput;
    }


    function input_qa() {
        if (!fn_token("chk")) { //令牌
            return array(
                "rcode" => "x030206",
            );
        }

        $_arr_adminPass = validateStr(fn_post("admin_pass"), 1, 0);
        switch ($_arr_adminPass["status"]) {
            case "too_short":
                return array(
                    "rcode" => "x010212",
                );
            break;

            case "ok":
                $_arr_qaInput["admin_pass"] = $_arr_adminPass["str"];
            break;
        }

        for ($_iii = 1; $_iii <= 3; $_iii++) {
            $_arr_adminSecQues = validateStr(fn_post("admin_sec_ques_" . $_iii), 1, 0);
            switch ($_arr_adminSecQues["status"]) {
                case "too_short":
                    return array(
                        "rcode" => "x010238",
                    );
                break;

                case "ok":
                    $_arr_qaInput["admin_sec_ques_" . $_iii] = $_arr_adminSecQues["str"];
                break;
            }

            $_arr_adminSecAnsw = validateStr(fn_post("admin_sec_answ_" . $_iii), 1, 0);
            switch ($_arr_adminSecAnsw["status"]) {
                case "too_short":
                    return array(
                        "rcode" => "x010237",
                    );
                break;

                case "ok":
                    $_arr_qaInput["admin_sec_answ_" . $_iii] = $_arr_adminSecAnsw["str"];
                break;
            }
        }

        $_arr_qaInput["rcode"]  = "ok";

        return $_arr_qaInput;
    }



    function input_prefer() {
        $this->arr_prefer = fn_post("prefer");

        return $this->arr_prefer;
    }
}
