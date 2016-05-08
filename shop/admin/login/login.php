<?
include "../../lib/library.php";

// �α��ο��� �����ں��� �������� üũ
$alCert = Core::loader('adminLoginCert');
$alStat = $alCert->loginStatus();
if ($alStat == 'failure') {
	go("../login/adm_login_cert.php");
}
else if ($alStat == 'success') {
	unset($ici_admin);
}

// ������ üũ
if ($ici_admin) go("../index.php");

setCookie('Xtime',time(),0,'/');

### ���ȼ����� �α�url
if ($cfg['ssl'] == "1") { //���ȼ����� ����ϸ�...
	$loginActionUrl = $sitelink->link('member/login_ok.php','ssl');
} else {
	$loginActionUrl = "../../member/login_ok.php";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
	<title>���� - ���θ� ������ �α���</title>
	<link href="../basic/css/shop-admin-login.css" rel="stylesheet" type="text/css"/>
	<!--[if lte ie 8]><link href="../basic/css/old-ie.css" rel="stylesheet" type="text/css" /><![endif]-->
	<script type="text/javascript" src="../common.js"></script>
	<script type="text/javascript" src="../prototype.js"></script>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<script type="text/javascript">
		function checkedSSL(chkObj) {
			var form = chkObj.form;
			if(chkObj.checked) { //��������üũ
				form.action="<?=$loginActionUrl?>";
			} else { //��������üũ����
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
					<h1><img src="../img/login/logo.png" alt="����" /><strong>������ �α���</strong></h1>
					<fieldset>
						<legend>�α�����</legend>
						<div class="ipf">
							<label for="user-id" id="lbl_id">���̵� �Է��ϼ���.</label>
							<input type="text" name="m_id" class="text user-id" id="user-id" title="���̵� �Է�" value="<?=$_COOKIE['admId'] != '' ? $_COOKIE['admId'] : $_GET[m_id]?>" required label="���̵�" style="padding:0 0 0 55px" onfocus="focusEvent(this, 'lbl_id', 'on')" onfocusout="focusEvent(this, 'lbl_id', 'out')" />
						</div>
						<div class="ipf">
							<label for="user-pw" id="lbl_pw">��й�ȣ�� �Է��ϼ���.</label>
							<input type="password" name="password" class="text user-pw" id="user-pw" title="��й�ȣ �Է�" value="<?=$_GET[password]?>" required label="��й�ȣ" style="padding:0 0 0 55px" onfocus="focusEvent(this, 'lbl_pw', 'on')" onfocusout="focusEvent(this, 'lbl_pw', 'out')" />
						</div>
						<button type="submit" class="bt-login">�α���</button>
						<div class="support">
							<input type="checkbox" class="checkbox" id="save-id" name="saveId" />
							<label for="save-id">���̵� ����</label>
							<a href="javascript:layerPopup()">���̵�/��й�ȣ ã��</a>
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
							<h2>���̵�/��й�ȣ ã��</h2>
							<p>1. ��û �� �α��� ������ ������ ���� ���� ��� ���θ� <br />���� ������ <strong> ���Ϸ� �߼�</strong>�� �帳�ϴ�.</p>
							<ol>
								<li>�� ��ȸ�� �α���  </li>
								<li>�� ���̰� &gt; ���θ����� &gt;���θ� ��� �������� �̵�</li>
								<li>�� "���� ����" �׸��� [����] ��ư Ŭ��</li>
								<li>�� "���ø��� �ޱ�" �׸��� [���Ϻ�����] ��ư Ŭ��</li>
							</ol>
							<p>2. �α��� ������ ������ ���� �ִ� ��� ���̵�/��й� <br />ȣ�� <strong>�缳��</strong>�� �帳�ϴ�.</p>
							<ol>
								<li>�� ��ȸ�� �α���  </li>
								<li>�� ���̰� &gt; 1:1����/�亯 &gt; 1:1 �����ϱ� ������</li>
								<li>�� ������ ���̵�/��й�ȣ �缳�� ��û</li>
							</ol>
							<a href="http://www.godo.co.kr/" target="_blank">��ȸ�� �α��� �ٷΰ���</a>
							<button type="button" title="�ݱ�" onclick="layerClose()">�ݱ�</button>
						</div>
					</div>
				</div>
			</div>
			<!-- ��� ������� ������� �ּ� ���� �ڵ� ���� -->
			<span class="banner" id="adminlogin">
				<script>panelNoncheck('adminlogin', 'bannerPanel');</script>
			</span>
			<!-- //��� ������� ������� �ּ� ���� �ڵ� ���� -->
		</div>
	</div>
	<div id="adminlogin2"><script>panelNoncheck('adminlogin2', 'boardpanel');</script></div>
</form>
</body>
</html>