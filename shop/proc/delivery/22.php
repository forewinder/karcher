<?
	### ÀÏ¾ç·ÎÁö½º https://www.ilyanglogis.com/functionality/popup_result.asp?hawb_no=
	$out = split_betweenStr($out,'<!-- body -->',"<!-- /body -->");
?>
<style>
body { position:relative; color:#000000;font-family: '³ª´®°íµñ','¸¼Àº°íµñ','Malgun Gothic',serif;font-size:13px;}
#popContainer{ margin:20px; text-align:left;}
#popContainer .popBtn{ padding:10px 0; text-align:center;}
#popContainer .popBtn2{ padding:20px 0 10px; text-align:center;}
#popContainer .popGuideTitle{ padding:40px 0 0 0}
#popContainer .popReviewGuide{ padding:15px 0 15px 10px}
#popContainer .popReviewGuide li{ background:url(/images/bu_dot.gif) no-repeat 0 5px; padding-left:7px; font-size:11px; color:#ababab; padding-bottom:5px}
h2 {background:url('/images/pop_ico_delivery.gif') no-repeat left 2px; padding-left:17px; margin-bottom:10px; line-height:15px; font-size:14px; color:#4a4a4a;}
dl {border:1px solid #d1d1d1; border-top:2px solid #f7921e; padding:13px 17px 5px; margin-bottom:15px; font-size:13px; color:#434343;}
dl:after {content:""; clear:both; display:block; height:0%;}
dl dt {float:left; width:106px; height:20px;}
dl dd {height:20px;}
dl dd strong {color:#f14637;}
.tbsDelivery {text-align:center; width:100%; border:2px solid #7ea8ce; font-size:13px; border-collapse:separate;border-spacing:0; color:#999999;}
.tbsDelivery thead th{border-top:1px solid #cbdceb; color:#528dc2; background-color:#edf7ff;}
.tbsDelivery thead tr:first-child th{border-top:none;}
.tbsDelivery th {background:#f5f7f6; border-left:1px solid #cbdceb; height:24px; font-weight:normal; color:#535353; }
.tbsDelivery td {border-top:1px solid #cbdceb;border-left:1px solid #cbdceb; height:24px;  font-weight:normal;color:#595959;}
.tbsDelivery .borLn {border-left:none;}
.tbsDelivery a{color:#999999; text-decoration:none;}
</style>
<?=$out[0]?>