{@
if($_GET[idx]){
	$item = $myreg_form_row[0][fields];

	$itemArray = array(
		"goodsName" => $item[0][read_value],
		"discription" => $item[1][read_value],
		"imgUrl" => $item[2][read_value],
		"quantity" => $item[3][read_value],
		"price" => $item[4][read_value]
	);
} else {
	$itemArray = array(
		"goodsName" => $_POST[user_app_goods_name],
		"discription" => $_POST[discription],
		"imgUrl" => $_POST[imgUrl],
		"quantity" => $_POST[quantity],
		"price" => $_POST[user_app_price]
	);
}
@}

<style>
	.glores-A-title {margin:0 0 10px; padding:0; font-size:16px; font-weight:600; color:#000;}
	.glores-A-title:before {content:''; position:relative; top:0; margin-right:8px; border-left:3px solid #777;}
	.glores-A-title + .glores-A-form {margin-top:15px;}
	.glores-A-form + .glores-A-title {margin-top:30px;}

	.glores-A-mgt {margin-top:40px !important;}

	.glores-A-form {width:100%; border-top:1px solid #ececec; border-bottom:1px solid #ececec; border-collapse:separate; border-spacing:0;}
	.glores-A-form caption {width:0; height:0; overflow:hidden; position:absolute; top:-9999px; left:-9999px;}
	.glores-A-form > colgroup {display:none;}
	.glores-A-form > thead > tr > th {height:45px; border-bottom:2px solid #ececec; background:#f8f8f8; font-size:12px; color:#6f6f6f; vertical-align:middle; word-break:break-all;}
	.glores-A-form > tbody > tr > th {display:block; padding:10px 15px; border-top:1px solid #ececec; background:#f8f8f8; font-size:12px; color:#6f6f6f; text-align:left; line-height:20px; word-break:break-all;}
	.glores-A-form > tbody > tr:first-child > th, .glores-A-form > tbody > tr:first-child > td {border-top:none;}
	.glores-A-form > tbody > tr > th i {font-style:normal; color:#ff003e;}
	.glores-A-form > tbody > tr > td {display:block; padding:10px 15px; border-top:1px solid #ececec; font-size:12px; color:#6f6f6f; line-height:22px; line-height:20px; word-break:break-all;}
	.glores-A-form > tbody > tr > td p {margin:0; padding:0;}
	.glores-A-form > tbody > tr > td ul {margin:0; padding:0; list-style-type:none;}
	.glores-A-form > tbody > tr > td select {height:25px;}
	.glores-A-form > tbody > tr > td textarea {padding:5px; border:1px solid #ddd; -webkit-box-sizing:border-box; -moz-box-sizing:border-box; box-sizing:border-box;}
	.glores-A-form > tbody > tr > td > a {
		-webkit-box-sizing:border-box; -moz-box-sizing:border-box; box-sizing:border-box;
		display:inline-block; height:25px; margin:0 0 0 4px; padding:0 10px; border:1px solid #d1d1d1; background:#f6f6f6;
		font-family:Nanum Gothic, 'NanumGothic', 'Malgun Gothic'; font-size:12px; color:#000; text-align:center; text-decoration:none; line-height:25px;
	}
	.glores-A-form > tbody > tr > td * {vertical-align:middle;}
	.glores-A-form input[type=text],
	.glores-A-form input[type=password],
	.glores-A-form input[type=number] {
		-webkit-box-sizing:border-box; -moz-box-sizing:border-box; box-sizing:border-box;
		height:25px; margin:0; padding:0 5px; font-size:13px; border:1px solid #ddd; line-height:normal;
	}
	.glores-A-form > tbody > tr > td input[id*=zipcode],
	.glores-A-form > tbody > tr > td a[id*=zipcode],
	.glores-A-form > tbody > tr > td button[id*=zipcode] {margin-bottom:5px;}
	.glores-A-form textarea {width:100%; height:200px; vertical-align:top;}
	.glores-A-form i.glores-A-info {display:inline-block; margin:0; padding:0; font-size:12px; font-style:normal;}
	.glores-A-form #total_price_str {vertical-align:top;}

	.glores-A-agree-wrap > div {margin:30px 0;}
	.glores-A-agree-wrap > div.glores-A-agree {margin-top:0;}
	.glores-A-agree-wrap .glores-A-agree-box {
		display:block; overflow:auto; height:100px; margin-top:15px; padding:10px; border:1px solid #ccc; border-radius:3px;
		-webkit-overflow-scrolling:touch;
	}
	.glores-A-agree-wrap .glores-A-agree-box h5 {margin:40px 0 0; padding:0; font-size:15px; font-weight:600; color:#000;}
	.glores-A-agree-wrap .glores-A-agree-box h5:first-child {margin-top:0;}
	.glores-A-agree-wrap .glores-A-agree-box p,
	.glores-A-agree-wrap .glores-A-agree-box ul,
	.glores-A-agree-wrap .glores-A-agree-box ol {margin:15px 0 0; padding:0; font-size:13px; list-style-type:none;}
	.glores-A-agree-wrap .glores-A-agree-box li {margin-top:10px; padding-left:10px;}
	.glores-A-agree-wrap .glores-A-agree-box p {padding-left:10px;}

	.glores-A-agree-wrap .agree-check-box {margin:15px 0;}
	.glores-A-agree-wrap .agree-check-box:after {content:''; display:block; clear:both;}
	.glores-A-agree-wrap .agree-check-box input {float:left;}
	.glores-A-agree-wrap .agree-check-box label {float:left; margin-right:10px; font-size:13px; color:#333;}

	.glores-A-payment-type {margin:20px 0;}
	.glores-A-payment-type input {margin-left:15px;}
	.glores-A-payment-type:first-child {margin-left:0;}
	.glores-A-payment-type label {font-size:13px;}
	.glores-A-payment-type font {position:relative; top:-2px; margin-left:6px;}
	.glores-A-payment-type * {vertical-align:middle;}
	.glores-A-payment-type br {display:none;}

	.glores-A-btn-wrap {margin:30px 0 0; padding:0; text-align:center;}
	.glores-A-btn-wrap a,
	.glores-A-btn-wrap button {margin:2px 0;}
	.glores-A-btn-wrap * {vertical-align:top;}

	.glores-A-btn-type1 {
		-webkit-box-sizing:border-box; -moz-box-sizing:border-box; box-sizing:border-box;
		display:inline-block; margin:0; padding:0; border:1px solid #bbb; border-radius:3px; background:#ccc; box-shadow:inset 1px 1px 0 rgba(255,255,255,0.3);
		font-family:Nanum Gothic, 'NanumGothic', 'Malgun Gothic'; font-size:12px; font-weight:bold; color:#333;
		text-align:center; text-decoration:none; line-height:35px; cursor:pointer;
	}
	.glores-A-btn-type1:hover {border-color:#bbb; background:#bbb; color:#333;}
	.glores-A-btn-type1.glores-A-small {width:auto; height:25px; padding:0 10px; line-height:23px;}
	.glores-A-btn-type1.glores-A-medium {height:35px; padding:0 20px; line-height:33px;}
	.glores-A-btn-type1.glores-A-big {width:auto; height:70px; padding:0 30px; line-height:68px; font-size:14px;}
	.glores-A-btn-type1.glores-A-highlight {
		background: #0dbccf;
		background: -webkit-linear-gradient(#0dbccf, #018c9b); /* For Safari 5.1 to 6.0 */
		background: -o-linear-gradient(#0dbccf, #018c9b); /* For Opera 11.1 to 12.0 */
		background: -moz-linear-gradient(#0dbccf, #018c9b); /* For Firefox 3.6 to 15 */
		background: linear-gradient(#0dbccf, #018c9b); /* Standard syntax */
		border:1px solid #018998; box-shadow:inset 0 1px 0 rgba(255,255,255,.3); text-shadow:0 -1px 0 #1c2128; color:#fff;
	}

	@media (min-width:768px){
		.glores-A-form > colgroup {display:table-column-group;}
		.glores-A-form > thead > tr > th {height:45px; font-size:13px; border-bottom:2px solid #ececec; background:#f8f8f8;}
		.glores-A-form > tbody > tr > th {display:table-cell; height:25px; padding:10px 0; font-size:13px; text-align:center; border-top:1px solid #ececec; background:#f8f8f8;}
		.glores-A-form > tbody > tr > td {display:table-cell; height:25px; padding:10px; font-size:13px; border-top:1px solid #ececec;}
		.glores-A-form > tbody > tr:first-child > th, .glores-A-form > tbody > tr:first-child > td {border-top:none;}
		.glores-A-form input[type=text],
		.glores-A-form input[type=password],
		.glores-A-form input[type=number] {width:auto;}

		.glores-A-form i.glores-A-info {margin:0 0 0 10px;}

		.glores-A-agree-wrap > div {margin:50px 0;}
		.glores-A-agree-wrap .glores-A-agree-box {height:250px; padding:20px;}

		.glores-A-btn-wrap.glores-A-left {text-align:left;}
		.glores-A-btn-wrap a,
		.glores-A-btn-wrap button {margin-left:6px;}
		.glores-A-btn-wrap a:first-child,
		.glores-A-btn-wrap button:first-child {margin-left:0;}
	}

	@media \0screen{
		.glores-A-form > colgroup {display:table-column-group;}
		.glores-A-form > thead > tr > th {height:45px; font-size:13px; border-bottom:2px solid #ececec; background:#f8f8f8;}
		.glores-A-form > tbody > tr > th {display:table-cell; height:25px; padding:10px 0; font-size:13px; text-align:center; border-top:1px solid #ececec; background:#f8f8f8;}
		.glores-A-form > tbody > tr > td {display:table-cell; height:25px; padding:10px; font-size:13px; border-top:1px solid #ececec;}
		.glores-A-form > tbody > tr:first-child > th, .glores-A-form > tbody > tr:first-child > td {border-top:none;}
		.glores-A-form input[type=text],
		.glores-A-form input[type=password],
		.glores-A-form input[type=number] {width:auto;}

		.glores-A-agree-wrap > div {margin:50px 0;}
		.glores-A-agree-wrap .glores-A-agree-box {height:250px; padding:20px;}

		.glores-A-btn-wrap.glores-A-left {text-align:left;}
		.glores-A-btn-wrap a,
		.glores-A-btn-wrap button {margin-left:6px;}
		.glores-A-btn-wrap a:first-child,
		.glores-A-btn-wrap button:first-child {margin-left:0;}
	}
</style>


<div class="ShoppingForm">
	<!--@if($itemArray[goodsName]||$_GET[idx])-->
		{$s_write_form}
			<input type="hidden" name="s_v11" value="{$itemArray[goodsName]}">
			<input type="hidden" name="s_v13" value="{$itemArray[discription]}">
			<input type="hidden" name="s_v19" value="{$itemArray[imgUrl]}">
			<input type="hidden" name="s_v20" value="{$itemArray[quantity]}">
			<input type="hidden" name="s_v23" value="{$itemArray[price]}">

			<!-- 상품정보 -->
			<dl class="productInfo" cond="$itemArray[goodsName]">
				<dt><img src="{$itemArray[imgUrl]}" alt="{$itemArray[goodsName]}"></dt>
				<dd>
					<p class="title">{$itemArray[goodsName]}</p>
					<p class="discription">{$itemArray[discription]}</p>
					<ul class="options">
						<li><span data-lan="kr">가격</span> : <em>{$itemArray[price]|number}</em></li>
						<li><span data-lan="kr">수량</span> : {$itemArray[quantity]} ea</li>
					</ul>
				</dd>
			</dl>
			<!-- // 상품정보 -->

			<div class="glores-A-customize-form">
				<!--@foreach($myreg_form_row as $k => $v)-->
				{@
				if($k==0) continue;
				@}
				<h4 class="glores-A-title">{$v[subject]}</h4>

				<table class="glores-A-form">
					<colgroup>
						<col width="25%">
						<col width="75%">
					</colgroup>

					<tbody>
						<tr loop="$v[fields] as $k2 => $v2">
							<th>{$v2[fieldName]} <i cond="$v2[fieldImportant]==1">*</i></th>
							<td>{$v2[fieldTag]} <i class="glores-A-info" cond="$v2[fieldComment]">{$v2[fieldComment]}</i></td>
						</tr>
					</tbody>
				</table>
				<!--@end-->

				<!--@if($myreg_app_yn)-->
				<h4 class="glores-A-title" data-lan="kr">결제 정보</h4>

				<table class="glores-A-form">
					<colgroup>
						<col width="25%">
						<col width="75%">
					</colgroup>

					<tbody>
						<tr cond="$myreg_price_name1">
							<th>{$myreg_price_name1}</th>
							<td>{$myreg_price_table1}</td>
						</tr>
						<tr cond="$myreg_price_name2">
							<th>{$myreg_price_name2}</th>
							<td>{$myreg_price_table2}</td>
						</tr>
						<tr cond="$myreg_price_name3">
							<th>{$myreg_price_name3}</th>
							<td>{$myreg_price_table3}</td>
						</tr>
						<tr>
							<th data-lan="kr">총 금액</th>
							<td>
								<input type="hidden" name="total_price" id="total_price" value="0">
								<span id="total_price_str">0</span>
							</td>
						</tr>
					</tbody>
				</table>

				<h4 class="glores-A-title" data-lan="kr">결제 방법</h4>

				<div class="glores-A-payment-type">
					{$app_mode}
				</div>

				<div id="online_span">
					<table class="glores-A-form">
						<colgroup>
							<col width="200">
							<col>
						</colgroup>

						<tbody>
							<tr>
								<th data-lan="kr">결제은행</th>
								<td>
									<select name="admin_bank" id="admin_bank" class="glores-A-select">
										{$admin_bank_op}
									</select>
								</td>
							</tr>
							<tr>
								<th data-lan="kr">입금자명</th>
								<td>
									<input type="text" name="bank_name" id="bank_name" size="16" class="glores-A-input-txt">
									<span class="glores-A-info" data-lan="kr">반드시 입금자명을 써주세요.</span>
								</td>
							</tr>
							<tr>
								<th data-lan="kr">입금 예정일</th>
								<td>
									<select name="bank_date" id="bank_date" class="glores-A-select">
										{$bank_date_op}
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<!--@end-->

				<div class="glores-A-agree-wrap glores-A-mgt">
					<div class="glores-A-agree">
						<h4 class="glores-A-title" data-lan="kr">개인정보취급방침</h4>
						<div class="glores-A-agree-box">
							<h5 data-lan="kr">수집하는 개인정보의 항목</h5>
							<p data-lan="kr">회사는 회원가입, 상담, 서비스 신청 등등을 위해 아래와 같은 개인정보를 수집하고 있습니다.</p>
							<ul>
								<li><strong>- <span data-lan="kr">수집항목</span></strong> : {$privacy_column_list}</li>
								<li><strong>- <span data-lan="kr">개인정보 수집방법</span></strong> : <span data-lan="kr">홈페이지</span>(<span data-lan="kr">회원가입</span>)</li>
							</ul>

							<h5 data-lan="kr">개인정보의 수집 및 이용목적</h5>
							<p data-lan="kr">회사는 수집한 개인정보를 다음의 목적을 위해 활용합니다.</p>
							<ul>
								<li><strong>- <span data-lan="kr">서비스 제공에 관한 계약 이행 및 서비스 제공에 따른 요금정산</span></strong>
									<p><span data-lan="kr">구매 및 요금 결제, 물품배송 또는 청구지 등 발송</span></p>
								</li>
								<li><strong>- <span data-lan="kr">회원 관리</span></strong>
									<p data-lan="kr">회원제 서비스 이용에 따른 본인확인, 개인 식별, 불량회원의 부정 이용 방지와 비인가 사용 방지, 가입 의사 확인, 연령확인</p>
								</li>
							</ul>

							<h5 data-lan="kr">개인정보의 보유 및 이용기간</h5>
							<p data-lan="kr">회사는 개인정보 수집 및 이용목적이 달성된 후에는 예외 없이 해당 정보를 지체 없이 파기합니다.</p>
						</div>

						<div class="agree-check-box">
							<input type="checkbox" name="myreg_privacy_chk" id="myreg_privacy_chk" value="1">
							<label for="myreg_privacy_chk" data-lan="kr">위 개인정보취급방침에 동의합니다.</label>
						</div>
					</div>
				</div>

				<div class="glores-A-btn-wrap">
					<a href="{$myreg_submit_url}" class="glores-A-btn-type1 glores-A-medium glores-A-highlight" data-lan="kr">확인</a>
				</div>
			</div>
		{$e_write_form}
	<!--@else-->
		<div class="empty" data-lan="kr">구매상품이 없습니다.</div>
	<!--@end-->
</div>