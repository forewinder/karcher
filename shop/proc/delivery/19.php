<?
 ### 천일택배 http://www.chunil.co.kr/HTrace/HTrace.jsp?transNo=
$out = iconv("UTF-8","EUC-KR",$out);
$out = str_replace('<table width="550" border="0" cellspacing="0" cellpadding="5">', '<table width="550" border="0" cellspacing="0" cellpadding="5" style="display:none;">', $out);
$out = str_replace('<table width="550" height="30" border="0" cellpadding="0" cellspacing="0">', '<table width="550" height="30" border="0" cellpadding="0" cellspacing="0" style="display:none;">', $out);
?>
<?=$out?>