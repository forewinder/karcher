<?
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.popup.php";
require_once ('./_inc/config.inc.php');

$shople = Core::loader('shople');

$mode = $_GET['m'];
?>
<script type="text/javascript" src="./_inc/common.js"></script>
<!-- * -->
<script type="text/javascript">
function fnPutReason() {

	var data = $('frmClaim').serialize().toQueryParams();
	opener.nsShople.claim.exchange.<?=$mode?>( data );
	self.close();
	return false;
}
</script>

<div class="title title_top" style="margin-top:10px;">��ȯ ����ó��<span>&nbsp;</span></div>

<p class="gd_notice">
<span>�̹� �߼۵� ��ǰ�� ���� �����ȣ�� �Է��Ͻø� ��ҿ�û�� �̵ǰ� �����ڿ��� �ڵ����� ��ҺҰ��� ���� �ȳ� ������ �߼۵˴ϴ�.</span>
<span class="red">��ҿ�û�� ��ǰ�� ������� ��ǰ�̶�� [��ҺҰ�]ó����, ������ۻ�ǰ���� ��� [�߼�ó��]�˴ϴ�.</span>
</p>

<form name="frmClaim" id="frmClaim" method="post" action="" onSubmit="return fnPutReason();">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�ù�缱��</td>
	<td>
		<select name="dlvEtprsCd">
		<option value="">����</option>
		<? foreach ($_spt_ar_dlv_company as $k => $v) { ?>
		<option value="<?=$k?>" <?=($shople->cfg['dlv_company'] == $k ? 'selected' : '')?>><?=$v?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>�����ȣ�Է�</td>
	<td><input type=text name="invcNo" value="" onkeydown="onlynumber()"></td>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_save.gif">
<img src="../img/btn_cancel.gif" class="hand" onClick="self.close();">

</div>
</form>

<!-- eof * -->
<script type="text/javascript">
linecss();
table_design_load();
</script>
</body>
</html>