<?php
/**********************************************************************************************
*
* ���ϸ� : AGS_escrow_ing.php
* �ۼ����� : 2009/3/20
*
* ���ϵ� ����Ÿ�� �޾Ƽ� ���ϰ�����û�� �մϴ�.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/

include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

// �ֹ�����
$ordno = $_POST['ordno'];
$query = "
select
	orddt,settlekind,escrowno
from
	".GD_ORDER." a
	left join ".GD_LIST_DELIVERY." b on a.deliveryno = b.deliveryno
where
	a.ordno = '$ordno'
";
$data = $db->fetch($query);
$pg_settlekind	= array(
	'c'	=> '01',
	'o'	=> '02',
	'v'	=> '03',
);

/** Function Library **/
require "aegis_Func.php";


/****************************************************************************
*
* [1] �ô�����Ʈ ����ũ�� ������ ����� ���� ��ż��� IP/Port ��ȣ
*
* $IsDebug : 1:����,���� �޼��� Print 0:������
* $LOCALADDR : �ô�����Ʈ ������ ����� ����ϴ� ��ȣȭProcess�� ��ġ�� �ִ� IP (220.85.12.74)
* $LOCALPORT : ��Ʈ
* $ENCTYPE : E : �ô�����Ʈ ����ũ��
* $CONN_TIMEOUT : ��ȣȭ ����� ���� ConnectŸ�Ӿƿ� �ð�(��)
* $READ_TIMEOUT : ������ ���� Ÿ�Ӿƿ� �ð�(��)
*
****************************************************************************/

$IsDebug = 0;
$LOCALADDR  = "220.85.12.74";
$LOCALPORT  = "29760";
$ENCTYPE    = "E";
$CONN_TIMEOUT = 10;
$READ_TIMEOUT = 30;


/****************************************************************************
*
* [2] AGS_escrow.html �� ���� �Ѱܹ��� ����Ÿ
*
****************************************************************************/
$TrCode = trim('E200');												//�ŷ��ڵ�
$PayKind = trim($pg_settlekind[$data['settlekind']]);				//��������
$RetailerId = trim($pg['id']);										//����ID
$DealTime = trim(str_replace('-','',substr($data['orddt'],0,10)));	//��������
$SendNo = trim($data['escrowno']);									//�ŷ�������ȣ
$IdNo = $_POST['id_no'];											//�޴�����ȣ

/****************************************************************************
*
* [3] ����Ÿ�� ��ȿ���� �˻��մϴ�.
*
****************************************************************************/

$ERRMSG = "";

if( empty( $TrCode ) || $TrCode == "" )
{
	$ERRMSG .= "�ŷ��ڵ� �Է¿��� Ȯ�ο�� <br>";		//�ŷ��ڵ�
}

if( empty( $PayKind ) || $PayKind == "" )
{
	$ERRMSG .= "�������� �Է¿��� Ȯ�ο�� <br>";		//��������
}

if( empty( $RetailerId ) || $RetailerId == "" )
{
	$ERRMSG .= "�������̵� �Է¿��� Ȯ�ο�� <br>";		//�������̵�
}

if( empty( $DealTime ) || $DealTime == "" )
{
	$ERRMSG .= "�������� �Է¿��� Ȯ�ο�� <br>";		//�����ð�
}

if( empty( $SendNo ) || $SendNo == "" )
{
	$ERRMSG .= "�ŷ�������ȣ �Է¿��� Ȯ�ο�� <br>";	//�ŷ�������ȣ
}


if( strlen($ERRMSG) == 0 )
{
	/****************************************************************************
	* TrCode = "E100" �߼ۿϷ�
	* TrCode = "E200" ����Ȯ��
	* TrCode = "E300" ���Ű���
	* TrCode = "E400" �������
	****************************************************************************/

	/****************************************************************************
	*
	* [4] �߼ۿϷ�/����Ȯ��/���Ű���/������ҿ�û (E100/E101)/(E200/E201)/(E300/E301)/(E400/E401)
	*
	* -- ������ ���̴� �Ŵ��� ����
	*
	* -- �߼ۿϷ� ��û ���� ����
	* + �����ͱ���(6) + ��ü ESCROW ����(1) + ������
	* + ������ ����(������ ������ "|"�� �Ѵ�.)
	* �ŷ��ڵ�(10)	| ��������(2)	| ��üID(20)	| �ֹε�Ϲ�ȣ(13) |
	* ��������(8)	| �ŷ�������ȣ(6)	|
	*
	* -- �߼ۿϷ� ���� ���� ����
	* + �����ͱ���(6) + ������
	* + ������ ����(������ ������ "|"�� �Ѵ�.
	* �ŷ��ڵ�(10)	|��������(2)	| ��üID(20)	| ����ڵ�(2)	| ��� �޽���(100)	|
	*
	*****************************************************************************/

	$ENCTYPE = "E";

	/****************************************************************************
	* ���� ���� Make
	****************************************************************************/

	$sDataMsg = $ENCTYPE.
		$TrCode."|".
		$PayKind."|".
		$RetailerId."|".
		$IdNo."|".
		$DealTime."|".
		$SendNo."|";

	$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );

	/****************************************************************************
	*
	* ���� �޼��� ����Ʈ
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $sSendMsg."<br>";
	}

	/****************************************************************************
	*
	* ��ȣȭProcess�� ������ �ϰ� ���� ������ �ۼ���
	*
	****************************************************************************/

	$fp = fsockopen( $LOCALADDR, $LOCALPORT , &$errno, &$errstr, $CONN_TIMEOUT );


	if( !$fp )
	{
		/** ���� ���з� ���� �ŷ����� �޼��� ���� **/

		$rSuccYn = "n";
		$rResMsg = "���� ���з� ���� �ŷ�����";
	}
	else
	{
		/** ���ῡ �����Ͽ����Ƿ� �����͸� �޴´�. **/

		$rResMsg = "���ῡ �����Ͽ����Ƿ� �����͸� �޴´�.";


		/** ���� ������ ��ȣȭProcess�� ���� **/

		fputs( $fp, $sSendMsg );

		socket_set_timeout($fp, $READ_TIMEOUT);

		/** ���� 6����Ʈ�� ������ ������ ���̸� üũ�� �� �����͸�ŭ�� �޴´�. **/

		$sRecvLen = fgets( $fp, 7 );
		$sRecvMsg = fgets( $fp, $sRecvLen + 1 );

		/****************************************************************************
		*
		* ������ ���� ���������� �Ѿ�� ���� ��� �̺κ��� �����Ͽ� �ֽñ� �ٶ��ϴ�.
		* PHP ������ ���� ���� ������ ���� üũ�� ������������ �߻��� �� �ֽ��ϴ�
		* �����޼���:���� ������(����) üũ ���� ��ſ����� ���� ���� ����
		* ������ ���� üũ ������ �Ʒ��� ���� �����Ͽ� ����Ͻʽÿ�
		* $sRecvLen = fgets( $fp, 6 );
		* $sRecvMsg = fgets( $fp, $sRecvLen );
		*
		****************************************************************************/

		/** ���� close **/

		fclose( $fp );
	}

	/****************************************************************************
	*
	* ���� �޼��� ����Ʈ
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $sRecvMsg."<br>";
	}

	if( strlen( $sRecvMsg ) == $sRecvLen )
	{
		/** ���� ������(����) üũ ���� **/

		$RecvValArray = array();
		$RecvValArray = explode( "|", $sRecvMsg );

		$rTrCode        = $RecvValArray[0];
		$rPayKind       = $RecvValArray[1];
		$rRetailerId    = $RecvValArray[2];
		$rSuccYn        = $RecvValArray[3];
		$rResMsg        = $RecvValArray[4];

		/****************************************************************************
		*
		* ����ũ�� ��� ����� ���������� ���ŵǾ����Ƿ� DB �۾��� �� ���
		* ����������� �����͸� �����ϱ� �� �̺κп��� �ϸ�ȴ�.
		*
		* TrCode = "E101" �߼ۿϷ�����
		* TrCode = "E201" ����Ȯ������
		* TrCode = "E301" ���Ű�������
		* TrCode = "E401" ��ҿ�û����
		*
		* ���⼭ DB �۾��� �� �ּ���.
		* ����) $rSuccYn ���� 'y' �ϰ�� ����ũ�ι�۵�Ϲױ���Ȯ�μ���
		* ����) $rSuccYn ���� 'n' �ϰ�� ����ũ�ι�۵�Ϲױ���Ȯ�ν���
		* DB �۾��� �Ͻ� ��� $rSuccYn ���� 'y' �Ǵ� 'n' �ϰ�쿡 �°� �۾��Ͻʽÿ�.
		*
		****************************************************************************/

		// ����ó���Ǿ����� DB ó��
		$db->query("update ".GD_ORDER." set escrowconfirm=2 where ordno='$ordno'");
	}
	else
	{
		/** ���� ������(����) üũ ������ ��ſ����� ���� ���� ���з� ���� **/

		$rSuccYn = "n";
		$rResMsg = "���� ������(����) üũ ���� ��ſ����� ���� ���� ����";
	}
}
else
{
	$rSuccYn = "n";
	$rResMsg = $ERRMSG;
}

?>
<html>
<head>
<title>�ô�����Ʈ</title>
<style type="text/css">
<!--
body { font-family:"����"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"����"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; vertical-align:top; }
th { font-family:"����"; font-size:9pt; color:#000000; letter-spacing:0pt; line-height:180%; vertical-align:top; }
.clsleft { padding:0 10px; text-align:left; }
-->
</style>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><hr></td>
</tr>
<tr>
	<td align="center"><b>�ô�����Ʈ ����ũ�� �ŷ� ����Ȯ�� ��û ���</b></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td align="center">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<th class="clsleft">�� ����ũ�� ����</th>
		<td>����Ȯ��</td>
	</tr>
	<tr>
		<th class="clsleft">�� �������</th>
		<td><?php echo $rResMsg; ?></td>
	</tr>
	<tr>
		<th class="clsleft">�� ����ڵ�</th>
		<td><?php echo $rSuccYn; ?></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td><hr></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
</table>
</body>
</html>