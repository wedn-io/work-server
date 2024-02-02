<?
	include "./common.php";

	//실명인증 유효기간 사용시 -  관리자는 패스
	if($id == "@@"){
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
?>



<?

	$INCLUDE_HEAD = LAYOUT_PATH($LAYOUT_INCLUDE_DIR, $HEAD_PATH);
	$INCLUDE_TAIL = LAYOUT_PATH($LAYOUT_INCLUDE_DIR, $TAIL_PATH);

	switch ($DIR) {
		case $WORK_DIR:
			$INCLUDE_CONTENTS = INCLUDE_PATH($WORK_DIR, $INDEX_PATH);
			break;
		case $WORKLOG_DIR:
			$INCLUDE_CONTENTS = INCLUDE_PATH($WORK_DIR, $WORKLOG_PATH);
			break;
		case $BOARD_DIR:
			$INCLUDE_CONTENTS = INCLUDE_PATH($WORK_DIR, $WORKLOG_PATH);
			break;
		default:
			$INCLUDE_CONTENTS = INCLUDE_PATH($MAIN_DIR, $INDEX_PATH);
			break;
	}

	include $INCLUDE_HEAD;
	include $INCLUDE_CONTENTS;
	include $INCLUDE_TAIL;
?>