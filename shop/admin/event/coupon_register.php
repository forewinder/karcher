<?
if (isset($_GET['couponcd'])) $_GET['mode'] = "modify";
if (! isset($_GET['mode'])) $_GET['mode'] = "register";

if($_GET['mode'] == 'register'){
	$hidden['sort'] = "style='display:none'";
	$location = "����������� > ���������";
	$msg = "<div class='title title_top'>���������<span>�������� �߱��� ������ ����ϴ�.";
}else{
	$location = "����������� > �����߱޳�������";
	$msg = "<div class='title title_top'>���������ϱ�<span>�������� �߱��� ������ �����մϴ�.";
}

include "../_header.php";
?>
<?=$msg?> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=12')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<?
include "_form.coupon.php";
?>
<div style="padding-top:10px"></div>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ȸ�������ٿ�ε������� ������ �ٸ� �������� �߱޹��� ȸ��1�� �� ��������� 1ȸ�� ���� �˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<? include "../_footer.php"; ?>