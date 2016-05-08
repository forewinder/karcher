<?
include "../../lib/library.php";

// 로그인에서 관리자보안 인증상태 체크
$alCert = Core::loader('adminLoginCert');
$alStat = $alCert->loginStatus();
if ($alStat == 'failure') {
	go("../login/adm_login_cert.php");
}
else if ($alStat == 'success') {
	unset($ici_admin);
}

// 관리자 체크
if ($ici_admin) go("../index.php");

setCookie('Xtime',time(),0,'/');

### 보안서버용 로긴url
if ($cfg['ssl'] == "1") { //보안서버를 사용하면...
	$loginActionUrl = $sitelink->link('member/login_ok.php','ssl');
} else {
	$loginActionUrl = "../../member/login_ok.php";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
	<title>고도몰 - 쇼핑몰 관리자 로그인</title>
	<link href="../basic/css/shop-admin-login.css" rel="stylesheet" type="text/css"/>
	<!--[if lte ie 8]><link href="../basic/css/old-ie.css" rel="stylesheet" type="text/css" /><![endif]-->
	<script type="text/javascript" src="../common.js"></script>
	<script type="text/javascript" src="../prototype.js"></script>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<script type="text/javascript">
		function checkedSSL(chkObj) {
			var form = chkObj.form;
			if(chkObj.checked) { //보안접속체크
				form.action="<?=$loginActionUrl?>";
			} else { //보안접속체크해제
				form.action="../../member/login_ok.php";
			}
		}
		function layerPopup() {
			var layer = document.getElementById('layer');
			layer.style.display = "block";
		}
		function layerClose() {
			var layer = document.getElementById('layer');
			layer.style.display = "none";
		}
		function loginSubmit() {
			var save_id = document.getElementById('save-id').checked;
			if(save_id) {
				document.cookie = "adm_id=" + escape (value) + "; path=/; expires=" + expires.toGMTString();
			}
		}
		function focusEvent(obj, id, opt) {
			if(opt == 'on') {
				document.getElementById(id).style.display='none';
			} else {
				if(obj.value != '') {
					document.getElementById(id).style.display='none';
				} else {
					document.getElementById(id).style.display='block';
				}
			}
		}
		window.onload = function () {
			var userid = document.getElementById('user-id').value;
			if(userid != '') {
				document.getElementById('lbl_id').style.display='none';
				document.getElementById('save-id').checked = 'true';
			}
		}
	</script>
</head>
<body>
<form method="post" action="<?=$loginActionUrl?>" onsubmit="return chkForm(this);">
	<input type="hidden" name="returnUrl" value="../admin/index.php">
	<input type="hidden" name="adm_login" value="1" />
	<div class="top">
		<div class="wrap">
			<div class="login">
				<div class="in">
					<h1><img src="../img/login/logo.png" alt="고도몰" /><strong>관리자 로그인</strong></h1>
					<fieldset>
						<legend>로그인폼</legend>
						<div class="ipf">
							<label for="user-id" id="lbl_id">아이디를 입력하세요.</label>
							<input type="text" name="m_id" class="text user-id" id="user-id" title="아이디 입력" value="<?=$_COOKIE['admId'] != '' ? $_COOKIE['admId'] : $_GET[m_id]?>" required label="아이디" style="padding:0 0 0 55px" onfocus="focusEvent(this, 'lbl_id', 'on')" onfocusout="focusEvent(this, 'lbl_id', 'out')" />
						</div>
						<div class="ipf">
							<label for="user-pw" id="lbl_pw">비밀번호를 입력하세요.</label>
							<input type="password" name="password" class="text user-pw" id="user-pw" title="비밀번호 입력" value="<?=$_GET[password]?>" required label="비밀번호" style="padding:0 0 0 55px" onfocus="focusEvent(this, 'lbl_pw', 'on')" onfocusout="focusEvent(this, 'lbl_pw', 'out')" />
						</div>
						<button type="submit" class="bt-login">로그인</button>
						<div class="support">
							<input type="checkbox" class="checkbox" id="save-id" name="saveId" />
							<label for="save-id">아이디 저장</label>
							<a href="javascript:layerPopup()">아이디/비밀번호 찾기</a>
						</div>
						<? if($cfg[ssl] == "1" ) { ?>
						<div class="support">
							<label style="background:none;">
								<input type="checkbox" name="sslcheck" value="1" checked onclick="checkedSSL(this)" />
								<img src="../img/login/login_security.gif" style="cursor:pointer;" onmouseover="openLayer('ssltooltip','block')" onmouseout="openLayer('ssltooltip','none')" />
							</label>
						</div>
						<div style="margin:-43px 0 0 65px;"><img src="../img/login/login_security_info.png" id="ssltooltip" style="display:none;" /></div>
						<? } ?>
					</fieldset>
					<div id="layer" style="background:url('../img/login/layer.png') no-repeat left top" class="hide">
						<div>
							<h2>아이디/비밀번호 찾기</h2>
							<p>1. 신청 후 로그인 정보를 변경한 적이 없는 경우 쇼핑몰 <br />세팅 정보를 <strong> 메일로 발송</strong>해 드립니다.</p>
							<ol>
								<li>① 고도회원 로그인  </li>
								<li>② 마이고도 &gt; 쇼핑몰관리 &gt;쇼핑몰 목록 페이지로 이동</li>
								<li>③ "서비스 관리" 항목의 [관리] 버튼 클릭</li>
								<li>④ "세팅메일 받기" 항목의 [메일보내기] 버튼 클릭</li>
							</ol>
							<p>2. 로그인 정보를 변경한 적이 있는 경우 아이디/비밀번 <br />호를 <strong>재설정</strong>해 드립니다.</p>
							<ol>
								<li>① 고도회원 로그인  </li>
								<li>② 마이고도 &gt; 1:1문의/답변 &gt; 1:1 문의하기 페이지</li>
								<li>③ 관리자 아이디/비밀번호 재설정 요청</li>
							</ol>
							<a href="http://www.godo.co.kr/" target="_blank">고도회원 로그인 바로가기</a>
							<button type="button" title="닫기" onclick="layerClose()">닫기</button>
						</div>
					</div>
				</div>
			</div>
			<!-- 배너 사용하지 않을경우 주석 안의 코드 삭제 -->
			<span class="banner" id="adminlogin">
				<script>panelNoncheck('adminlogin', 'bannerPanel');</script>
			</span>
			<!-- //배너 사용하지 않을경우 주석 안의 코드 삭제 -->
		</div>
	</div>
	<div id="adminlogin2"><script>panelNoncheck('adminlogin2', 'boardpanel');</script></div>
</form>
</body>
</html>