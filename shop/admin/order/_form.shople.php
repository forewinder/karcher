<?php
$channel = preg_replace('/_form\.([a-z]+)\.php/','$1',basename(__FILE__));
// �ܺ� �ֹ����̹Ƿ� �߰輭���� �����͸� ����ȭ ���� �����ش�.

/*
$integrate_order = Core::loader('integrate_order');
$integrate_order -> doSync();
*/


// �ֹ�����
$orderInfo = $db->fetch("SELECT * FROM ".GD_INTEGRATE_ORDER." WHERE channel = '$channel' AND ordno = '".$_GET['ordno']."'",1);

if (!$orderInfo) {
	msg('�ֹ������� �������� �ʽ��ϴ�.',-1);
	exit;
}


// �ֹ���ǰ����
$orderItems = array();
$query = "
SELECT
	OI.*
FROM ".GD_INTEGRATE_ORDER." AS O
INNER JOIN ".GD_INTEGRATE_ORDER_ITEM." AS OI
ON O.channel = OI.channel AND O.ordno = OI.ordno
WHERE O.channel = '$channel' AND O.ordno = '".$_GET['ordno']."'
";
$rs = $db->query($query);
while ($item = $db->fetch($rs,1)) {
	$orderItems[] = $item;
}
?>

<div class="title title_top">�ֹ��󼼳���</div>
<table class="tb" cellpadding="4" cellspacing="0">
<tr height="25" bgcolor="#2E2B29" class="small4" style="padding-top:8px">
	<th><font color="white">��ȣ</font></th>
	<th><font color="white">��ǰ��</font></th>
	<th><font color="white">�ɼ�</font></th>
	<th><font color="white">����</font></th>
	<th><font color="white">��ǰ����</font></th>
</tr>
<col align=center>
<col>
<col align=center span=4>
<? for ($i=0,$m=sizeof($orderItems);$i<$m;$i++) { ?>
<? $item = $orderItems[$i]; ?>
<tr>
	<td width=70 nowrap><?=($i+1)?></td>
	<td width=100%><?=htmlspecialchars($item['goodsnm'])?></td>
	<td width=200 nowrap><?=htmlspecialchars($item['option'])?></td>
	<td width=80 nowrap><?=number_format($item['ea'])?></td>
	<td width=150 nowrap><?=number_format($item['price'])?>��</td>
</tr>
<? } ?>
</table>
<br><br>

<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>�ֹ�����</b></font></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�ֹ� �Ͻ�</td>
	<td><?=($orderInfo['ord_date'])?></td>
</tr>
<tr>
	<td>�ֹ� ����</td>
	<td><?=$integrate_cfg['step'][$orderInfo['ord_status']]?></td>
</tr>
<tr>
	<td>�Ǹ� �Ϸ� �Ͻ�</td>
	<td><?=($orderInfo['fin_date'])?></td>
</tr>
</table>
<br><br>

<!-- �ֹ�����(����Ȯ��) -->
<? if($orderInfo['ord_status'] == 1) { ?>
<form name="frmPlaceOrder" id="frmPlaceOrder" action="./indb.php" target="_self" method="post">
<input type="hidden" name="mode" value="integrate_action">
<input type="hidden" name="ord_status" value="2">
<input type="hidden" name="ordno" value="<?=$orderInfo['ordno']?>">
<input type="hidden" name="channel" value="<?=$orderInfo['channel']?>"><table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�ֹ�����ó��</td>
	<td><input type="button" value="�ֹ������ϱ�" onclick="document.frmPlaceOrder.submit()"></td>
</tr>
</table>
</form>
<br>
<? } ?>

<!-- �߼�ó�� -->
<? if($orderInfo['ord_status'] == 2) { ?>
<form name="frmShipOrder" id="frmShipOrder" action="./indb.php" target="_self" method="post">
<input type="hidden" name="mode" value="integrate_action">
<input type="hidden" name="ord_status" value="3">
<input type="hidden" name="ordno" value="<?=$orderInfo['ordno']?>">
<input type="hidden" name="channel" value="<?=$orderInfo['channel']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�߼�ó��</td>
	<td>
		<table cellpadding="0">
		<td>��۹�� :</td>
		<td>
		<?
		$shople = Core::loader('shople');
		echo $integrate_cfg['dlv_company']['shople'][$shople->cfg['shople']['dlv_company']];
		?>
		<input type="hidden" name="dlv_company" value="<?=$shople->cfg['shople']['dlv_company']?>">
		</td>
		</tr>
		<tr>
		<td>�����ȣ :</td>
		<td><input type="text" name="dlv_no" style="width:300px" class="iptTrackingNumber"></td>
		</tr>
		<tr>
		<td></td>
		<td> <input type="button" value="�߼�ó���ϱ� " onclick="document.frmShipOrder,submit();"></td>
		</tr>
		</table>
	</td>
</tr>
</table></form>
<br>
<? } ?>

<!-- �Ǹ� �ź� ó�� -->
<? if($orderInfo['ord_status'] == 1 || $orderInfo['ord_status'] == 2) { ?>
<form name="frmCancelSale" id="frmCancelSale" action="./indb.php" target="_self" method="post">
<input type="hidden" name="mode" value="integrate_action">
<input type="hidden" name="ord_status" value="11">
<input type="hidden" name="ordno" value="<?=$orderInfo['ordno']?>">
<input type="hidden" name="channel" value="<?=$orderInfo['channel']?>">
<input type="hidden" name="reject" value="1"><!-- ��ҽ��� ó���� ��� �ڵ尡 ������ �׼��� �޶�, �Ǹ� �ź� ���� �˸��� ����. -->
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�ǸźҰ�ó��</td>
	<td>
		<table cellpadding="0">
		<tr>
		<td>�ǸźҰ� ���� :</td>
		<td>
		<select name="cs_reason_code" style="width:150px">
		<? foreach($integrate_cfg['claim_code']['shople'] as $code => $name) { ?>
		<? if ($code > 100) continue; ?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>
		</td>
		<tr>
		<td>�ǸźҰ� �޼��� :</td>
		<td><input type="text" name="cs_reason" style="width:300px"></td>
		</tr>
		<tr>
		<td></td>
		<td> <input type="button" value="�Ǹ�����ϱ� " onclick="document.frmCancelSale.submit()"></td>
		</tr>
		</table>
	</td>
</tr>
</table></form>
<br>
<? } ?>

<!-- �ֹ����(��ҽ���) -->
<? if($orderInfo['ord_status'] == 10) { ?>
<form name="frmCancelOrder" id="frmCancelOrder" action="./indb.php" target="_self" method="post">
<input type="hidden" name="mode" value="integrate_action">
<input type="hidden" name="ord_status" value="11">
<input type="hidden" name="ordno" value="<?=$orderInfo['ordno']?>">
<input type="hidden" name="channel" value="<?=$orderInfo['channel']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�ֹ����ó��</td>
	<td>
		<table cellpadding="0">
		<tr>
		<td>�ֹ���� ���� :</td>
		<td>
		<select name="cs_reason_code" style="width:150px">
		<? foreach($integrate_cfg['claim_code']['shople'] as $code => $name) { ?>
		<? if ($code < 100) continue; ?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>
		</td>
		<tr>
		<td>�ֹ���� �޼��� :</td>
		<td><input type="text" name="cs_reason" style="width:300px"></td>
		</tr>
		<tr>
		<td></td>
		<td> <input type="button" value="�ֹ�����ϱ� " onclick="document.frmCancelOrder.submit()"></td>
		</tr>
		</table>
	</td>
</tr>
</table></form>
<br>
<? } ?>



<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>��������</b></font></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�ֹ� �ݾ�</td>
	<td><?=number_format($orderInfo['ord_amount'])?>��</td>
</tr>
<tr>
	<td>��ۺ�</td>
	<td><?=number_format($orderInfo['dlv_amount'])?>��</td>
</tr>
<tr>
	<td>���� �ݾ�</td>
	<td><?=number_format($orderInfo['pay_amount'])?>��</td>
</tr>
<tr>
	<td>���� ���</td>
	<td>-</td>
</tr>
<tr>
	<td>���� �Ͻ�</td>
	<td><?=($orderInfo['pay_date'])?></td>
</tr>

<tr>
	<td>��ۺ� ����</td>
	<td><?=htmlspecialchars($orderInfo['dlv_type'])?></td>
</tr>
</table>


<br><br>
<table border="0" width="100%">
<tr>
<td width="50%"  valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>�ֹ�������</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�̸�</td>
		<td><?=htmlspecialchars($orderInfo['ord_name'])?></td>
	</tr>
	<tr>
		<td>���̵�(11����)</td>
		<td><?=htmlspecialchars($orderInfo['m_id_out'])?></td>
	</tr>
	<tr>
		<td>����ó</td>
		<td><?=htmlspecialchars($orderInfo['ord_mobile'])?></td>
	</tr>
	<tr>
		<td>���� �ּ�</td>
		<td><?=htmlspecialchars($orderInfo['ord_email'])?></td>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>������ ����</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�̸�</td>
		<td><?=htmlspecialchars($orderInfo['rcv_name'])?></td>
	</tr>
	<tr>
		<td>����� �ּ�</td>
		<td>(<?=substr($orderInfo['rcv_zipcode'],0,3)?>-<?=substr($orderInfo['rcv_zipcode'],3,3)?>) <br>
		<?=$orderInfo['rcv_address']?> </td>
	</tr>
	<tr>
		<td>����ó1</td>
		<td><?=htmlspecialchars($orderInfo['rcv_phone'])?></td>
	</tr>
	<tr>
		<td>����ó2</td>
		<td><?=htmlspecialchars($orderInfo['rcv_mobile'])?></td>
	</tr>
	<tr>
		<td>��� �޼���</td>
		<td><?=htmlspecialchars($orderInfo['dlv_message'])?></td>
	</tr>
	</table>
</td>
</table>

<br><br>


<table border="0" width="100%">
<tr>
<td width="50%"  valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>�������</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>��� �Ͻ�</td>
		<td><?=($orderInfo['dlv_date'])?></td>
	</tr>
	<tr>
		<td>��� ���</td>
		<td><?=$orderInfo['dlv_method']?></td>
	</tr>
	<tr>
		<td>��ۻ�</td>
		<td><?=$integrate_cfg['dlv_company']['shople'][$orderInfo['dlv_company']]?></td>
	</tr>
	<tr>
		<td>���� ��ȣ</td>
		<td><?=htmlspecialchars($orderInfo['dlv_no'])?></td>
	</tr>
	<tr>
		<td>��� �Ϸ� �Ͻ�</td>
		<td><?=($orderInfo['fin_date'])?></td>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>���/��ǰ/��ȯ ����</b></font></div>
	<?
	$cs_type = null;
	switch (floor($orderInfo['ord_status'] / 10) * 10) {	// x ����
		case 10 :
			$cs_type = '���';
			break;
		case 20 :
			$cs_type = 'ȯ��';
			break;

		case 30 :
			$cs_type = '��ǰ';
			break;
		case 40 :
			$cs_type = '��ȯ';
			break;
	}
	?>
	<? if ($cs_type !== null) { ?>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td><?=$cs_type?> ��û �Ͻ�</td>
		<td><?=($orderInfo['cs_regdt'])?></td>
	</tr>
	<tr>
		<td><?=$cs_type?> ó�� �Ͻ�</td>
		<td><?=($orderInfo['cs_confirmdt'])?></td>
	</tr>
	<tr>
		<td><?=$cs_type?> ����</td>
		<td><?=($orderInfo['cs_reason_type'])?></td>
	</tr>
	<tr>
		<td><?=$cs_type?> ���� (�ڼ���)</td>
		<td><?=htmlspecialchars($orderInfo['cs_reason'])?></td>
	</tr>
	</table>
	<? } ?>
</td>
</table>

<br><br>