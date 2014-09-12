<?php
/*-----------------------------------------------------------------
！！！！警告！！！！
以下为系统文件，请勿修改
-----------------------------------------------------------------*/

//不能非法包含或直接执行
if(!defined("IN_BAIGO")) {
	exit("Access Denied");
}

include_once(BG_PATH_FUNC . "http.func.php"); //载入 http
include_once(BG_PATH_CLASS . "sso.class.php"); //载入 SSO
include_once(BG_PATH_CLASS . "tpl.class.php"); //载入模板类
include_once(BG_PATH_MODEL . "admin.class.php"); //载入管理帐号模型

/*-------------用户类-------------*/
class CONTROL_LOGON {

	private $obj_base;
	private $config;
	private $obj_sso;
	private $obj_tpl;
	private $mdl_admin;


	function __construct() { //构造函数
		$this->obj_base   = $GLOBALS["obj_base"]; //获取界面类型
		$this->config     = $this->obj_base->config;
		$this->obj_sso    = new CLASS_SSO(); //SSO
		$this->mdl_admin  = new MODEL_ADMIN(); //设置管理员对象
	}

	/*============登录============
	返回数组
		admin_id UC ID
		str_alert 提示信息
	*/
	function ctl_login() {
		$_arr_adminLogin = $this->input_login();
		if ($_arr_adminLogin["str_alert"] != "ok") {
			return $_arr_adminLogin;
			exit;
		}

		$_arr_ssoLogin = $this->obj_sso->sso_login($_arr_adminLogin["admin_name"], $_arr_adminLogin["admin_pass"]); //sso验证
		if ($_arr_ssoLogin["str_alert"] != "y010401") {
			$_arr_ssoLogin["forward"] = $_arr_adminLogin["forward"];
			return $_arr_ssoLogin;
			exit;
		}

		$_arr_adminRow = $this->mdl_admin->mdl_read($_arr_ssoLogin["user_id"]); //本地数据库处理

		if ($_arr_adminRow["str_alert"] != "y020102") {
			$_arr_adminRow["forward"] = $_arr_adminLogin["forward"];
			return $_arr_adminRow;
			exit;
		}

		if ($_arr_adminRow["admin_status"] == "disable") {
			return array(
				"forward"   => $_arr_adminLogin["forward"],
				"str_alert" => "x020401",
			);
			exit;
		}

		$_str_rand = fn_rand(6);
		$this->mdl_admin->mdl_login($_arr_ssoLogin["user_id"], $_str_rand);


		$_SESSION["admin_id_" . BG_SITE_SSIN]         = $_arr_ssoLogin["user_id"];
		$_SESSION["admin_ssintime_" . BG_SITE_SSIN]   = time();
		$_SESSION["admin_hash_" . BG_SITE_SSIN]       = fn_baigoEncrypt($_arr_adminRow["admin_time"], $_str_rand);

		return array(
			"admin_id"   => $_arr_ssoLogin["user_id"],
			"forward"    => $_arr_adminLogin["forward"],
			"str_alert"  => "y020401",
		);
	}


	/*============登录界面============
	无返回
	*/
	function ctl_logon() {
		$this->obj_tpl    = new CLASS_TPL(BG_PATH_SYSTPL_ADMIN . $this->config["ui"]); //初始化视图对象
		$_str_forward     = fn_getSafe($_GET["forward"], "txt", "");
		$_str_alert       = fn_getSafe($_GET["alert"], "txt", "");

		$_arr_tplData = array(
			"forward"    => $_str_forward,
			"alert"      => $_str_alert,
		);

		$this->obj_tpl->tplDisplay("logon.tpl", $_arr_tplData);
	}

	/*============登出============
	无返回
	*/
	function ctl_logout() {
		$_str_forward  = fn_getSafe($_GET["forward"], "txt", "");
		if (!$_str_forward) {
			$_str_forward = base64_encode(BG_URL_ADMIN . "ctl.php");
		}

		fn_ssin_end();

		return array(
			"forward" => $_str_forward,
		);
	}

	/**
	 * fn_adminLogin function.
	 *
	 * @access public
	 * @return void
	 */
	private function input_login() {
		$_arr_adminLogin["forward"] = fn_getSafe($_POST["forward"], "txt", "");
		if (!$_arr_adminLogin["forward"]) {
			$_arr_adminLogin["forward"] = base64_encode(BG_URL_ADMIN . "ctl.php");
		}

		if (!fn_seccode()) { //验证码
			return array(
				"forward"    => $_arr_adminLogin["forward"],
				"str_alert"  => "x030101",
			);
			exit;
		}

		if (!fn_token("chk")) { //令牌
			return array(
				"forward"    => $_arr_adminLogin["forward"],
				"str_alert"  => "x030102",
			);
			exit;
		}

		$_arr_adminName = validateStr($_POST["admin_name"], 1, 30, "str", "strDigit");
		switch ($_arr_adminName["status"]) {
			case "too_short":
				return array(
					"forward"   => $_arr_adminLogin["forward"],
					"str_alert" => "x020201",
				);
				exit;
			break;

			case "too_long":
				return array(
					"forward"   => $_arr_adminLogin["forward"],
					"str_alert" => "x020202",
				);
				exit;
			break;

			case "format_err":
				return array(
					"forward"   => $_arr_adminLogin["forward"],
					"str_alert" => "x020203",
				);
				exit;
			break;

			case "ok":
				$_arr_adminLogin["admin_name"] = $_arr_adminName["str"];
			break;

		}

		$_arr_adminPass = validateStr($_POST["admin_pass"], 1, 0);
		switch ($_arr_adminPass["status"]) {
			case "too_short":
				return array(
					"forward"   => $_arr_adminLogin["forward"],
					"str_alert" => "x020208",
				);
				exit;
			break;

			case "ok":
				$_arr_adminLogin["admin_pass"] = $_arr_adminPass["str"];
			break;

		}

		$_arr_adminLogin["str_alert"] = "ok";
		$_arr_adminLogin["view"]      = fn_getSafe($_POST["view"], "txt", "");

		return $_arr_adminLogin;
	}

}
?>