<?php /* Template_ 2.2.7 2015/11/30 11:04:46 /www/hummingimc_godo_co_kr/shop/data/skin/standard/outline/side/cs.htm 000001888 */  $this->include_("dataBanner");?>
<!-- 고객센터 메뉴 시작 -->
<div id="left_cs" style="width:<?php echo $GLOBALS["cfg"]['shopSideSize']?>px;">
	<div class="title_cs">고객센터</div>
	<div class="line_cs"></div>
	<div style="padding:5px 0 3px 8px;">
	<table cellpadding=0 cellspacing=7 border=0>
	<tr>
		<td><a href="<?php echo url("service/faq.php")?>&" class="lnbmenu">ㆍFAQ</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("service/guide.php")?>&" class="lnbmenu">ㆍ이용안내</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("mypage/mypage_qna.php")?>&" class="lnbmenu">ㆍ1:1문의게시판</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("member/find_id.php")?>&" class="lnbmenu">ㆍID찾기</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("member/find_pwd.php")?>&" class="lnbmenu">ㆍ비밀번호찾기</a></td>
	</tr>
	<tr>
		<td><a href="<?php echo url("mypage/mypage.php")?>&" class="lnbmenu">ㆍ마이페이지</a></td>
	</tr>
	</table>
	</div>
	<div class="line_cs"></div>
</div>
<!-- 고객센터 메뉴 끝 -->

<!-- 메인왼쪽배너 : Start -->
<table cellpadding="0" cellspacing="0" border="0"width=100%>
<tr><td align="left"><!-- (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 4))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
<tr><td align="left"><!-- (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 5))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td></tr>
</table>
<!-- 메인왼쪽배너 : End -->