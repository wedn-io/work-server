<?
	# /member/login.php

    if($is_member){
		alert("이미 로그인중 입니다.", $INDEX_PATH);
	}
?>
<h2>로그인</h2>
<input type="text" name="member_id" id="member_id" value="">
<input type="password" name="member_password" id="member_password" value="">
<button type="button" onClick="login()">Add</button>
<a href="<?=LOCATION_PATH($INDEX_PATH, $DEFAULT_DIR, $JOIN_DIR)?>">Join</a>

<script>
	function login(){
		let member_id = document.getElementsByName("member_id")[0].value;
		let member_password = document.getElementsByName("member_password")[0].value;

		<? # AJAX 전송 시 POST 고정 ?>
		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: {
				"mode":"login",
				"member_id":member_id,
				"member_password":member_password
			},
			url: '/proc/member_proc.php',
			success:function(res){
                alert(res.msg);

                if(res.status == "OK") location.href="/index.php";
			},
			error:function(){
				<? # TODO 에러 로그 저장 및 메시지 출력 ?>
			}
		});
	}
</script>