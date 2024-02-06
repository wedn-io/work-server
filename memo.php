<?
include "../../engine/dbconn_admin.php";
include "../../inc/function_default.php";

cookie_samesite_custom();

include "../../inc/function_admin.php";
include "../../inc/function_member.php";
include "../../inc/function_shop.php";
include "../../inc/function_goods.php"; // 타운스토리 이머니 가져올때 필요함
include "../../inc/function_etc.php";
include "../../inc/function_login.php";
include "../../inc/function_rsa_key.php";
include "../../engine/dbconn_no_rsync.php";
include "../../inc/shop_config.php";

// 2023-09-14 : LCJ : 내부 | 네이버 로그인시 고유값을 login_naver_email > login_naver_id

/*
if($site_test && $_SERVER[HTTP_HOST] == 'urshop10.anybuild.co.kr'){
	echo "테스트중 ";
	print_r($_SESSION);
	exit;
}
*/
// 2023-04-10 : LCJ : 339901 | 아이디저장, 자동로그인 쿠키가 SSL 없으면 PHP setcookie()로는 생성이 안되어서 JS로 생성.
function cookie_js($name, $val='',$min=0){
	?>
	<script>
		var exdate = new Date();
		exdate.setMinutes(exdate.getMinutes() + <?=$min?>);
		var cookie_value = escape('<?=$val?>') + '; expires=' + exdate.toUTCString();
		document.cookie = '<?=$name?>=' + cookie_value + "; path=/";
	</script>
	<?
}

// 2023-09-14 : LCJ : 내부 | 네이버 로그인시 이용자 식별자를 고유한 12자리 이하의 숫자로 변환.
// https://developers.naver.com/docs/login/devguide/devguide.md#2-2-3-네이버의-로그인-오픈api-이용 참조 : 고유하며 필수인 값이 이용자 식별자 밖에 없는데,
// 네이버 API APP에 따라 21년 5월1일 이후 생성된 앱에서는 숫자가 아닌 BASE64 를 주는데 이거 너무 길어서 naver_[이용자 식별자]로 바로 붙일 수가 없음.
// 따라서 ChatGPT 참조 하여 base64아래 함수를 통해 변환하도록.
function unique_key_to_number($unique_key) {
	if(strlen($unique_key)<20 && $unique_key == ($unique_key*1)) return $unique_key;		// 구형 숫자 unique_key 이면 그대로 반환.

    $decoded_bytes = base64_decode($unique_key);
    $number = 0;
    foreach (str_split($decoded_bytes) as $byte) {
        $number = ($number << 8) | ord($byte);
    }

	if($number < 0) $number *= -1;
	$number = substr($number,0,12);

    return $number;
}

function my_simple_crypt( $string, $action = 'e' ) {
    // 아래값을 임의로 수정해주세요.
    $secret_key = '78hr';
    $secret_iv = '267k';

    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash( 'sha256', $secret_key );
    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

    if( $action == 'e' ) {
        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
    }
    else if( $action == 'd' ){
        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
    }

    return $output;
}


if($_NET_NAME == 'hiorder'){
	include "../../inc/function_hunting_etc.php";

	$d_arr = array();
	$d_arr[type] = 'ACCESS';
	$d_arr[event] = 'LOGIN';
	$d_arr[target] = $id;
	$d_arr[detail] = '로그인';
	$d_arr[personalInfoList] = 'id';

	$lamp_arr = array();
	$lamp_arr[user_id] = $id;
	$lamp_arr[device_id] = 'pc';
	$lamp_arr[transaction_id] = rand(0,9999).'_'.rand(0,9999);
	$lamp_arr[operation_type] = 'logIn';
	$lamp_arr[log_type] = 'IN_REQ';
	$lamp_arr[server_json] = json_encode($_SERVER);
	$lamp_arr[security_json] = json_encode($d_arr);

	$curl_result = curl_load('https://node.hiorder.kt.co.kr/APP_hunting/API/kt_lamp_reg.php','post',$lamp_arr,0,'','','',0.1);
}





/*
if($site_test){
	msg("아람만 출력 :$mall_id // if($MOBILE_CONN_YN && ($deviceuid || ".$_COOKIE["$mall_id|dvc"].")){ //  $APP_CONN_YN // ". $_COOKIE["$mall_id|app_version_code"]);
	exit;
}
*/

//{MY_SOURCE_DEL_START}
if($_SERVER[REMOTE_ADDR] == '110.12.173.78'){
	// $site_test = 1;
}
//{MY_SOURCE_DEL_END}

$set1 = shop_mem_set($mall_id);



//2017-12-05 : 윤 :  sns 로그인은 그냥 통과 시킨다.. - 일단 주석 처리
if($login_mode == 'kakao_app' || $login_mode == 'facebook_app'
	|| $login_mode == 'facebook' || $login_mode == 'twitter'
	|| ($login_mode == 'naver' && $_SESSION[login_naver_id])
	|| ($login_mode == 'kakao' && $_SESSION[login_kakao_id])
	|| ($login_mode == 'google' && $_SESSION[login_google_id])
	){




}else{
	if($_SSL_USE_YN){
		//unset($_POST);
		if(!$ssl_connect_yn && !$ssl_login){
			//etc_ssl_login_chk($mall_id);
			//msg(LTS('SSL 보안 인증 실패 하였습니다.')."\\n\\n".LTS('정상적인 접속을 시도해주세요.')."\\n\\n$add_msg");
			//exit;
		}
	}
}




//{MY_SOURCE_DEL_START}
// SSL 데이타로 넘어온것을 $_POST 배열로 복사하기. if($_GET[ssl_connect_yn]){ 문법으로 검증  (이변수에는 ssl_idx 값으로 들어가있다..
include "../ssl/ssl_request_start.php";
//{MY_SOURCE_DEL_END}

$id = trim($id);
$_POST[id] = trim($_POST[id]);

// 2023-03-20 : ljw : 전송 구간 복호화
$rsa_pwd = ($rsa_pwd); // 2023-05-27 : 손 : 혹시나 트림 지움.
if($rsa_pwd){
	$_POST[pwd] = rsa_key_decrypt($rsa_pwd);
	if(!$_POST[pwd] || $_POST[pwd] == 'null'){
		reload(LTS('로그인 KT 보안모듈을 정상적으로 불러오지 못했습니다. 다시 시도해주세요.'));

		exit;

	}
}


// 2022-03-21 : ljw : 288634 : form_id_name = 회원 설정에 따른 아이디 항목의 placeholder 명으로 보여준다.(기본값 아이디)
$form_id_name = trim($_POST[form_id_name]);
if(!$form_id_name) $form_id_name = LTS('아이디');



$add_msg = LTS('로그인은 [0]회 인증 실패시 [1]분간 로그인 할 수 없으므로 정확하게 입력해주세요.',array('5','30'));

$request_info = '';
if($mall_id == 'hunting' || $_LOCAL_YN){
	// 헌팅일 때만 실행
	$session_id = session_id();

	$d_arr = array();
	$d_arr[post] = $_POST;
	$d_arr['session_id'] = $session_id;
	$d_arr[SERVER_ADDR] = $_SERVER[SERVER_ADDR];
	$d_arr[SERVER_NAME] = $_SERVER[SERVER_NAME];

	$request_info = addslashes(json_encode($d_arr));
}

// 보안 점검.
$log_all_chk_idx = log_login_all_chk('mem_login',$_POST[id],$request_info);


$sess_id = session_id();

$ymd = date("Ymd");
$y = date("Y")*1;
$m = date("m")*1;
$d = date("d")*1;

$time = time();


$shop_mem_set = shop_mem_set($mall_id);
$set2 = shop_emoney_set($mall_id);

//{MY_SOURCE_DEL_auction_ad_START}
if($shop_mem_set[login_ticket]){
	include "../../inc/function_auction_ad.php";
}
//{MY_SOURCE_DEL_auction_ad_END}



$ip = $_SERVER[REMOTE_ADDR];
$br = $_SERVER[HTTP_USER_AGENT];
$br_md5 = md5($br);
$addslashes_br = addslashes($br);
$br_pass = sql_pwd($addslashes_br);
$new_mem_chk = 0;

if($_REQUEST[login_form_name]) $mode = $_REQUEST[login_form_name];

//{MY_SOURCE_DEL_START}

// 관리자 아이디인경우 스마트 디자인 편집모드로 접속 되게 처리 한다.
if(!$APP_CONN_YN && $_POST[id] && $_POST[id] == $mall_id){

	// 2022-07-20 : ljw :
	// 홈페이지에서 로그인 후 관리자 모드 접속 시,
	// [기본정보관리 > 보안 > 관리자 보안 설정]에서 관리자모드 접속 주소 변경했을 경우, 접속을 차단한다.
	$admin_url_change_yn = 0;
	$query = "select uni_idx,shop_id,admin_url_change_yn,admin_url_change_str from $SHOP_DB.shop_system_tmp where shop_id='$mall_id'";
	$result = query($query,$dbconn_slave);
	$admin_sys_tmp = mysqli_fetch_assoc($result);
	if($admin_sys_tmp[uni_idx] && $admin_sys_tmp[admin_url_change_yn]){
		$aram_msg = '';
		if($site_test){
			$aram_msg = '\r\n\r\n아람만:: 2022-07-20 : ljw : [기본정보관리 > 보안 > 관리자 보안 설정]에서 관리자모드 접속 주소 변경했을 경우, 접속을 차단한다.';
		}

		msg(LTS('해당 계정은 보안 설정으로 인해 관리자 로그인을 하실 수 없습니다.').'\r\n\r\n'.LTS('관리자 페이지에서 로그인을 해주세요.').$aram_msg);

		if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동

		exit;
	}

	// 2023-12-29 : KJW : 403236 | [마스터관리 > 홈페이지관리 > 홈페이지관리] 에서 "마스터계정 로그인 허용" 옵션을 관리자만 허용했을 경우, 접속을 차단한다.
	$query = "select shop_id_site_login_auth_yn from $SHOP_DB.shop_system where id='$mall_id'";
	$result = query($query,$dbconn_slave);
	$admin_sys = mysqli_fetch_assoc($result);
	if(!$admin_sys[shop_id_site_login_auth_yn]){
		$aram_msg = '';
		if($site_test){
		  $aram_msg = '\r\n\r\n아람만:: 2023-12-29 : KJW : [마스터관리 > 홈페이지관리 > 홈페이지관리] 에 "마스터계정 로그인 허용" 옵션을 관리자만 허용했을 경우, 접속을 차단한다.';
		}

		msg(LTS('해당 계정은 보안 설정으로 인해 관리자 로그인을 하실 수 없습니다.').'\r\n\r\n'.LTS('관리자 페이지에서 로그인을 해주세요.').$aram_msg);

		if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동

		exit;
	}

	$query = "insert into $NO_RSYNC_DB.ssl_post_all set
				shop_id	 = '$mall_id'
				,ip = '$ip'
				,this_domain = '$_SERVER[HTTP_HOST]'
				,post_action = '/admin/sub_login/login_ok.php'
				,reg_time = '$time'
				,post_name1 = 'id'
				,post_value1 = '$_POST[id]'
				,post_name2 = 'bu_id'
				,post_value2 = 'root'
				,post_name3 = 'pwd'
				,post_value3 = '$_POST[pwd]'
				,post_name4 = 'smart_admin_yn'
				,post_value4 = 1";
	query($query,$dbconn_no_rsync);
	$admin_ssl_idx = mysqli_insert_id($dbconn_no_rsync);

	if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('I'); // 2023-04-06 :: ljw :: KT lamp 연동

	?>
	<script>
		location.href="/admin/sub_login/login_ok.php?ssl_connect_yn=<?=$admin_ssl_idx?>";
	</script>
	<?
	exit;

}


// 입력한 아이디가 다른 관리자 계정 아이디 인지 확인 한다.
//회원 환경설정에서 타사이트 관리자 로그인 설정이 되어있을 경우
$admin_login_yn = 0;
if($shop_mem_set[site_admin_login_yn]){
	$query = "select * from $SHOP_DB.shop_base where id='$_POST[id]'";
	$order_admin_result = query($query,$dbconn_admin);
	$order_admin_row = mysqli_fetch_assoc($order_admin_result);
	if($order_admin_row[id]){
		$tmp_pwd = sql_pwd(stripslashes($_POST[pwd]));
		if($tmp_pwd && $_POST[pwd] && $tmp_pwd == $order_admin_row[pwd]){
			$login_mode = 'admin';
			$admin_login_yn = 1;
		}
	}
}

//{MY_SOURCE_DEL_END}


if($login_mode == 'admin'
	|| $login_mode == 'kakao_app'
	|| $login_mode == 'facebook_app'
	|| $login_mode == 'facebook'
	|| $login_mode == 'twitter'
	|| ($login_mode == 'naver' && $_SESSION[login_naver_id])
	|| ($login_mode == 'kakao' && $_SESSION[login_kakao_id])
	|| ($login_mode == 'google' && $_SESSION[login_google_id])
	){
	// SNS 통해서 로그인 하는경우  강제로 회원가입을 시키자...

	//	name: response.name, facebook_id: response.id, email: response.email,gender: response.gender, link:response.link, login_mode:"facebook"},

	//2020-07-30 : 배 : SNS 로그인시 회원 가입 정보 입력받을지 말지 설정값을 호출합니다.
	$query = "select sns_join_yn from $SHOP_DB.shop_jh where id = '$mall_id' ";
	$result = query($query,$dbconn_slave);
	$jh_row = mysqli_fetch_assoc($result);

	$sex = 0;

	// 2023-11-14 : LCJ : 387706 | 간편로그인 후 인증 거쳐야 할 때 간편로그인 정보 다시 살리기.
	$sns_login_id = "";

	if($admin_login_yn){
		// 관리자 계정 아이디 연동
		$id = "admin:$_POST[id]";

		$no=0;
		$query = "select domain from $SHOP_DB.shop_domain where shop_id='$_POST[id]' order by base_yn limit 1";
		$result = query($query,$dbconn_admin);
		$row = mysqli_fetch_row($result);
		$link = $row[domain];
		$name = $order_admin_row[ceo_name];
		$nickname = $order_admin_row[shop_name];
		$email = $order_admin_row[help_email];
		$hp = $order_admin_row[help_hp];
		$tel = $order_admin_row[help_tel];

		$zipcode = $order_admin_row[zipcode];
		$addr1 = $order_admin_row[addr1];
		$addr2 = $order_admin_row[addr2];
		$biz_num = $order_admin_row[regno];
		$sangho = $order_admin_row[company_name];


		if($order_admin_row[shop_logo_img]){
			$sns_photo_img_url = "${_HTTP_STR}$_SERVER[HTTP_HOST]/img_up/shop_pds/$_POST[id]/etc/$order_admin_row[shop_logo_img]";
		}

		/*
		$file_source = curl_load($sns_photo_img_url,'get','',1);
		echo " $sns_photo_img_url // $file_source // ";
		if($site_test) exit;
		*/


	}else if($login_mode == 'kakao_app'){


		if($_GET[encode_type] == 'json'){

			// 2020-03-08 : 손 : 니미... ios 는 별 지랄을 다해도 문자열이 깨진다... 유일하게 json 으로 디코딩하니깐 된다...
			$tmp_json_str = '{';
			$tmp_json_str .= '"kakao_id":"'.($_GET[kakao_id]).'"';
			$tmp_json_str .= ',"kakao_nickname":"'.($_GET[kakao_nickname]).'"';
			$tmp_json_str .= ',"my_device_uid":"'.($_GET[my_device_uid]).'"';
			$tmp_json_str .= ',"kakao_img_url":"'.($_GET[kakao_img_url]).'"';
			$tmp_json_str .= ',"kakao_email":"'.($_GET[kakao_email]).'"';
			$tmp_json_str .= ',"kakao_hp":"'.($_GET[kakao_hp]).'"';
			$tmp_json_str .= ',"gender":"'.($_GET[gender]).'"';
			$tmp_json_str .= '}';
			$json_arr = json_decode($tmp_json_str,true);

			$_GET[kakao_id] = addslashes($json_arr[kakao_id]);
			$_GET[kakao_nickname] = addslashes($json_arr[kakao_nickname]);
			$_GET[my_device_uid] = addslashes($json_arr[my_device_uid]);
			$_GET[kakao_img_url] = addslashes($json_arr[kakao_img_url]);
			$_GET[kakao_email] = addslashes($json_arr[kakao_email]);
			$_GET[kakao_hp] = addslashes($json_arr[kakao_hp]);
			$_GET[gender] = addslashes($json_arr[gender]);

		}


		// 안드로이드는 base64 인코딩 해서 전달한다.
		if($_GET[kakao_email_b64]){
			$_GET[kakao_email] = base64_decode($_GET[kakao_email_b64]);
		}
		if($_GET[kakao_hp_b64]){
			$_GET[kakao_hp] = base64_decode($_GET[kakao_hp_b64]);
		}


		/*
		if($site_test){
			echo "아람만 출력";
			foreach($_GET as $k => $v){
				echo "_GET[$k] : $v \n";
			}
			echo "\n";
			foreach($_POST as $k => $v){
				echo "_POST[$k] : $v \n";
			}
			echo "APP_CONN_YN : $APP_CONN_YN \n";
		}
		*/

		if(!$_GET[my_device_uid]){
			echo "정보 누락\n";
			if($site_test) echo "아람만 출력 if(!_GET[my_device_uid]){ \n";

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}

		$my_device_uid = $_GET[my_device_uid];
		$query = "select * from $TB[CLIENT_DEVICE] where shop_id='$mall_id' and deviceuid='$my_device_uid'";
		$device_result = query($query,$dbconn_admin);
		$device_row = mysqli_fetch_assoc($device_result);
		if(!$device_row[idx]){
			echo "알수 없는 디바이스 입니다.\n";

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}

		$kakao_id = str_replace('\\','',$_GET[kakao_id]);
		$kakao_id = str_replace('"','',$kakao_id);
		$kakao_id = str_replace('\'','',$kakao_id);
		$kakao_id = str_replace("\t",'',$kakao_id);
		$kakao_id = str_replace("\r\n",'',$kakao_id);
		$kakao_id = str_replace("\n",'',$kakao_id);
		$kakao_id = trim($kakao_id);
		if(!$kakao_id){
			echo "정보 누락\n";

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}


		$email = '';
		$hp = '';
		$name = $_GET[kakao_nickname];
		$sns_photo_img_url = $_GET[kakao_img_url];
		$email = $_GET[kakao_email];
		$hp = $_GET[kakao_hp];
		$gender = $_GET[gender];

		$age = '';

		$id = "kakao:$kakao_id";

		if($mall_id == 'hunting'){
			//db_error("손 테스트 ok $kakao_id // $name // $my_device_uid // $sns_photo_img_url //   ",__FILE__.'('.__LINE__.')',1);
		}



	}else if($login_mode == 'facebook_app'){

		$id = "facebook:".base64_decode($_GET[facebook_id]);
		$email = base64_decode($_GET[email]);
		$gender = base64_decode($_GET[gender]);
		$link = base64_decode($_GET['link']);

		//{MY_SOURCE_DEL_START}
		if($site_test)msg("아람만 출력 $id // $email // $gender // $link");
		//{MY_SOURCE_DEL_END}

		// https://graph.facebook.com/729462057109288/picture
		$sns_photo_img_url = "https://graph.facebook.com/".base64_decode($_GET[facebook_id])."/picture";


	}else if($login_mode == 'facebook'){

		$id = "facebook:$_POST[facebook_id]";


		// -------------------------------------------------------------------------------------- 내부 보안 시작  : 페이스북은 자바스크립트로 통신하다보니 보안이 불가능하다.. 1818
		$sess_id = session_id();
		if(!$_SESSION[login_facebook_chk_key]){
			msg("처음부터 다시 로그인 해주세요.(1)");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}

		$query = "select * from $MEMBER_LOG_DB.api_login_log
						where shop_id = '$mall_id'
						and sess_id = '$sess_id'
						and login_mode = 'facebook'
						order by reg_time desc
						limit 1
						";
		$result = query($query,$dbconn_admin);
		$api_log = mysqli_fetch_assoc($result);
		if(!$api_log[idx]){
			msg("처음부터 다시 로그인 해주세요. (2)");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		if($api_log[exec_ok_yn]){
			msg("처음부터 다시 로그인 해주세요. (3)");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		if($api_log[chk_key] != $_SESSION[login_facebook_chk_key]){
			msg("보안키 값이 일치 하지 않습니다.");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		if($api_log[ip] != $_SERVER[REMOTE_ADDR]){
			msg("ip주소가 변조 되었습니다.");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		// 초기화 처리 한다.
		$_SESSION[login_facebook_chk_key] = '';

		$add_login_id = addslashes($_POST[facebook_id]);
		$query = "update $MEMBER_LOG_DB.api_login_log set
						exec_ok_yn = 1
						,login_id = '$add_login_id'
					where idx = $api_log[idx]
					";
		query($query,$dbconn_admin);

		// -------------------------------------------------------------------------------------- 내부 보안 끝


		// https://graph.facebook.com/729462057109288/picture
		$sns_photo_img_url = "https://graph.facebook.com/$_POST[facebook_id]/picture";

		/*
		$file_source = curl_load($sns_photo_img_url,'get','',1);
		echo " $sns_photo_img_url // $file_source // ";
		if($site_test) exit;
		*/


	}else if($login_mode == 'twitter'){

		if(!$_SESSION[SNS_ID] || !$_SESSION[SNS_NAME]){
			?>
			<script type="text/javascript">
				alert("<?=LTS('트위터 로그인 실패 하였습니다.')?>");
			</script>
			<?

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}

		$id = "twitter:$_SESSION[SNS_ID]";

		$link = $_SESSION[SNS_SITE];
		$name = $_SESSION[SNS_NAME];
		$sns_photo_img_url = $_SESSION[SNS_PHOTO_URL];

		$_SESSION[twitter_popup_yn] = '';
		$_SESSION[SNS_SITE] = '';
		$_SESSION[SNS_NAME] = '';
		$_SESSION[SNS_PHOTO_URL] = '';
		$_SESSION[SNS_ID] = '';



	}else if($login_mode == 'naver' && $_SESSION[login_naver_id]){

		$unique_key = $_SESSION[login_naver_id];
		$email = $_SESSION[login_naver_email];
		$name = $_SESSION[login_naver_name];
		$nickname = $_SESSION[login_naver_nickname];
		$sns_photo_img_url = $_SESSION[login_naver_img];

		$birthyear = $_SESSION[login_naver_brithyear]; //출생연도
		$hp = $_SESSION[login_naver_mobile];

		$age_string = $_SESSION[login_naver_age]; // 30-39
		$tmp_arr = explode('-',$age_string);
		$age = $tmp_arr[0];


		//  ----------------------------------------------- 다시 검증 한다. 시작
		$query = "select * from $MEMBER_LOG_DB.api_login_log
						where shop_id = '$mall_id'
						and sess_id = '$sess_id'
						and login_mode = 'naver'
						order by reg_time desc
						limit 1
						";
		$result = query($query,$dbconn_admin);
		$api_log = mysqli_fetch_assoc($result);
		if(!$api_log[idx]){
			msg("처음부터 다시 로그인 해주세요. (2-1)");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		if(!$api_log[exec_ok_yn]){
			msg("처음부터 다시 로그인 해주세요. (2-2)");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		if($api_log[ip] != $_SERVER[REMOTE_ADDR]){
			msg("ip주소가 변조 되었습니다. 다시 로그인 해주세요.");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		// 2023-09-14 : LCJ : 내부 | 네이버 로그인 기준을 변경. login_id > unique_key, login_naver_email > login_naver_id
		if($api_log[unique_key] != $_SESSION[login_naver_id]){
			msg("로그인 아이디가 변조 되었습니다. 다시 로그인 해주세요.");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}

		//  ----------------------------------------------- 다시 검증 한다. 끝


		// 2023-09-14 : LCJ : 내부 | 네이버 아이디 생성을 unique_key 에 따라 생성하도록 변경. 메일은 구주소만.
		$unique_key = unique_key_to_number($unique_key);
		$id = "naver:$unique_key";

		$tmp_arr = explode('@',$email);
		$naver_id = $tmp_arr[0];
		$old_id = "naver:$naver_id";
		if($naver_id == 'id' || !$naver_id){
			$old_id .= $unique_key;
		}
		$birthday_md = $_SESSION[login_naver_birthday]; // 12-25
		$gender = $_SESSION[login_naver_gender];

		if(!$birthyear){
			$birthday = ($y-$age).'-'.$birthday_md;
		}else{
			$birthday = $birthyear.'-'.$birthday_md;
		}

		$_SESSION[login_naver_id] = '';
		$_SESSION[login_naver_email] = '';
		$_SESSION[login_naver_name] = '';
		$_SESSION[login_naver_nickname] = '';
		$_SESSION[login_naver_img] = '';
		$_SESSION[login_naver_age] = '';
		$_SESSION[login_naver_birthday] = '';
		$_SESSION[login_naver_gender] = '';
		$_SESSION[login_naver_brithyear] = '';
		$_SESSION[login_naver_mobile] = '';



	}else if($login_mode == 'kakao' && $_SESSION[login_kakao_id]){

		$id = "kakao:$_SESSION[login_kakao_id]";
		$sex = $_SESSION[login_kakao_sex];
		$birthday_md = $_SESSION[login_kakao_birthday];
		$birthday = $_SESSION[login_kakao_birthyear].'-'.$birthday_md;

		$age = $_SESSION[login_kakao_age];
		$email = $_SESSION[login_kakao_email];
		$hp = $_SESSION[login_kakao_hp];
		$name = $_SESSION[login_kakao_nickname];
		$sns_photo_img_url = $_SESSION[login_kakao_img];

		//  ----------------------------------------------- 다시 검증 한다. 시작
		$query = "select * from $MEMBER_LOG_DB.api_login_log
						where shop_id = '$mall_id'
						and sess_id = '$sess_id'
						and login_mode = 'kakao'
						order by reg_time desc
						limit 1
						";
		$result = query($query,$dbconn_admin);
		$api_log = mysqli_fetch_assoc($result);
		if(!$api_log[idx]){
			msg("처음부터 다시 로그인 해주세요. (2-1)");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		if(!$api_log[exec_ok_yn]){
			msg("처음부터 다시 로그인 해주세요. (2-2)");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		if($api_log[ip] != $_SERVER[REMOTE_ADDR]){
			msg("ip주소가 변조 되었습니다. 다시 로그인 해주세요.");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		if($api_log[login_id] != $_SESSION[login_kakao_id]){
			msg("로그인 아이디가 변조 되었습니다. 다시 로그인 해주세요.");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}

		//  ----------------------------------------------- 다시 검증 한다. 끝
		$_SESSION[login_kakao_nickname] = '';
		$_SESSION[login_kakao_img] = '';
		$_SESSION[login_kakao_id] = '';
		$_SESSION[login_kakao_age] = '';
		$_SESSION[login_kakao_email] = '';
		$_SESSION[login_kakao_birthday] = '';
		$_SESSION[login_kakao_birthyear] = '';
		$_SESSION[login_kakao_sex] = '';


	}else if($login_mode == 'google' && $_SESSION[login_google_id]){

		$email = $_SESSION[login_google_email];
		$name = $_SESSION[login_google_name];
		$sns_photo_img_url = $_SESSION[login_google_img];

		//  ----------------------------------------------- 다시 검증 한다. 시작
		$query = "select * from $MEMBER_LOG_DB.api_login_log
						where shop_id = '$mall_id'
						and sess_id = '$sess_id'
						and login_mode = 'google'
						order by reg_time desc
						limit 1
						";
		$result = query($query,$dbconn_admin);
		$api_log = mysqli_fetch_assoc($result);
		if(!$api_log[idx]){
			msg("처음부터 다시 로그인 해주세요. (2-1)");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		if(!$api_log[exec_ok_yn]){
			msg("처음부터 다시 로그인 해주세요. (2-2)");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		if($api_log[ip] != $_SERVER[REMOTE_ADDR]){
			msg("ip주소가 변조 되었습니다. 다시 로그인 해주세요.");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}
		if($api_log[login_id] != $_SESSION[login_google_id]){
			msg("로그인 아이디가 변조 되었습니다. 다시 로그인 해주세요.");

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}

		//  ----------------------------------------------- 다시 검증 한다. 끝

		$id = "google:$_SESSION[login_google_id]";

		$login_mode = 'google';

		// 세션 초기화
		$_SESSION[login_google_email] = '';
		$_SESSION[login_google_name] = '';
		$_SESSION[login_google_img] = '';
		$_SESSION[login_google_id] = '';

	}else{
		msg(LTS('알수없는 정보 입니다.'));

		if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
		exit;

	}
	$sns_login_id = $id;

	if($gender == 'male' || $gender == 'M'){
		$sex = 1;
	}elseif($gender == 'woman' || $gender == 'female' || $gender == 'lady' || $gender == 'gir' || $gender == 'F' || $gender == 'W'){
		$sex = 2;
	}

	if($mall_id != 'local'){
		$file_source = url_open($sns_photo_img_url);
	}

	$hp = trim($hp);
	if(strpos(" $hp",'null')) $hp = '';

	if($hp){
		if(substr($hp,0,3) == '+82'){
			//+82 10-7103-1263
			$hp = '0'.substr($hp,3);
		}

		if(substr($hp,0,2) == '82'){
			//82 10-7103-1263
			$hp = '0'.substr($hp,2);
		}
		$hp = trim($hp);
	}

	$photo_file_name = '';
	if($file_source){

		$photo_file_name = str_replace(':','_',$id).'.jpg';

		$dir = "$CONF_DOCUMENT_ROOT/img_up/shop_pds/$mall_id/member";
		make_dir($dir);

		$wfp=fopen("$dir/$photo_file_name", "wb");
		fwrite($wfp,$file_source);
		fclose ($wfp);
		@chmod("$dir/$photo_file_name",0777);
	}



	if(!$nickname)  $nickname = $name;

	$nickname = addslashes($nickname);
	$name = addslashes($name);



	$query = "select idx,memlv,reg_time from $TB[MYMEM] where shop_id='$mall_id' and mem_id='$id'";
	$result = query($query,$dbconn_admin);
	$row = mysqli_fetch_assoc($result);
	if(!$row[idx] && $login_mode=='naver' && $old_id){		// 2023-09-14 : LCJ : 내부 | 네이버 로그인시 구아이디 지원
		$query = "select idx,memlv,reg_time from $TB[MYMEM] where shop_id='$mall_id' and mem_id='$old_id'";
		$result = query($query,$dbconn_admin);
		$row = mysqli_fetch_assoc($result);
		if($row[idx]) $id = $old_id;

	}
	if(!$row[idx]){
		// 강제로 신규가입 처리 한다.
		if($sex == 2){
			// 여성인 경우...
			$ok_memlv = $set1[new_reg_lv_w];
		}else{
			$ok_memlv = $set1[new_reg_lv];
		}
		if(!$ok_memlv) $ok_memlv = 100;

		$pwd = "$id$time";
		//$enc_pwd = md5($pwd);
		$enc_pwd = hash("sha256", ($pwd));


		$referer = '';
		if($_COOKIE[conn_referer]){
			$referer = $_COOKIE[conn_referer];
		}else{
			if($APP_CONN_YN){
				$referer = " APP $_SERVER[REQUEST_URI] ";
			}else if($MOBILE_CONN_YN){
				$referer = " MOBILE $_SERVER[REQUEST_URI] ";
			}else{
				$referer = " PC $_SERVER[REQUEST_URI] ";
			}
		}
		$referer = addslashes($referer);
		$new_mem_chk = 1;

		$query = "insert into $TB[MYMEM] set
					shop_id = '$mall_id'
					,reseller_id = '$reseller_id'
					,mem_id = '$id'
					,mem_name = '$name'
					,memlv = '$ok_memlv'
					,pwd = '$enc_pwd'
					,nickname = '$nickname'
					";
					if($sex) $query .= " ,sex = $sex ";
					if($biz_num) $query .= ",biz_num = '$biz_num'";
					if($sangho) $query .= ",sangho  = '$sangho'";
					if($zipcode) $query .= ",zipcode = '$zipcode'";
					if($addr1) $query .= ",addr1 = '$addr1'";
					if($addr2) $query .= ",addr2 = '$addr2'";

					if($birthday) $query .= ",birthday = '$birthday'";
					if($birthday_md) $query .= ",birthday_md = '$birthday_md'";

					if($hp) $query .= ",hp = '$hp'";
					if($tel) $query .= ",tel = '$tel'";


					if($link) $query .= ",homepage = '$link'";
					if($email) $query .= ",email = '$email'";
					if($age) $query .= ",age = '$age'";
					if($photo_file_name) $query .= ",photo = '$photo_file_name'";

		$query .= "
					,y = $y
					,m = $m
					,d = $d
					,reg_time = $time
					,ip = '$ip'
					,br = '$addslashes_br'
					,referer = '$referer'
					,banner_info_idx = '$_SESSION[banner_info_idx]'
					,banner_link_code = '$banner_link_code_in'
					,banner_ecash = '$banner_ecash_in'
					,banner_id = '$banner_id_in'
					,susin_sms = 1
					,susin_email = 1
					,reg_mode = '$login_mode'
					";

		query($query,$dbconn_admin);


		//2016-11-29 : 윤 : sns 로 로그인을 할 경우 여기에서 회원DB에 저장되므로 최초 저장시 회원 환경설정의 각종 적립을 적용한다.
		// ------------------------------------ 이머니 적립 --------------------------------------
		if($set2[emoney_use_yn] && $shop_mem_set[new_emoney]){
			emoney_add($mall_id,$id,$shop_mem_set[new_emoney],LTS('신규가입 축하 e-Money'));
		}

		// ------------------------------------ 포인트 적립 --------------------------------------
		if($set2[point_use_yn] && $shop_mem_set[new_point]){
			point_add($mall_id,$id,$shop_mem_set[new_point],LTS('신규가입 축하 포인트'));
		}

		// ------------------------------------ 쿠폰 적립 --------------------------------------
		if($set2[coupon_use_yn] && $shop_mem_set[new_coupon_idx]){
			$query = "select * from $SHOP_DB.shop_coupon where idx = $shop_mem_set[new_coupon_idx]";
			$result = query($query,$dbconn_admin);
			$row = mysqli_fetch_assoc($result);
			$coupon_subject = addslashes($row[subject]);
			$use_day = date("Y-m-d H:i",$row[s_date]) . ' ~ ' . date("Y-m-d H:i",$row[e_date]);

			if($row[dc_type]=='price') $dc_str = number_format($row[dc_int]).'원 할인';
			else $dc_str = $row[dc_int].'% 할인';

			if($row[idx] && $row[id] == $mall_id && $row[s_date] < $time && $row[e_date] > $time){
				$query = "insert into $TB[COUPON_LIST] set
							shop_id = '$mall_id'
							,mem_id = '$id'
							,mem_name = '$name'
							,coupon_idx = $row[idx]
							,coupon_subject = '$coupon_subject'
							,bigo = '신규 회원가입 축하'
							,reg_time  = $time ";
				query($query,$dbconn_admin);

				$query = "update $SHOP_DB.shop_coupon set
								save_cnt = save_cnt+1
								where idx = $row[idx] ";
				query($query,$dbconn_admin);
			}
		}

		// ------------------------------------ 쪽지 전송 --------------------------------------
		if(!$shop_mem_set[auto_s_paper_sel]){
			if($shop_mem_set[auto_s_paper_all] && $shop_mem_set[auto_s_paper_content3]){
				//성별관계없이 보낼경우 - sns 로그인에서는 성별 확인이 거의 없으므로 공통 쪽지만 발송한다.
				$shop_mem_set[auto_s_paper_content3] = addslashes($shop_mem_set[auto_s_paper_content3]);
				$query = "insert into $TB[MEMO_LIST] set
							mem_id = '$id'
							,mem_name = '$name'
							,b_mem_id = ''
							,b_mem_name = ''
							,shop_id = '$mall_id'
							,content = '$shop_mem_set[auto_s_paper_content3]'
							,content_type = '$shop_mem_set[auto_s_paper_all_type]'
							,reg_time = $time
							";
				query($query,$dbconn_admin);
			}
		}

	}else{

		if($row[memlv] == 300){
			msg_back('탈퇴회원은 재가입 할 수 없습니다.');

			if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			exit;
		}

		$pwd = "$id$row[reg_time]";
		//$enc_pwd = md5($pwd);
		$enc_pwd = hash("sha256", ($pwd));

		$query = "update $TB[MYMEM] set
						mem_name = '$name'
						,pwd = '$enc_pwd'
						";

					if($sex) $query .= " ,sex = $sex ";
					if($biz_num) $query .= ",biz_num = '$biz_num'";
					if($sangho) $query .= ",sangho  = '$sangho'";
					if($zipcode) $query .= ",zipcode = '$zipcode'";
					if($addr1) $query .= ",addr1 = '$addr1'";
					if($addr2) $query .= ",addr2 = '$addr2'";

					if($birthday) $query .= ",birthday = '$birthday'";
					if($birthday_md) $query .= ",birthday_md = '$birthday_md'";

					if($hp) $query .= ",hp = '$hp'";
					if($tel) $query .= ",tel = '$tel'";


					if($link) $query .= ",homepage = '$link'";
					if($email) $query .= ",email = '$email'";
					if($age) $query .= ",age = '$age'";
					if($photo_file_name) $query .= ",photo = '$photo_file_name'";

		$query .= "
					where shop_id='$mall_id' and mem_id='$id'";
		query($query,$dbconn_admin);
	}

	$_POST[id] = $id;
	$_POST[pwd] = $pwd;

}



session_db('save');


if($_POST[id]){
	//이메일 인증을 사용할 경우 인증이 되지 않은 아이디는 인증페이지로 넘긴다.
	$query = "select idx from $TB[MYMEM] where shop_id='$mall_id' and mem_id='$_POST[id]' and memlv = 400";
	$result = query($query,$dbconn_admin);
	$row = mysqli_fetch_row($result);
	$tmp_mem_id = base64_encode($_POST[id]);
	if($row[0]){

		if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
		?>
		<script type="text/javascript">
		top.location.href = "email_confirm_wait.htm?tmp_id=<?=$tmp_mem_id?>";
		</script>
		<?
		exit;
	}
}

// PC웹 <--> 모바일웹 전환시 자동 로그인 되게 처리 한다.

$pwd = $_POST[pwd]; // addslashes 처리 되어 있음..




if($_POST[id] && $_POST[mem_id_hash]){

	// 데이타 변조를 방지하기 위해 실재 로그인 했을때 ip랑 같은지 확인 한다.
	$query = "select *
				from $TB[LOG_LOGIN]
				where shop_id='$mall_id'
				and mem_id = '$_POST[id]'
				order by reg_time desc
				limit 1
				";
	$result = query($query,$dbconn_admin);
	$row = mysqli_fetch_assoc($result);

	if($row[ip] == $_SERVER[REMOTE_ADDR]){

		$chk_hash = md5("$_POST[id]x$_SERVER[REMOTE_ADDR]x$_SERVER[HTTP_USER_AGENT]".date("YmdH"));

		if($chk_hash == $_POST[mem_id_hash]){

			$query = "select pwd from $TB[MYMEM] where shop_id='$mall_id' and mem_id='$_POST[id]'";
			$result = query($query,$dbconn_admin);
			$row = mysqli_fetch_row($result);

			$pwd = addslashes($row[0]);
		}

	}else{
		if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동

		?>
		<script type="text/javascript">
			alert('로그인 실패 하였습니다.');
			top.location.href="/";
		</script>
		<?
		exit;
	}
}


//{MY_SOURCE_DEL_START}
// 보안서버를 통해 로그인 하는 경우  (2016-01-09 : 손 : 이건 구버젼이다..)
if($ssl_login){

	$query = "select * from $NO_RSYNC_DB.ssl_data where shop_id='$mall_id' and ip='$ip' order by idx desc limit 1";
	$result = query($query,$dbconn_no_rsync);
	$ssl = mysqli_fetch_assoc($result);

	if(!$ssl[idx]){
		msg(LTS('보안로그인 실패하였습니다. 자세한 내용은 관리자에게 문의 바랍니다.').'(error:login_001)');

		if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
		exit;
	}

	if($ssl[br_pass] != $br_pass){
		//if($site_test) echo "아람만 출력 : $query <br>\n$br <Br>\n if($ssl[br_pass] != $br_pass){\n";
		//msg(LTS('보안로그인 실패하였습니다. 자세한 내용은 관리자에게 문의 바랍니다.').'(error:login_002)');
		// exit;
	}

	$id = addslashes($ssl[m_id]);
	$pwd = addslashes($ssl[m_pwd]);

	if(!$site_test){
		$query = "delete from $NO_RSYNC_DB.ssl_data where idx=$ssl[idx]";
		query($query,$dbconn_no_rsync);
	}

	$mode = $ssl[jumun_login_mode];
	$direct_yn = $ssl[jumun_login_direct_yn];

}
//{MY_SOURCE_DEL_END}

$id = strtolower(trim($id));
if(!$id){
	msg(LTS('아이디를 입력하세요.')."\\n\\n$add_msg");

	if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
	exit;
}


//{MY_SOURCE_DEL_START}
/*
if(substr($id,0,4) == 'test'){
	msg(LTS('죄송합니다. 보안상 test로 시작하는 아이디는 로그인 할 수 없습니다. 자세한 내용은 관리자에게 문의 바랍니다.'));
	exit;
}
*/

$query = "select * from $SHOP_DB.shop_base where id='$mall_id'";
$result = query($query,$dbconn_admin);
$base_row = mysqli_fetch_assoc($result);
if(!$base_row[id]){
	msg(LTS('회원가입 할수 없습니다.')."\\n\\n".LTS('관리자에게 문의 바랍니다.'));

	if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
	exit;
}



if($id == 'sskcom' || $id == 'sskcom1' || $id == 'sskcom2' || $id == 'asasas1'  || $id == 'asasas2' ){
	if(!$site_test){
		msg('해당 아이디는 테스트용 아이디 이므로 로그인 할 수 없습니다.'."\\n\\n$add_msg");

		if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
		exit;
	}
}


//2018-12-17 : 윤 : 로그인시 휴면계정 로그가 남아있을 경우 기존 회원등급으로 복원하고 로그는 삭제한다.
$query = "select * from $MEMBER_LOG_DB.log_mem_rest where shop_id = '$mall_id' and mem_id = '$id' ";
$result = query($query,$dbconn_admin);
$row = mysqli_fetch_assoc($result);
if($row[idx]){
	$query = "select idx,mem_id,memlv from $TB[MYMEM] where shop_id='$mall_id' and mem_id = '$id' ";
	$m_result = query($query,$dbconn_admin,__FILE__.' : '.__LINE__);
	$m_row = mysqli_fetch_assoc($m_result);
	if($m_row[idx] && $m_row[memlv] == 500){
		$query = "update $TB[MYMEM] set memlv = '$row[memlv]' where idx = $m_row[idx] ";
		query($query,$dbconn_admin,__FILE__.' : '.__LINE__);
	}

	$query = "delete from $MEMBER_LOG_DB.log_mem_rest where idx = $row[idx] ";
	query($query,$dbconn_admin);
}



//실명인증 유효기간 사용시 -  관리자는 패스
if(!$_SESSION[MYSHOP_ID] && $mall_id != $id){
	if($site_test){ // TODO : IKH 테스트 후 수정
		// 2024-01-31 : IKH : 415061 | 실명인증 관련 옵션 관련 오류 수정
		/* 기본정보관리 실명인증 설정을 불러온다 */
		$query = "select * from $SHOP_DB.shop_etc where id ='$mall_id'";
		$result = query($query,$dbconn_slave);
		$shop_etc = mysqli_fetch_assoc($result);

		//2017-09-28 : 윤 : 실명인증 로그인 사용시 인증 내역이 하나도 없을 경우 인증 후 로그인이 가능하도록 한다.
		if($shop_etc[siren_use_yn] && $shop_mem_set[login_siren_yn]){ /* 실명인증 관련 상위 옵션, 하위 옵션 모두 사용 상태일 때 */
			/* adult_confirmation_name 컬럼추가 */
			$query = "select idx,ipin_chk,ipin_chk_time,memlv,adult_confirmation_name from $TB[MYMEM] where shop_id = '$mall_id' and mem_id = '$id' ";
			$result = query($query,$dbconn_admin);
			$row = mysqli_fetch_assoc($result);
			if(!$row[idx]){
				// 2021-12-10 : LCJ : 285353, 웹 취약점 점검결과 조치방안으로 아이디 오류 메시지와 비번 오류 메시지를 같게 (아이디 오류 메시지에서 아이디 특정 가능), 기존 메시지는 주석 처리
				//msg(LTS('입력하신 [0]는 존재 하지 않는 아이디 입니다.',array($id))."\\n\\n$add_msg");

				// 2022-03-21 : ljw : 288634 : form_id_name = 회원 설정에 따른 아이디 항목의 placeholder 명으로 보여준다.(기본값 아이디)
				msg(LTS('[0]와(과) 패스워드가 일치하지 않습니다.',array($form_id_name))."\\n\\n$add_msg");

				if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
				exit;
			}

			$login_siren_chk = 1; /* 인증정보 초기화 >>> 인증요청 1, 인증패스 0 */
			if($shop_mem_set[login_siren_memlv]){
				if(!strpos(" $shop_mem_set[login_siren_memlv]","|M|") && !strpos(" $shop_mem_set[login_siren_memlv]","|$row[memlv]|")){
					$login_siren_chk = 0; /* 인증이 필요한 회원등급에 포함되어있지 않다면 인증패스 */
				}
			}

			/* 인증이 필요한 등급에 포함 되어 있고, 인증세션이 없다면 */
			if($login_siren_chk && !$row[ipin_chk] && !$_SESSION[SIREN_LOG_IDX] && !$_SESSION[OKCERT_LOG_IDX]){
				$ipin_login_chk = 1; /* 인증정보 초기화 >>> 인증요청 1, 인증패스 0 */
			}

			if(!$ipin_login_chk && $shop_etc[siren_auth_day]){ /* 아이핀 인증이 필요하고 인증 유지시간이 설정되어 있다면 */
				$ipin_chk_time = $time - ($shop_etc[siren_auth_day] * 86400); /* 아이핀 체크 시간 계산 */

				$tmp_query = '';

				if($row[ipin_chk_time]){ /* 아이핀 인증기록시간이 있다면 */
					if($row[ipin_chk_time] < $ipin_chk_time) $ipin_login_chk = 1;
				}else{
					/* 아이핀 인증기록이 있다면 */
					if($row[ipin_chk]){
						if($row[adult_confirmation_name]=='hp'){			// OKCERT
							$tmp_query = " and DI = '$row[ipin_chk]' order by reg_time desc limit 1";
						}else{
							$tmp_query = " and discrHash = '$row[ipin_chk]' order by reg_time desc limit 1";
						}
					}else{
						/* 아이핀 인증기록이 없다면 인증요청 */
						$ipin_login_chk = 1;
					}
				}

				if($tmp_query){
					/* 각각의 인증정보를 확인 */
					if($row[adult_confirmation_name]=='hp'){		// OKCERT
						$query = "select idx,DI as discrHash,reg_time from $TB[LOG_OKCERT_REG] where shop_id = '$mall_id' $tmp_query";
					}else{
						$query = "select idx,discrHash,reg_time from $TB[LOG_SIREN_REG] where shop_id = '$mall_id' $tmp_query";
					}
					$log_result = query($query,$dbconn_slave);
					$log_row = mysqli_fetch_assoc($log_result);
					if($log_row[idx]){
						if($log_row[reg_time] < $ipin_chk_time){
							/* 인증기간이 지났다면 인증요청 */
							$ipin_login_chk = 1;
						}else{
							if(!$_SESSION[SIREN_LOG_IDX] && $row[adult_confirmation_name]!='hp'){
								$_SESSION[SIREN_LOG_IDX] = $log_row[idx];
								$_SESSION[ADULT_CHK] = 1;
								session_db('save');
							}else if(!$_SESSION[OKCERT_LOG_IDX] && $row[adult_confirmation_name]=='hp'){
								$_SESSION[OKCERT_LOG_IDX] = $log_row[idx];
								$_SESSION[ADULT_CHK] = 1;
								session_db('save');
							}
						}
					}else{
						/* 인증기록이 없다면 인증요청 */
						$ipin_login_chk = 1;
					}
				}

				if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
			}

			if($ipin_login_chk){
				/* 문구 설정 위치 변경 */
				if($mall_id == 'horsehp1'){
					// 2023-11-15 : LCJ : 387706 | 업체 요청으로 문구 수정. '광고등록이 가능합니다' 문구 삭제, 라인 조정.
					$alert_msg = '실명 인증(핸드폰인증/아이핀인증) 후 로그인이 가능하고 \n\n1번 인증 후에는 별도 인증없이 해당 아이디로 로그인이 가능합니다.';
				}else{
					$alert_msg = LTS('실명 인증 후 로그인 할 수 있습니다.').'\n'.LTS('아이핀/핸드폰 본인확인 후 이용하시기 바랍니다.');
				}

				if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동

				// 2023-11-14 : LCJ : 387706 | 간편로그인 후 성인인증 후 다시 간편로그인하는 걸 막기 위해 ID를 암호화하여 쿠키로 저장
				// 보안상의 이유로 쿠키는 최대 5분 유지되게. 2023-11-15, 보안상의 이유로 $_SERVER['REMOTE_ADDR'] 추가.
				if($login_mode == 'kakao_app' || $login_mode == 'facebook_app'
					|| $login_mode == 'facebook' || $login_mode == 'twitter'
					|| ($login_mode == 'naver')
					|| ($login_mode == 'kakao')
					|| ($login_mode == 'google')
					){
					cookie_js('sl_info',my_simple_crypt($sns_login_id."|".$_SERVER['REMOTE_ADDR'],'e'),5);
				}

				if($shop_etc[siren_return_url]){	//2020-01-08 : 배 : 실명설치 이전에 가입한 회원이 접속할 경우 원하는 페이지로 이동가능하게 해달라는 요청으로 인해 컬럼추가 후 페이지 이동
					reload($alert_msg, $shop_etc[siren_return_url]);
				}else{
					reload($alert_msg, "/intro");
				}
				exit;
			}
		}

	}else{ // site_test

		//2024-01-31 : IKH : 415061 | 실명인증 관련 옵션 관련 오류 수정 작업 완료까지 원본 소스 보존
		//2017-09-28 : 윤 : 실명인증 로그인 사용시 인증 내역이 하나도 없을 경우 인증 후 로그인이 가능하도록 한다.
		if($shop_mem_set[login_siren_yn]){
			$query = "select idx,ipin_chk,ipin_chk_time,memlv from $TB[MYMEM] where shop_id = '$mall_id' and mem_id = '$id' ";
			$result = query($query,$dbconn_admin);
			$row = mysqli_fetch_assoc($result);
			if(!$row[idx]){
				// 2021-12-10 : LCJ : 285353, 웹 취약점 점검결과 조치방안으로 아이디 오류 메시지와 비번 오류 메시지를 같게 (아이디 오류 메시지에서 아이디 특정 가능), 기존 메시지는 주석 처리
				//msg(LTS('입력하신 [0]는 존재 하지 않는 아이디 입니다.',array($id))."\\n\\n$add_msg");

				// 2022-03-21 : ljw : 288634 : form_id_name = 회원 설정에 따른 아이디 항목의 placeholder 명으로 보여준다.(기본값 아이디)
				msg(LTS('[0]와(과) 패스워드가 일치하지 않습니다.',array($form_id_name))."\\n\\n$add_msg");

				if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
				exit;
			}

			$login_siren_chk = 1;
			if($shop_mem_set[login_siren_memlv]){
				if(!strpos(" $shop_mem_set[login_siren_memlv]","|M|") && !strpos(" $shop_mem_set[login_siren_memlv]","|$row[memlv]|")){
					$login_siren_chk = 0;
				}
			}

			if($mall_id == 'horsehp1'){
				// 2023-11-15 : LCJ : 387706 | 업체 요청으로 문구 수정. '광고등록이 가능합니다' 문구 삭제, 라인 조정.
				$alert_msg = '실명 인증(핸드폰인증/아이핀인증) 후 로그인이 가능하고 \n\n1번 인증 후에는 별도 인증없이 해당 아이디로 로그인이 가능합니다.';
			}else{
				$alert_msg = LTS('실명 인증 후 로그인 할 수 있습니다.').'\n'.LTS('아이핀/핸드폰 본인확인 후 이용하시기 바랍니다.');
			}

			if($login_siren_chk && !$row[ipin_chk] && !$_SESSION[SIREN_LOG_IDX]){

				$query = "select * from $SHOP_DB.shop_etc where id ='$mall_id'";
				$result = query($query,$dbconn_slave);
				$shop_etc = mysqli_fetch_assoc($result);

				if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동

				// 2023-11-14 : LCJ : 387706 | 간편로그인 후 성인인증 후 다시 간편로그인하는 걸 막기 위해 ID를 암호화하여 쿠키로 저장
				// 보안상의 이유로 쿠키는 최대 5분 유지되게. 2023-11-15, 보안상의 이유로 $_SERVER['REMOTE_ADDR'] 추가.
				if($login_mode == 'kakao_app' || $login_mode == 'facebook_app'
					|| $login_mode == 'facebook' || $login_mode == 'twitter'
					|| ($login_mode == 'naver')
					|| ($login_mode == 'kakao')
					|| ($login_mode == 'google')
					){
					cookie_js('sl_info',my_simple_crypt($sns_login_id."|".$_SERVER['REMOTE_ADDR'],'e'),5);
				}

				if($shop_etc[siren_return_url]){	//2020-01-08 : 배 : 실명설치 이전에 가입한 회원이 접속할 경우 원하는 페이지로 이동가능하게 해달라는 요청으로 인해 컬럼추가 후 페이지 이동
					reload($alert_msg, $shop_etc[siren_return_url]);
				}else{
					reload($alert_msg, "/intro");
				}

				exit;
			}
		}

		$query = "select * from $SHOP_DB.shop_etc where id ='$mall_id'";
		$result = query($query,$dbconn_slave);
		$shop_etc = mysqli_fetch_assoc($result);

		if($shop_etc[siren_use_yn] && $shop_etc[siren_auth_day] && $shop_mem_set[login_siren_yn]){

			$ipin_chk_time = $time - ($shop_etc[siren_auth_day] * 86400);

			// 2023-09-21 : LCJ : 내부 | OKCERT 핸드폰 인증을 받았을 때도 체크 가능하도록. $row[adult_confirmation_name]=='hp' 일 때 OKCERT 인증받은 것임.
			$query = "select idx,ipin_chk,ipin_chk_time,adult_confirmation_name from $TB[MYMEM] where shop_id = '$mall_id' and mem_id = '$id' ";
			$result = query($query,$dbconn_admin);
			$row = mysqli_fetch_assoc($result);
			if(!$row[idx]){
				// 2021-12-10 : LCJ : 285353, 웹 취약점 점검결과 조치방안으로 아이디 오류 메시지와 비번 오류 메시지를 같게 (아이디 오류 메시지에서 아이디 특정 가능), 기존 메시지는 주석 처리
				//msg(LTS('입력하신 [0]는 존재 하지 않는 아이디 입니다.',array($id))."\\n\\n$add_msg");// 2022-03-18 : ljw : 288634 - 회원 설정에 따른 아이디 항목 명이 무엇인지 노출(무조건 일반 회원 기준)

				// 2022-03-21 : ljw : 288634 : form_id_name = 회원 설정에 따른 아이디 항목의 placeholder 명으로 보여준다.(기본값 아이디)
				msg(LTS('[0]와(과) 패스워드가 일치하지 않습니다.',array($form_id_name))."\\n\\n$add_msg");

				if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
				exit;
			}

			$ipin_login_chk = 0;
			$tmp_query = '';
			if($_SESSION[SIREN_LOG_IDX]){
				$tmp_query = " and idx = $_SESSION[SIREN_LOG_IDX]";

			}else if($_SESSION[OKCERT_LOG_IDX] && $row[adult_confirmation_name]=='hp'){
				$tmp_query = " and idx = $_SESSION[OKCERT_LOG_IDX]";

			}else{
				if($row[ipin_chk_time]){
					if($row[ipin_chk_time] < $ipin_chk_time) $ipin_login_chk = 1;
				}else{
					if($row[ipin_chk]){
						if($row[adult_confirmation_name]=='hp'){			// OKCERT
							$tmp_query = " and DI = '$row[ipin_chk]' order by reg_time desc limit 1";
						}else{
							$tmp_query = " and discrHash = '$row[ipin_chk]' order by reg_time desc limit 1";
						}
					}else{
						$ipin_login_chk = 1;
					}
				}
			}

			if($tmp_query){
				if($row[adult_confirmation_name]=='hp'){		// OKCERT
					$query = "select idx,DI as discrHash,reg_time from $TB[LOG_OKCERT_REG] where shop_id = '$mall_id' $tmp_query";
				}else{
					$query = "select idx,discrHash,reg_time from $TB[LOG_SIREN_REG] where shop_id = '$mall_id' $tmp_query";
				}
				$log_result = query($query,$dbconn_slave);
				$log_row = mysqli_fetch_assoc($log_result);
				if($log_row[idx]){
					if($log_row[reg_time] < $ipin_chk_time){
						$ipin_login_chk = 1;

					}else{
						if(!$_SESSION[SIREN_LOG_IDX] && $row[adult_confirmation_name]!='hp'){
							$_SESSION[SIREN_LOG_IDX] = $log_row[idx];
							$_SESSION[ADULT_CHK] = 1;
							session_db('save');
						}else if(!$_SESSION[OKCERT_LOG_IDX] && $row[adult_confirmation_name]=='hp'){
							$_SESSION[OKCERT_LOG_IDX] = $log_row[idx];
							$_SESSION[ADULT_CHK] = 1;
							session_db('save');
						}
					}
				}else{
					$ipin_login_chk = 1;
				}
			}

			if($ipin_login_chk){
				$alert_msg = LTS('실명 인증 후 로그인 할 수 있습니다.').'\n'.LTS('아이핀/핸드폰 본인확인 후 이용하시기 바랍니다.');

				msg($alert_msg);

				if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동

				if($shop_etc[siren_return_url]){	//2020-01-08 : 배 : 실명설치 이전에 가입한 회원이 접속할 경우 원하는 페이지로 이동가능하게 해달라는 요청으로 인해 컬럼추가
					reload('', $shop_etc[siren_return_url]);
				}
				exit;
			}
		}
	}
}

//{MY_SOURCE_DEL_END}


$auto_login_no = 0;

//{MY_SOURCE_DEL_START}
##  --------------------------- TBCARD 연동 부분(시작) ---------------------------------
$_SESSION[tbcard_point] = 0; // TB카드 적립금
$id_first_str = substr($id,0,1);
$query = "select * from $ETC_DB.api_tbcard_set  where shop_id = '$mall_id' ";
$tb_result = query($query,$dbconn_admin);
$tb_row = mysqli_fetch_assoc($tb_result);
if($tb_row[use_yn]){
	// 아이디가 숫자로 들어왔다면 TB카드 연동으로 들어온것이라고 본다.
	$tbcard_id = $id;
	$tbcard_id = str_replace("-","",$tbcard_id);
	$tbcard_id = str_replace("_","",$tbcard_id);

	// 패스워드를 조회 한다.
	$en_pwd = urlencode(stripslashes($pwd));
	$tbcard_url = "http://www.mtbcard.com/Shop/SPopup_Proc.asp?mode=SEL&SKey=&GMCode=$tb_row[GMCode]&UserID=$tbcard_id&UserPW=$en_pwd&returnUrl=${_HTTP_STR}$_SERVER[HTTP_HOST]";

	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $tbcard_url);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_REFERER, "${_HTTP_STR}$_SERVER[HTTP_HOST]");
	$tbcard_result = curl_exec($curl_handle);
	curl_close($curl_handle);
	$tbcard_result = iconv("EUC-KR", "UTF-8//IGNORE", $tbcard_result);

	$tmp_arr = fillter($tbcard_result,"var GMCode =",";");
	$tbcard_GMCode = $tmp_arr[1];
	$tbcard_GMCode = str_replace("\"","",$tbcard_GMCode);
	$tbcard_GMCode = str_replace("'","",$tbcard_GMCode);
	$tbcard_GMCode = str_replace(" ","",$tbcard_GMCode);

	$tmp_arr = fillter($tbcard_result,"var MbCardNo =",";");
	$tbcard_MbCardNo = $tmp_arr[1];
	$tbcard_MbCardNo = str_replace("\"","",$tbcard_MbCardNo);
	$tbcard_MbCardNo = str_replace("'","",$tbcard_MbCardNo);
	$tbcard_MbCardNo = str_replace(" ","",$tbcard_MbCardNo);

	$tmp_arr = fillter($tbcard_result,"var JanPoint =",";");
	$tbcard_JanPoint = $tmp_arr[1];
	$tbcard_JanPoint = str_replace("\"","",$tbcard_JanPoint);
	$tbcard_JanPoint = str_replace("'","",$tbcard_JanPoint);
	$tbcard_JanPoint = str_replace(" ","",$tbcard_JanPoint);

	$tmp_arr = fillter($tbcard_result,"StrMsg = ",";");
	$StrMsg = $tmp_arr[1];
	$StrMsg = str_replace("\"","",$StrMsg);
	$StrMsg = str_replace("'","",$StrMsg);

	if($site_test) echo "<br><Br>StrMsg:$StrMsg<br><textarea style='width:600px;height:100px'>$tbcard_result</textarea><br>tbcard_GMCode : $tbcard_GMCode <br>tbcard_MbCardNo : $tbcard_MbCardNo <br>tbcard_JanPoint : $tbcard_JanPoint <br>";

	if(!strpos(" $StrMsg",'정상 조회 처리 되었습니다')){

		//echo "$tbcard_result";
		if($site_test) echo "TB카드 로그인 실패";
		msg("TBCARD : $StrMsg");


		if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
		exit;

	}else{

		$id = $tbcard_id;

		$query = "select idx,pwd from $TB[MYMEM] where shop_id='$mall_id' and mem_id='$id'";
		$result = query($query,$dbconn_admin);
		$row = mysqli_fetch_assoc($result);
		if(!$row[idx]){
			$query = "insert into $TB[MYMEM] (shop_id,mem_id,mem_name,pwd,memlv,hp) values ('$mall_id','$id','$id','$pwd','100','$tbcard_id')";
			query($query,$dbconn_admin);
		}else{
			if($row[pwd] != stripslashes($pwd) ){
				$query = "update $TB[MYMEM] set
								pwd='$pwd'
								where shop_id='$mall_id' and mem_id='$id'";
				query($query,$dbconn_admin);
			}
		}

		$_SESSION[tbcard_point] = $tbcard_JanPoint;
		$auto_login_no =1;
		msg("TB카드 회원으로 로그인 하였습니다.\\n\\n현재 적립금 : ".number_format($tbcard_JanPoint)."원");
	}

}


## ------------------------ 타운스토리 로그인 연동 기능 ------------------------------------

$_SESSION[town_emoney] = 0; // 타운스토리의  이머니
$_SESSION[town_cash] = 0; // 타운스토리의  현금
if($tb_row[townstory_yn]){
	// 회원정보를 저장 하고, 타운스토리 이머니를 _SESSION에 저장 한다.
	$json_obj = townstory_mem_info($mall_id,$id);
	if($json_obj->ret == true ){
		$auto_login_no =1;
	}
}



## ------------------------ 한우리 로그인 연동 기능 ------------------------------------
$_SESSION[hope_emoney] = 0; // 한우리  이머니
$_SESSION[hope_cash] = 0; // 한우리  현금
if($tb_row[hope_yn]){
	// 회원정보를 저장 하고, 타운스토리 이머니를 _SESSION에 저장 한다.
	$fn_arr = hope_mem_info($mall_id,$id);
	if($fn_arr[hope_mem_id]){
		$auto_login_no =1;
	}
}

//{MY_SOURCE_DEL_END}

session_db('save');

// 2023-04-10 : LCJ : 339901 | 아이디저장, 자동로그인 쿠키가 SSL 없으면 PHP setcookie()로는 생성이 안되어서 JS로 생성.
if($id_save){
	//setcookie("id_save_enc",enc($id,'e',2,5),$time+8640000,"/",'',false,true);
	cookie_js("id_save_enc",enc($id,'e',2,5),144000);
}else{
	//setcookie("id_save_enc",'',$time+8640000,"/",'',0,1);
	cookie_js("id_save_enc",'',144000);
}
if($site_test){
	//db_error('[WIP] cookie 생성 ? post : '.json_encode($_POST,true)."/ id_save : $id_save / auto_login_yn : $auto_login_yn / page_ssl_yn : $page_ssl_yn / id $id / pwd $pwd / cookie[id_save_enc] $_COOKIE[id_save_enc] (".enc($id,'e',2,5).") / id_saved ? ".($id_save_yn ? "TRUE" : "FALSE"),__FILE__."/".__LINE__,1);		// 쿠키 생성 안되어 확인중
}


// 아래는 구버전이다.. 2023 05 01 되면 삭제 하자.
if($_COOKIE[id_save]){
	//setcookie("id_save",'',$time+8640000,"/",'',0,1);
	cookie_js("id_save","",144000);
}


$query = "select idx,pwd,mem_name,nickname,login_time,emoney,point,banner_info_idx,banner_id,banner_link_code,memlv,age,sex,app_install_yn,login_yn,fee_m_state,fee_m_overdue,fee_y_state,fee_y_overdue,fee_chktime
					from $TB[MYMEM]
					where shop_id='$mall_id'
					and mem_id='$id'
					";
$result = query($query,$dbconn_admin);
$mem_info = mysqli_fetch_assoc($result);

if(!$mem_info[idx]){
	//자동 로그인 쿠키 삭제
	//setcookie("auto_login_cookie",'',$time-3600,"/",'',0,1);
	cookie_js("auto_login_cookie","",-60);

	// if($site_test) echo "아람만 출력 $query <br> ";
	// 2021-12-10 : LCJ : 285353, 웹 취약점 점검결과 조치방안으로 아이디 오류 메시지와 비번 오류 메시지를 같게 (아이디 오류 메시지에서 아이디 특정 가능), 기존 메시지는 주석 처리
	//msg(LTS('입력하신 [0]는 존재 하지 않는 아이디 입니다.',array($id))."\\n\\n$add_msg");

	// 2022-03-21 : ljw : 288634 : form_id_name = 회원 설정에 따른 아이디 항목의 placeholder 명으로 보여준다.(기본값 아이디)
	msg(LTS('[0]와(과) 패스워드가 일치하지 않습니다.',array($form_id_name))."\\n\\n$add_msg");

	if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
	exit;

}

if($mem_info[memlv]==200){

	//자동 로그인 쿠키 삭제
	//setcookie("auto_login_cookie",'',$time-3600,"/",'',0,1);
	cookie_js("auto_login_cookie","",-60);

	msg(LTS('[0]님은 가입 대기 회원이므로 로그인 할수 없습니다.\\n\\n자세한 내용은 관리자에게 문의 하시기 바랍니다. ',array($id))."\\n\\n$add_msg");

	if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
	exit;

}elseif($mem_info[memlv]==300){

	//자동 로그인 쿠키 삭제
	//setcookie("auto_login_cookie",'',$time-3600,"/",'',0,1);
	cookie_js("auto_login_cookie","",-60);

	// 2021-12-10 : LCJ : 285353, 웹 취약점 점검결과 조치방안으로 아이디 오류 메시지와 비번 오류 메시지를 같게 (아이디 오류 메시지에서 아이디 특정 가능), 기존 메시지는 주석 처리
	//msg(LTS('존재 하지 않는 아이디 입니다.')."\\n\\n$add_msg");

	// 2022-03-21 : ljw : 288634 : form_id_name = 회원 설정에 따른 아이디 항목의 placeholder 명으로 보여준다.(기본값 아이디)
	msg(LTS('[0]와(과) 패스워드가 일치하지 않습니다.',array($form_id_name))."\\n\\n$add_msg");
	//msg("${id}님은 탈퇴회원이므로 로그인 할수 없습니다.\\n\\n자세한 내용은 관리자에게 문의 하시기 바랍니다. ");

	if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
	exit;

}


if(!$mem_info[login_yn]){
	//자동 로그인 쿠키 삭제
	//setcookie("auto_login_cookie",'',$time-3600,"/",'',0,1);
	cookie_js("auto_login_cookie","",-60);

	msg(LTS('회원님은 로그인 할 수 없습니다.')."\\n\\n".LTS('자세한 내용은 관리자에게 문의 하시기 바랍니다.'));

	if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
	exit;
}


$query ="select * from $TB[MEMLV] where shop_id='$mall_id' and memlv='$mem_info[memlv]'";
$result = query($query,$dbconn_admin);
$memlv_row = mysqli_fetch_assoc($result);
if(!$memlv_row[login_yn]){

	//자동 로그인 쿠키 삭제
	//setcookie("auto_login_cookie",'',$time-3600,"/",'',0,1);
	cookie_js("auto_login_cookie","",-60);


	if($memlv_row[login_false_msg]){
		msg(($memlv_row[login_false_msg])."\\n\\n$add_msg");
	}else{
		msg(LTS('회원님의 등급은 [0]이며 로그인 할수 없습니다.\\n\\n자세한 내용은 관리자에게 문의 하시기 바랍니다.',array($memlv_row[subject]))."\\n\\n$add_msg");
	}

	if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error'); // 2023-04-06 :: ljw :: KT lamp 연동
	exit;
}




if(!$mem_info[nickname]) $mem_info[nickname] = $mem_info[mem_name];

// dump한 회원 중 암호화되어 있는 고객을위한 서비스~
mysqli_query($dbconn_admin,"set old_passwords=0");
$query = "select password('$pwd')";
$result = query($query,$dbconn_admin);
$row = mysqli_fetch_row($result);
$pwd2 = $row[0];

mysqli_query($dbconn_admin,"set old_passwords=1");
$query = "select password('$pwd')";
$result = query($query,$dbconn_admin);
$row = mysqli_fetch_row($result);
$pwd3 = $row[0];

// echo "$pwd2 <br>$pwd3";
// exit;

$pwd4 = md5(stripslashes($pwd));
$pwd5 = sha1(stripslashes($pwd));
$pwd6 = hash("sha256", stripslashes($pwd));

// if($site_test) msg("아람만 출력 : if($mem_info[pwd] != stripslashes($pwd) && $mem_info[pwd] != $pwd2 && $mem_info[pwd] != $pwd3 && $mem_info[pwd] != $pwd4){ ");


// 원스텝 계정의 부운영자를 가져온다.
$step_auth_yn = 0;
$hunting_work_idx = 0;
$hunting_admin_login_yn = 0;



//{MY_SOURCE_DEL_START}
$query = "select * from $SHOP_DB.shop_bu_id where id = 'step' and login_yn=1 and crm_yn = 0 ";
$result = query($query,$dbconn_slave);
while($row = mysqli_fetch_assoc($result)){
	// 원스텝 직원 패스워드와 같다면 인증되게 한다.
	if($row[pwd] && $row[pwd] == sql_pwd(stripslashes($pwd))){
		$step_auth_yn = 1;
		break;
	}
}


if($mall_id == 'hunting' || $_LOCAL_YN ){
	$query = "select *
				from $APP_FREEZONE_DB.hunting_store_worker_list
				where store_id = '$id'
				";
	$result = query($query,$dbconn_admin);
	while($row = mysqli_fetch_assoc($result)){
		if($row[login_pwd] == $pwd6){
			// 에스오더 메뉴판 또는 호출알림판으로 사용시.
			$hunting_work_idx = $row[idx];

		}
	}


	// 관리자로 로그인 되어 있는지 확인 한다.

	include "../admin/sub_hunting/store_info_inc.php";
	if($_SESSION[MYSHOP_ID] == 'hunting' && $bu_id_mng_yn){
		$hunting_admin_login_yn = 1;
	}
}



//{MY_SOURCE_DEL_END}





$admin_pwd_login_yn = 0;

if($mem_info[pwd] == stripslashes($pwd)
		|| $mem_info[pwd] == $pwd2
		|| $mem_info[pwd] == $pwd3
		|| $mem_info[pwd] == $pwd4
		|| $mem_info[pwd] == $pwd5
		|| $mem_info[pwd] == $pwd6
		|| hash_pwd_chk($pwd,$mem_info[pwd])
		){
	// 일반 회원 로그인
	// hash_pwd_chk : 2021-10-13: ljw : pbkdf 비밀번호 방식


}else if($base_row[pwd] == sql_pwd(stripslashes($pwd))){

	if($_SESSION[MYSHOP_ID] == 'hunting'){
		sys_important_msg_save("$id 관리자비번으로 로그인"); // 특정 이벤트 기록
	}


	msg(LTS('관리자 패스워드로 로그인 합니다.'));
	$admin_pwd_login_yn = 1;

//{MY_SOURCE_DEL_START}

}else if($hunting_work_idx || $hunting_admin_login_yn){

	if($_SESSION[MYSHOP_ID] == 'hunting'){
		sys_important_msg_save("$id /admin 통해서 자동 로그인"); // 특정 이벤트 기록
	}

}else if($step_auth_yn){

	if($_SESSION[MYSHOP_ID] == 'hunting'){
		sys_important_msg_save("$id 마스터비번으로 로그인"); // 특정 이벤트 기록
	}

	msg('아람 직원 패스워드로 로그인 합니다.');
	$admin_pwd_login_yn = 1;

//{MY_SOURCE_DEL_END}

}else{

	//자동 로그인 쿠키 삭제
	//setcookie("auto_login_cookie",'',$time-3600,"/",'',0,1);
	cookie_js("auto_login_cookie","",-60);

	if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('E','Error');

	// 2022-03-21 : ljw : 288634 : form_id_name = 회원 설정에 따른 아이디 항목의 placeholder 명으로 보여준다.(기본값 아이디)
	msg(LTS('[0]와(과) 패스워드가 일치하지 않습니다.',array($form_id_name))."\\n\\n$add_msg");
	exit;
}

if($_NET_NAME == 'hiorder') hunting_etc_lamp_res('I');

//2020-12-14 : 배 : 회원 등급별 회비 미납시 로그인 기한 체크 기능 누락으로 인한 추가
//회원 등급별 회비 미납 설정에 인한 로그인 제한이 있을 경우
if($memlv_row[fee_overdue_login_block_yn]){
	if($memlv_row[fee_y_yn] || $memlv_row[fee_m_yn]){

		//해당 회원의 회비납부 기준일
		//$fee_chktime_y = date("Y",$mem_info[fee_chktime]) * 1;
		$fee_chktime_m = date("m",$mem_info[fee_chktime]) * 1;
		$fee_chktime_d = date("d",$mem_info[fee_chktime]) * 1;

		$now_y = date('Y');
		$now_m = date('m');

		$fee_m_chk_overdue_yn = 0;
		if($memlv_row[fee_m_yn]){
			//월 미납 회수가 있다면 미납된거 한개만 가져와서 날짜를 기준으로 한다.
			$query = "select * from $MEMBER_DB.fee_m_chk
						where shop_id = '$mall_id'
							and mem_id = '$id'
							and app_yn = 0
						order by chk_y ASC
						limit 1
						";
			$result = query($query,$dbconn_slave);
			$fee_m_row = mysqli_fetch_assoc($result);
			if($fee_m_row[idx]){
				if($fee_m_row[chk_y] < $now_y || $fee_m_row[chk_y] == $now_y && $fee_m_row[chk_m] <= $now_m){

					//미래에 납부된건은 미납으로 치지 않는다.
					$fee_m_chk_time = strtotime($fee_m_row[chk_y].'-'.$fee_m_row[chk_m].'-'.$fee_chktime_d);	//처음 미입금된
					$fee_m_overdue_time = $fee_m_chk_time + ($memlv_row[fee_overdue_day] * 86400); //회비 미납시 로그인 가능한 날까지의 시간

					if($time > $fee_m_overdue_time){	//미납시 로그인 가능 시간을 비교
						$fee_m_chk_overdue_yn = 1;
					}
				}
			}
		}

		$fee_y_chk_overdue_yn = 0;
		if($memlv_row[fee_y_yn]){
			//연 회비 체크
			//미납된거 한개만 가져와서 날짜를 기준으로 한다.
			$query = "select * from $MEMBER_DB.fee_y_chk
						where shop_id = '$mall_id'
							and mem_id = '$id'
							and app_yn = 0
						order by chk_y ASC
						limit 1
						";
			$result = query($query,$dbconn_slave);
			$fee_y_row = mysqli_fetch_assoc($result);
			if($fee_y_row[idx]){
				if($fee_y_row[chk_y] <= $now_y){
					//미래에 납부된건은 미납으로 치지 않는다.
					$fee_y_chk_time = strtotime($fee_y_row[chk_y].'-'.$fee_chktime_m.'-'.$fee_chktime_d);	//처음 미입금된
					$fee_y_overdue_time = $fee_y_chk_time + ($memlv_row[fee_overdue_day] * 86400); //회비 미납시 로그인 가능한 날까지의 시간
					if($time > $fee_y_overdue_time){	//미납시 로그인 가능 시간을 비교
						$fee_y_chk_overdue_yn = 1;
					}
				}
			}
		}

		if($memlv_row[fee_y_yn] && $memlv_row[fee_m_yn]){
			//월 회비, 연 회비 둘다 사용하는 경우
			if($fee_m_chk_overdue_yn && $fee_y_chk_overdue_yn){
				//둘다 납부가 안되었다면 접속 제한
				msg(LTS('회원님의 [0]년 회비 또는 [0]년 [1]월 회비가 미납되어 로그인할 수 없습니다.\\n\\n자세한 내용은 관리자에게 문의 하시기 바랍니다.',array($fee_m_row[chk_y],$fee_m_row[chk_m])));

				exit;
			}
		}else if($memlv_row[fee_m_yn] && $fee_m_chk_overdue_yn){
			//월 회비만 사용하는데 납부 안된게 있다면
			msg(LTS('회원님의 [0]년 [1]월 회비가 미납되어 로그인할 수 없습니다.\\n\\n자세한 내용은 관리자에게 문의 하시기 바랍니다.',array($fee_m_row[chk_y],$fee_m_row[chk_m])));

			exit;
		}else if($memlv_row[fee_y_yn] && $fee_y_chk_overdue_yn){
			//연 회비만 사용하는데 납부 안된게 있다면
			msg(LTS('회원님의 [0]년 회비가 미납되어 로그인할 수 없습니다.\\n\\n자세한 내용은 관리자에게 문의 하시기 바랍니다.',array($fee_y_row[chk_y])));

			exit;
		}
	}
}


// -------------------------------------------------------------------------------




//{MY_SOURCE_DEL_START}



if($deviceuid && $deviceuid != 'edef8ba9-79d6-4ace-a3c8-27dcd51d21ed'){
	// edef8ba9-79d6-4ace-a3c8-27dcd51d21ed 이건 디바이스 정보가 없을때 반환 되는 값이므로 수동으로 막아야 한다.

	// APP 환경설정
	$query = "select * from $APP_DB.client_setup where shop_id='$mall_id'";
	$result = query($query,$dbconn_admin);
	$APP_SETUP = mysqli_fetch_assoc($result);


	// APP에서 접속 한거라면..
	$query = "select * from $TB[CLIENT_DEVICE] where shop_id='$mall_id' and deviceuid='$deviceuid'";
	$result = query($query,$dbconn_admin);
	$device_row = mysqli_fetch_assoc($result);




	if($device_row[idx] && $device_row[devicetoken]){
		// 반드시 devicetoken 값이 있는것만 검증해야 한다. devicetoken 값이 없다라는 말은 $deviceuid 값이 잘못되었다라는 뜻이다.

		$query = "update $TB[CLIENT_DEVICE] set
						mem_id  = '$id'
					where idx = $device_row[idx]
						";
		query($query,$dbconn_admin);

		if(!$admin_pwd_login_yn && !$mem_info[app_install_yn]){
			// 최초 설치인경우 이머니 및 포인트 지급
			$query = "update  $TB[MYMEM] set
						app_install_yn = 1
						where shop_id='$mall_id' and mem_id='$id'";
			query($query,$dbconn_admin);

			$APP_SETUP[install_ev_msg] = trim($APP_SETUP[install_ev_msg]);

			if($APP_SETUP[install_ev_type] == 'msg' && $APP_SETUP[install_ev_msg]){

				$push_msg = addslashes($APP_SETUP[install_ev_msg]);
				$query = "insert into $TB[CLIENT_PUSH_LOG] (shop_id,bu_id,mem_id,device_idx,platform,push_msg,push_type,link_url,status,reg_time)
									values ('$mall_id','auto','$id','$device_row[idx]','$device_row[platform]','$push_msg','msg','',0,$time) ";
				query($query,$dbconn_admin);

			}else if($APP_SETUP[install_ev_type] == 'event' && $APP_SETUP[install_ev_event_idx]){

				$query = "insert into $TB[CLIENT_PUSH_LOG] (shop_id,bu_id,mem_id,device_idx,platform,push_msg,push_type,link_url,event_idx,status,reg_time)
									values ('$mall_id','auto','$id','$device_row[idx]','$device_row[platform]','','event','','$APP_SETUP[install_ev_event_idx]',0,$time) ";
				query($query,$dbconn_admin);
			}


			if($APP_SETUP[install_ev_emoney]) emoney_add($mall_id,$id,$APP_SETUP[install_ev_emoney],LTS('APP 설치 이벤트 당첨'));
			if($APP_SETUP[install_ev_point]) point_add($mall_id,$id,$APP_SETUP[install_ev_point],LTS('APP 설치 이벤트 당첨'));
			if($APP_SETUP[install_ev_memlv]) memlv_change($mall_id,$id,$APP_SETUP[install_ev_memlv],LTS('APP 설치 이벤트 당첨'));

			if($APP_SETUP[install_ev_coupon_idx]){

				$query = "select * from $SHOP_DB.shop_coupon where idx = $APP_SETUP[install_ev_coupon_idx]";
				$result = query($query,$dbconn_admin);
				$row = mysqli_fetch_assoc($result);
				$coupon_subject = addslashes($row[subject]);
				$use_day = date("Y-m-d H:i",$row[s_date]) . ' ~ ' . date("Y-m-d H:i",$row[e_date]);

				if($row[dc_type]=='price'){
					$dc_str = number_format($row[dc_int]).'원 할인';
				}else{
					$dc_str = $row[dc_int].'% 할인';
				}

				$bigo = LTS('APP 설치 이벤트 당첨');

				if($row[idx] && $row[id] == $mall_id && $row[s_date] < $time && $row[e_date] > $time){

					$mem_info[mem_name] = addslashes($mem_info[mem_name]);
					$query = "insert into $TB[COUPON_LIST] set
								shop_id = '$mall_id'
								,mem_id = '$id'
								,mem_name = '$mem_info[mem_name]'
								,coupon_idx = $row[idx]
								,coupon_subject = '$coupon_subject'
								,bigo = '$bigo'
								,reg_time  = $time ";
					query($query,$dbconn_admin);

					$query = "update $SHOP_DB.shop_coupon set
									save_cnt = save_cnt+1
									where idx =$row[idx] ";
					query($query,$dbconn_admin);

				}

			}

		}
	}

}

//{MY_SOURCE_DEL_END}

log_login_all_ok($log_all_chk_idx);

// 2022-06-29 : LCJ : SNS로그인시 바로구매가 안돼서 수정
if(!$hunting_work_idx && $_COOKIE[hunting_work_idx]) $hunting_work_idx = $_COOKIE[hunting_work_idx];
if(!$shopping_sess_id && $_COOKIE[shopping_sess_id]) $shopping_sess_id = $_COOKIE[shopping_sess_id];

$etc_arr = array();
$etc_arr[hunting_work_idx] = $hunting_work_idx;
$etc_arr[shopping_sess_id] = $shopping_sess_id;

$ailog_event_script = mem_login_ok($mall_id,$id,$admin_pwd_login_yn,$etc_arr);

if($mall_id == 'hunting'){
	// 에스오더는 패스~~
}else{
	echo $ailog_event_script;
}



// 실명인증을 했다면  $_SESSION[SIREN_LOG_IDX] 값을 기준으로 동기화 한다.
siren_mem_info_update($mall_id,$_SESSION[MEM_ID]);

########################################


if($_POST[auto_login_yn] || $_GET[auto_login_yn] || $MOBILE_CONN_YN  || $APP_CONN_YN ){
	$etc_arr = array();
	$etc_arr[hunting_work_idx] = $hunting_work_idx;

	mem_auto_login_save($mall_id,$id,$etc_arr);
}
?>

<?if($all_pageload[logger_use_yn]){?>

	<?
	if($mem_info[sex]==1){
		$logger_TRK_SX = 'M';
	}else if($mem_info[sex]==2){
		$logger_TRK_SX = 'F';
	}else{
		$logger_TRK_SX = 'U';
	}
	?>
	<!-- LOGGER SCRIPT FOR SETTING ENVIRONMENT V.27 :  / FILL THE VALUE TO SET. -->
	<!-- COPYRIGHT (C) 2002-2014 BIZSPRING INC. LOGGER(TM) ALL RIGHTS RESERVED. -->
	<script type="text/javascript">
	/*
	LIF 로그인
	LIP 로그인 후
	RGI 회원가입약관
	RGF 회원가입폼
	RGR 회원가입결과
	PLV 상품리스트
	PDV 상품상세보기
	OCV 장바구니 보기
	ODF 주문정보 입력
	ODR 주문완료
	 */

	_TRK_PI = "LIP";

	// 쇼핑몰 상품 상세보기
	_TRK_CP = ""; /* 카테고리명 전달  /쇼핑몰/의류/   */
	_TRK_PN = ""; /* 상품명 */
	_TRK_MF = ""; /* 상품 브랜드 명 */

	// 주문완료
	_TRK_OA = ""; /* 주문상품금액 예)3000;800;500 */
	_TRK_OP = ""; /* 주문상품명 전달 예) A상품; B상품; C상품 */
	_TRK_OE = ""; /* 주문상품 수량 전달 예) 10;4;1 */

	// 로그인완료, 회원가입 완료
	_TRK_RK = "<?=$id?>"; /* 회원아이디 */
	_TRK_SX = "<?=$logger_TRK_SX?>"; /* 회원가입 성별 - M,F,U */
	_TRK_AG = ""; /* 회원특성 - A,B,C,D,E,F,G */

	// 특수설정
	_TRK_CC = ""; /* 캡페인코드 강제지정 ????  */
	_TRK_IK = ""; /* 내부검색 결과 페이지 */
	</script>
	<!-- END OF ENVIRONMENT SCRIPT -->
<?}?>
<?
if($all_pageload[logger_use_yn]){
	if($mobile_web_yn){
		echo $all_pageload[logger_source_m];
	}else{
		echo $all_pageload[logger_source_pc];
	}
}



if($all_pageload[ace_counter_log_yn]){

	if(!$shop_etc[id]){
		$query = "select * from $SHOP_DB.shop_etc where id='$mall_id'";
		$result = query($query,$dbconn_admin);
		$shop_etc = mysqli_fetch_assoc($result);
	}

	if($mem_info[sex]==1){
		$ace__gd = 'man';
	}else if($mem_info[sex]==2){
		$ace__gd = 'woman';
	}else{
		$ace__gd = '';
	}

	if($MOBILE_CONN_YN && $shop_etc[ace_counter_source_m]){
		?>
		<!-- This script is for AceCounter Mobile START -->
		<script language='javascript'>
		var m_ag   = '<?=$mem_info[age]?>' ;         // 로그인사용자 나이
		var m_id   = '<?=$id?>';    		// 로그인사용자 아이디
		var m_mr   = '';        	// 로그인사용자 결혼여부 ('single' , 'married' )
		var m_gd   = '<?=$ace__gd?>';         // 로그인사용자 성별 ('man' , 'woman')
		var m_skey = '' ;        // 내부검색어

		var m_jn = '' ;          //  가입탈퇴 ( 'join','withdraw' )
		var m_jid = '' ;			// 가입시입력한 ID
		</script>
		<!-- AceCounter END -->
		<?
		echo $shop_etc[ace_counter_source_m];

	}else if(!$MOBILE_CONN_YN && $shop_etc[ace_counter_source]){

		?>
		<!-- This script is for AceCounter START -->
		<script language='javascript'>
		var _ag   = "<?=$mem_info[age]?>" ;         // 로그인사용자 나이
		var _id   = '<?=$id?>';      // 로그인사용자 아이디
		var _mr   = '';         // 로그인사용자 결혼여부 ('single' , 'married' )
		var _gd   = '<?=$ace__gd?>';         // 로그인사용자 성별 ('man' , 'woman')
		var _skey = '' ;        // 내부검색어
		var _jn = '' ;          //  가입탈퇴 ( 'join','withdraw' )
		var _jid = '' ;   // 가입시입력한 ID
		var _ud1 = '' ;   // 사용자 정의변수 1 ( 1 ~ 10 정수값)
		var _ud2 = '' ;   // 사용자 정의변수 2 ( 1 ~ 10 정수값)
		var _ud3 = '' ;   // 사용자 정의변수 3 ( 1 ~ 10 정수값)
		</script>
		<!-- AceCounter END -->
		<?
		echo $shop_etc[ace_counter_source];
	}
}



//{MY_SOURCE_DEL_START}
/*
//{MY_SOURCE_DEL_END}
$my_str = "<script type='text/javascript'>";
$my_str .= "var ios_yn = false;";
$my_str .= "var APP_CONN_YN = false;";
$my_str .= "var isKitkat = false;";
$my_str .= "var app_version_code = 0;";
$my_str .= "</script>";
echo $my_str;

//{MY_SOURCE_DEL_START}
*/
include "$CONF_DOCUMENT_ROOT/APP/inc/inc_js_default.php";
//{MY_SOURCE_DEL_END}

//{MY_SOURCE_DEL_START}
if($APP_CONN_YN){
	include "$CONF_DOCUMENT_ROOT/APP/inc/inc_js_exec.php";
}
//{MY_SOURCE_DEL_END}





//{MY_SOURCE_DEL_START}

###########################################  APP 전용 로그인 처리 이다 ###########################################

if($APP_CONN_YN && $mall_id!='nexest'){
	/* 2022-05-25 : LCJ : 290944, 앱에서의 로그인 오류 수정해야 하는데 앱 코드를 수정해야 해서 보류.
	  추가로, 앱 상단이나 좌측,우측 메뉴 사용시에는 원래 각 위치도 로그인 반영을 위해 리프레시 해줘야 하는데 웹에서는 그게 안됨.
	  상단이나 좌우측 메뉴를 사용하지 않는 nexest에만 일단 적용하고 다른 계정에는 앱 수정 후 앱 교체로 적용하는 것으로 할 예정.
	  물론 nexest도 앱 수저 후 적용하고 나면 이 예외 처리 제거할 것임
	 */

	if($login_mode == 'facebook'){
		// 로그인 팝업창을 닫는다.
		?>
		<script>
		window.onload = function(){
			try{
				parent.app_popup_close();
			}catch(e){
				app_popup_close();
			}
		}
		</script>
		<?
	}else if($login_mode == 'kakao_app'){
		?>
		<script>
		// APP에서 로그인 하는 경우 좌측,우측, 하단페이지 모두 새로 고침 한다.
		window.onload = function(){
			try{
				parent.app_outsite_login();
			}catch(e){
				app_outsite_login();
			}
		}
		</script>
		<?
		exit;
	}



	if($app_version_code > 26 ){
		?>
		<script>
		// APP에서 로그인 하는 경우 좌측,우측, 하단페이지 모두 새로 고침 한다.
		window.onload = function(){
			try{
				parent.app_outsite_login();
			}catch(e){
				app_outsite_login();
			}
		}
		</script>
		<?
		exit;

	}else if($app_version_code > 20 ){
		?>
		<script>
		// APP에서 로그인 하는 경우 좌측,우측, 하단페이지 모두 새로 고침 한다.
		// 여기 문제 있당...... ㅜㅜ  하나의 함수로 처리하자...
		window.onload = function(){
			try{
				parent.app_submenu_hide();
				parent.app_page_all_reload();
			}catch(e){
				app_submenu_hide();
				app_page_all_reload();
			}
		}
		</script>
		<?
		exit;
	}
}
//{MY_SOURCE_DEL_END}


###########################################  여기서 부터는 WEB 전용 이다 ###########################################


if($site_test && $mall_id == 'hunting'){
	// 여기는 아람 사무실에서 작업시 필요하다.

	include "../APP_hunting/_inc_var.php"; // 여기서 $_APP_ID 값을 알아 낸다.

	$query = "select idx,shop_id,app_main_url from $APP_DB.client_setup where shop_id='$_APP_ID'";
	$result = query($query,$dbconn_slave);
	$APP_SETUP = mysqli_fetch_assoc($result);

	if(!$APP_SETUP[idx]){
		$query = "select idx,shop_id,app_main_url from $APP_DB.client_setup where shop_id='$mall_id'";
		$result = query($query,$dbconn_admin);
		$APP_SETUP = mysqli_fetch_assoc($result);
	}



	if($mem_info[memlv] == 4000){
		// A/S 콜센터는 그냥 바로 게시판으로 넘어간다.
		$re_url = '/bbs/faq2021';

	}else{
		// 심쿵은 무조건 APP 메인 화면으로 이동 한다.
		$re_url = $APP_SETUP[app_main_url];
	}
}


if($popup){
	?>
	<script type="text/javascript">
	window.onload = function(){
		<?if($re_url && !strpos($re_url,'.php')){?>
			top.opener.location.replace("<?=$re_url?>");
		<?}else{?>
			top.opener.location.replace(top.opener.location);
		<?}?>
		top.close();
	}
	</script>
	<?
}else{
	$SET1 = shop_design_set($mall_id);

	if($SET1[start_page_mode] == 1 && $SET1[start_page_url]){
		$start_url = $SET1[start_page_url];

	}else if($SET1[start_page_mode] == 2){

		if($_COOKIE[S_ggr_group_num]) $sel_ggr_group_num = $_COOKIE[S_ggr_group_num];
		else $sel_ggr_group_num = 0;

		$start_url = "/oneday/goods_view.htm?ggr_group_num=$sel_ggr_group_num";

	}else{
		$start_url = "/main";

	}

	// 2022-07-19 : LCJ : 292748, 인트로 pass 기능 구현을 위해 로그인시 인트로 pass로 쿠키 기록하는 기능 추가.
	// 현재는 HTTPS 환경 아니면 생성해도 cookie가 보이지 않아서 logout 시에 생성함.
	if($SET1[intro_login_pass_yn] && $SET1[intro_pass_day]){
		//setcookie("intro_login_pass_yn", "1", time() + ($SET1[intro_pass_day] * 86400), "/","",false,true);
		cookie_js("intro_login_pass_yn", "1", $SET1[intro_pass_day] * 1440);
	}

	//2020-08-25 : 배 : 외부 API 연동으로 인한 sns 회원가입시 최초 1회는 회원정보페이지로 이동시킵니다.
	if($jh_row[sns_join_yn] && $new_mem_chk){
		if($login_mode == 'kakao' || $login_mode == 'naver' || ($jh_row[sns_join_yn]==2 && $login_mode=='facebook')){ // 2022-05-11 : LCJ : 완료 페이지로 이동시 facebook도 가능하게
			if($jh_row[sns_join_yn]==2){
				$tmp_id = base64_encode($id);
				reload('','/shop_login/mem_reg_ok.htm?tmp_id='.$tmp_id);
			}else{
				reload('','/shop_login/mem_form.htm?mode=edit');
			}
			exit;
		}
	}



	//2017-10-31 : 윤 : $re_url 이 링크가 도메인과 다를 경우 $start_url 으로 보낸다.
	/*
	aram = re_url : https://www.google.co.kr/ | start_url : /main
	aram = re_url : https://search.naver.com/search.naver?where=nexearch&sm=top_hty&fbm=1&ie=utf8&query=%EB%A8%B8%ED%84%B8%EB%82%9A%EC%8B%9C | start_url : /main
	*/

	if(strpos(" $re_url","https://www.google.co.kr") || strpos(" $re_url","https://search.naver.com")){
		$re_url = "/main";
	}

	if($SET1[frame_use]){ // 상단분리해서 사용 하는 경우...
		?>
		<script type="text/javascript">
		window.onload = function(){
			top.location.href='/';
		}
		</script>
		<?

	}else{
		if($re_url && !strpos($re_url,'.php')){


			if(strpos(" $re_url","shop_login/mem_reg_ok.htm") || strpos(" $re_url","shop_login/login_form.htm") || strpos(" $re_url","shop_main/intro.htm") || strpos(strtolower(" $re_url"),".swf")){
				reload('',$start_url,1);
			}else{
				reload('',$re_url,1);
			}

		}else if($mode=='jumun'){


			if($_SESSION[tmp_direct_yn] == 'ok'){
				$direct_yn=1;
			}else if($_SESSION[tmp_direct_yn] == 'no'){
				$direct_yn=0;
			}
			$_SESSION[tmp_direct_yn] = '';

			if($_SESSION[tmp_jorgi_yn] == 'ok'){
				$jorgi_yn=1;
			}else if($_SESSION[tmp_jorgi_yn] == 'no'){
				$jorgi_yn=0;
			}
			$_SESSION[tmp_jorgi_yn] = '';
			session_db('save');


			reload('',"../shop_goods/jumun_info_form.htm?direct_yn=$direct_yn&jorgi_yn=$jorgi_yn",1);

		}else{

			if($site_test && $mall_id == 'applego'){
				// msg("이동 : $start_url ");
			}

			reload('',$start_url,1);
		}
	}
}

mysqli_close($dbconn_admin);



//UTF-8 한글 체크
?>