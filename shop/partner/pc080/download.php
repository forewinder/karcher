<?
@include "../../conf/config.php";
@include "../../conf/phone.php";

if($_GET['mode'] == '1'){
	$UserLoginId = $set['phone']['pc080_id'];
	$UserLoginPassword = $set['phone']['pwd'];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> New Document </TITLE>

<STYLE TYPE="text/css">
.txt {
	FONT-SIZE: 9pt; COLOR: #333333; LINE-HEIGHT: 16pt; FONT-FAMILY: "����","Verdana", "Arial", "sans-serif", "helvetica"
}
</STYLE>

<SCRIPT LANGUAGE="JavaScript">
 <!--

		  document.write('<OBJECT ID="MyPC080BIZ"' +
		  'CLASSID="CLSID:D7B800A6-CEE6-4EBA-B41F-8212AFDD43AC"' +
		  'width="0" height="0"></OBJECT>');


		function ActX_CheckValid() {

			if ( MyPC080BIZ.object == null ) {

			sp2 = window.navigator.userAgent.indexOf("SV1") != -1;
			ie7 = navigator.appVersion.indexOf("MSIE 7") != -1;

				if(ie7 || sp2) {

				instruction1.style.display = "block";
				instruction2.style.display = "none";
				instruction3.style.display = "none";

				} else {
				instruction1.style.display = "none";
				instruction2.style.display = "block";
				instruction3.style.display = "none";
				}

			}else if(!MyPC080BIZ.GetPC080InstallState()) {

			    instruction1.style.display = "none";
				instruction2.style.display = "none";
				instruction3.style.display = "block";


			}
			else {
			    alert('�޽��� �� ��ġ�� �Ϸ�Ǿ����ϴ�!   ');

			    instruction1.style.display = "none";
			    instruction2.style.display = "none";
			    instruction3.style.display = "block";

			}

		}
 //-->
</SCRIPT>
</HEAD>

<BODY>
<!-- sp2 �� ie7 �̻��� ��� -->
<TABLE id="instruction1" style="display:none;" class="txt">
<TD>��ġ���<br>
 1. �Ʒ��� ���� ȭ������ ����� �˸� ǥ������ Ŭ���Ͽ� <B>[ActiveX ��Ʈ�� ��ġ]</B> ������ �ּ���! <br>
 <IMG SRC="activex_noti.gif" BORDER="0" ALT=""><br>
 2. �Ʒ��� ���� ���α׷� ��ġ Ȯ��â���� <B>[��ġ]</B>�� Ŭ���� �ּ���!<br>
 <IMG SRC="activexie7.gif"  BORDER="0" ALT=""><br>
���� ��ġ Ȯ��â�� ��Ÿ���� ������<br><A href="javascript:;" onClick="window.location.reload();">����</A>�� Ŭ���� �ּ���!
 </TD>
</TR>
</TABLE>


<!-- window2k�� ��� -->
<TABLE id="instruction2" style="display:none;" class="txt">
<TR>
<TD>��ġ���<br>
 1. �Ʒ��� ���� ���α׷� ��ġ Ȯ��â���� <B>[Ȯ��]</B>�� Ŭ���� �ּ���!<br>
<IMG SRC="activex2k.gif" BORDER="0" ALT=""><br>
���� ��ġ Ȯ��â�� ��Ÿ���� ������<br><A href="javascript:;" onClick="window.location.reload();">����</A>�� Ŭ���� �ּ���!</TD>
</TR>
</TABLE>


<!-- Activex�� �̹� ��ġ�Ǿ� �ְ�, �޽������� ���ų� ���� ��ġ�Ϸ��� ����
�޽������� ���� ��� �ڵ����� ��ġ�ǰ� �ִ� ���� �������� �ٿ�ε��Ͽ� ��ġ�ϸ� �� -->
<TABLE id="instruction3" style="display:none;" class="txt" width="100%">
<tr><td align="center">
<table width = "400" cellpadding="1" cellspacing="1" bgcolor="#d4d4d4"  class="txt">
<tr bgcolor="#FFFFFF">
<td height="45" align="center" valign="middle"><font color="#B52301">�޽������� �ڵ����� ��ġ ���Դϴ�. ��ø� ��ٷ� �ֽʽÿ�!!</font><br>
<img src="loading.gif" width="338" height="10"></td>
</tr>
</table>
</td></tr>
<tr>
<td align="center"><br>����, �޽������� �ڵ����� ��ġ���� ������<br><A HREF="http://www.pc080.net/pc080sp/Korean/setup.exe">����</A>�� Ŭ���Ͽ� �������� ��ġ�� �ּ���!</td>
</tr>
</TABLE>


<SCRIPT LANGUAGE="JavaScript">
	ActX_CheckValid();
</SCRIPT>
<OBJECT ID="MyPC080BIZ" CLASSID="CLSID:D7B800A6-CEE6-4EBA-B41F-8212AFDD43AC" CODEBASE="http://www.pc080.net/pc080sp/PC080Biz.cab#version=1,1,0,0" width="0" height="0">
  <PARAM NAME="BizRegistryUrl" VALUE="http://<?=$_SERVER['HTTP_HOST']?><?=$cfg['rootDir']?>/conf/pc080.txt">
  <PARAM NAME="UserLoginId" value = "<?=$UserLoginId?>">
  <PARAM NAME="UserLoginPassword" value = "<?=$UserLoginPassword?>">
</OBJECT>
</center>
</BODY>
</HTML>