<?php /* Template_ 2.2.7 2015/08/04 10:18:13 /www/hummingimc_godo_co_kr/shop/data/skin_mobileV2/light/index.htm 000005113 */  $this->include_("mobileAnimationBanner");
if (is_array($GLOBALS["cfg_step"])) $TPL__cfg_step_1=count($GLOBALS["cfg_step"]); else if (is_object($GLOBALS["cfg_step"]) && in_array("Countable", class_implements($GLOBALS["cfg_step"]))) $TPL__cfg_step_1=$GLOBALS["cfg_step"]->count();else $TPL__cfg_step_1=0;?>
<?php $this->print_("header",$TPL_SCP,1);?>

<style type="text/css">
#background {
	position : fixed;
	left : 0;
	top : 0;
	bottom:0;
	width : 100%;
	height : 100%;
	background : rgba(0, 0, 0, 0.2);
	display:block;
	z-index:98;
}

#popup {position:fixed; bottom:0px; width:100%;z-index:99;}
#popup .popup_wrap{width:308px; margin:6px auto;}
#popup .popup_wrap .popup_content{width:306px; text-align:center; border:solid 1px #dadada; background:#FFFFFF; min-height:150px;}
#popup .popup_wrap .popup_content img{max-width:100%; }
#popup .popup_wrap .popup_btn {height:26px;}
#popup .popup_wrap .popup_btn .btn-today-close{height:26px; width:176px; background:url('/shop/data/skin_mobileV2/light/common/img/main/btn_p_today.png') no-repeat; float:left; line-height:26px; color:#FFFFFF; text-align:center;}
#popup .popup_wrap .popup_btn .btn-close{height:26px; width:132px; background:url('/shop/data/skin_mobileV2/light/common/img/main/btn_p_close.png') no-repeat;float:left; line-height:26px; color:#FFFFFF; text-align:center;}


</style>
<script type="text/javascript">
function closePop() {
	$("#popup").hide();
	$("#background").hide();
}

function closeTodayPop(popupNo) {
	setCookieMobile('popup_'+popupNo, 1, 1, '/');
	$("#popup").hide();
	$("#background").hide();

}
</script>
<?php if($TPL_VAR["page_cache_enabled"]){?>
<div id="template-mobile-popup" style="display: none;">
	<div class="popup_wrap">
		<div class="popup_content"></div>
		<div class="popup_btn">
			<div class="btn-today-close">오늘하루 닫기</div>
			<div class="btn-close">닫기</div>
		</div>
	</div>
</div>
<div id="background" style="display: none;"></div>
<?php }else{?>
<?php if($TPL_VAR["popup_data"]){?>
<?php if(isset($_COOKIE['popup_'.$TPL_VAR["popup_data"]["mpopup_no"]])===false){?>
<div id="popup" >

<div class="popup_wrap">
<?php if($TPL_VAR["popup_data"]["link_url"]){?>
<a href="http://<?php echo $TPL_VAR["popup_data"]["link_url"]?>">
<?php }?>
<div class="popup_content">
<?php if($TPL_VAR["popup_data"]["popup_type"]=='0'){?>
	<?php echo $TPL_VAR["popup_data"]["popup_img"]?>

<?php }else{?>
	<?php echo $TPL_VAR["popup_data"]["popup_body"]?>

<?php }?>
</div>
<?php if($TPL_VAR["popup_data"]["link_url"]){?>
</a>
<?php }?>
<div class="popup_btn">
	<div class="btn-today-close" onClick="javascript:closeTodayPop('<?php echo $TPL_VAR["popup_data"]["mpopup_no"]?>');">오늘하루 닫기</div>
	<div class="btn-close" onClick="javascript:closePop();">닫기</div>
</div>
</div>

</div>
<!--
오늘 하루 보이지 않음 <input type="checkbox" style="cursor:pointer; background-color:#000000;" onClick="setCookieMobile('popup', 1, 1, '/'); $('div#popup').hide();">
-->
<div id="background"></div>
<?php }?>
<?php }?>
<?php }?>

<?php if($GLOBALS["cfgMobileShop"]["mobileShopMainBanner"]){?>
<div class="main_banner content" ><img src="<?php echo $GLOBALS["cfg"]["rootDir"]?>/data/skin_mobileV2/<?php echo $GLOBALS["cfgMobileShop"]["tplSkinMobile"]?>/<?php echo $GLOBALS["cfgMobileShop"]["mobileShopMainBanner"]?>" alt="메인배너이미지" /></div>
<hr class="hidden" />
<?php }?>

<?php echo mobileAnimationBanner()?>


<section id="main" class="content" >
	<audio id="speach-description-player"></audio>
	<!-- 아래 상품리스트에 쓰이는 세부소스는 '디자인관리 > 상품(goods) > 상품목록 > 상품스크롤형,이미지스크롤형,탭형,매거진스크롤형,이벤트롤링형' 에 있습니다  -->
<?php if($TPL__cfg_step_1){foreach($GLOBALS["cfg_step"] as $TPL_V1){?>
<?php if($TPL_V1["chk"]){?>
<?php if($TPL_V1["page_type"]=='cate'){?><?php if($TPL_V1["text_temp1"]){?><div><?php }?><?php }?>
	<?php echo $this->assign('id',"main_list_01")?>

	<?php echo $this->assign('dpCfg',$TPL_V1)?>

	<?php echo $this->define('tpl_include_file_1',"goods/list/".$TPL_V1["tpl"].".htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

<?php if($TPL_V1["page_type"]=='cate'){?><?php if($TPL_V1["text_temp1"]){?></div><?php }?><?php }?>
	<div style="padding-top:15px"></div>
<?php }?>
<?php }}?>
</section>

<!-- 품절상품 마스크 -->
<div id="el-goods-soldout-image-mask" style="display:none;position:absolute;top:0;left:3px;background:url(<?php if($GLOBALS["cfg_soldout"]["mobile_display_overlay"]=='custom'){?><?php echo $GLOBALS["cfg"]["rootDir"]?>/data/goods/icon/mobile_custom_soldout<?php }else{?><?php echo $GLOBALS["cfg"]["rootDir"]?>/data/goods/icon/mobile_icon_soldout<?php echo $GLOBALS["cfg_soldout"]["mobile_display_overlay"]?><?php }?>) no-repeat center center; background-size:cover;"></div>
<script>
addOnloadEvent(function(){ setGoodsImageSoldoutMask() });
</script>

<?php $this->print_("footer",$TPL_SCP,1);?>