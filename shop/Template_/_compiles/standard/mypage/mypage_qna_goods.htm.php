<?php /* Template_ 2.2.7 2014/07/23 17:03:45 /www/hummingimc_godo_co_kr/shop/data/skin/standard/mypage/mypage_qna_goods.htm 000005567 */ 
if (is_array($GLOBALS["loop"])) $TPL__loop_1=count($GLOBALS["loop"]); else if (is_object($GLOBALS["loop"]) && in_array("Countable", class_implements($GLOBALS["loop"]))) $TPL__loop_1=$GLOBALS["loop"]->count();else $TPL__loop_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>


<!-- 상단이미지 || 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/standard/img/common/title_myqna.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > 마이페이지 > <B>나의상품문의</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<table width="100%" border="0" cellspacing="0" cellpadding="5" style="border-top-style:solid;border-top-color:#303030;border-top-width:2;border-bottom-style:solid;border-bottom-color:#D6D6D6;border-bottom-width:1;">
<tr height="23" bgcolor="#F0F0F0" class=input_txt>
	<th width=50>번호</th>
	<th width=60>이미지</th>
	<th>상품명/제목</th>
	<th width=80>작성자</th>
	<th width=80>작성일</th>
</tr>
</table>

<?php if($TPL__loop_1){foreach($GLOBALS["loop"] as $TPL_V1){?>
<div>
<?php if($TPL_V1["sno"]==$TPL_V1["parent"]){?>
<table width=100% cellpadding=3 cellspacing=0 style="border-bottom-style:solid;border-bottom-color:#E6E6E6;border-bottom-width:1;cursor:pointer;" onclick="view_content(this, event)">
<tr height=25 onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
	<td width=50 align="center"><?php echo $TPL_V1["idx"]?></td>
	<td width=60 align="center"><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimg($TPL_V1["img_s"], 50)?></a></td>
	<td>
	<TABLE cellpadding=0 cellspacing=0 border=0>
	<TR>
		<TD style="padding-top:5"><span style="font-weight:bold;"><?php echo $TPL_V1["goodsnm"]?></span> <a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><img src="/shop/data/skin/standard/img/common/btn_goodview2.gif" align=absmiddle></a></TD>
	</TR>
	<tr>
		<td style="padding-top:5; padding-bottom:5" class=stxt><?php echo $TPL_V1["subject"]?> <span style="color:#007FC8;">[<?php echo $TPL_V1["replecnt"]?>]</span></td>
	</tr>
	</TABLE>
	</td>
	<td width=80 align="center"><?php echo $TPL_V1["m_id"]?></td>
	<td width=80 align="center"><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
</tr>
</table>
<?php }elseif($TPL_V1["sno"]!=$TPL_V1["parent"]){?>
<table width=100% cellpadding=3 cellspacing=0 style="border-bottom-style:solid;border-bottom-color:#E6E6E6;border-bottom-width:1;cursor:pointer;" onclick="view_content(this, event)">
<tr height=25 onmouseover=this.style.background="#F7F7F7" onmouseout=this.style.background="">
	<td width=50 align="center"><?php echo $TPL_V1["idx"]?></td>
	<td width=60 align="center"><img src="/shop/data/skin/standard/img/common/myqna_answer.gif"></td>
	<td style="padding-top:5; padding-bottom:5" class=stxt><?php echo $TPL_V1["subject"]?></td>
	<td width=80 align="center"><?php echo $TPL_V1["m_id"]?></td>
	<td width=80 align="center"><?php echo substr($TPL_V1["regdt"], 0, 10)?></td>
</tr>
</table>
<?php }?>
<div style="display:none;padding:10;border-bottom-style:solid;border-bottom-color:#E6E6E6;border-bottom-width:1;">
	<div width="100%" style="padding-left:55"><?php echo $TPL_V1["contents"]?></div>
	<div style="text-align:right;">
<?php if($TPL_V1["authreply"]=='Y'){?>
	<a href="javascript:;" onclick="popup_register( 'reply_qna', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );"><img src="/shop/data/skin/standard/img/common/btn_reply.gif" border="0" align="absmiddle"></a>
<?php }?>
<?php if($TPL_V1["authmodify"]=='Y'){?>
	<a href="javascript:;" onclick="popup_register( 'mod_qna', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );"><img src="/shop/data/skin/standard/img/common/btn_modify2.gif" border="0" align="absmiddle"></a>
<?php }?>
<?php if($TPL_V1["authdelete"]=='Y'){?>
    <a href="javascript:;" onclick="popup_register( 'del_qna', '<?php echo $TPL_V1["goodsno"]?>', '<?php echo $TPL_V1["sno"]?>' );"><img src="/shop/data/skin/standard/img/common/btn_delete.gif" border="0" align="absmiddle"></a>
<?php }?>
    </div>
</div>
</div>
<?php }}?>

<div class="pagediv"><?php echo $TPL_VAR["pg"]->page['navi']?></div>


<script language="javascript">

function popup_register( mode, goodsno, sno )
{
	if ( mode == 'del_qna' ) var win = window.open("../goods/goods_qna_del.php?mode=" + mode + "&sno=" + sno,"qna_register","width=400,height=200");
	else var win = window.open("../goods/goods_qna_register.php?mode=" + mode + "&goodsno=" + goodsno + "&sno=" + sno,"qna_register","width=600,height=500");
	win.focus();
}

var preContent;

function view_content(obj)
{
	var div = obj.parentNode;

	for (var i=1, m=div.childNodes.length;i<m;i++) {
		if (div.childNodes[i].nodeType != 1) continue;	// text node.
		else if (obj == div.childNodes[ i ]) continue;

		obj = div.childNodes[ i ];
		break;
	}

	if (preContent && obj!=preContent){
		obj.style.display = "block";
		preContent.style.display = "none";
	}
	else if (preContent && obj==preContent) preContent.style.display = ( preContent.style.display == "none" ? "block" : "none" );
	else if (preContent == null ) obj.style.display = "block";

	preContent = obj;
}
</script>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>