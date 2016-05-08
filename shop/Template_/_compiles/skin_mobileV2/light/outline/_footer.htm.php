<?php /* Template_ 2.2.7 2015/08/04 10:18:13 /www/hummingimc_godo_co_kr/shop/data/skin_mobileV2/light/outline/_footer.htm 000003092 */ ?>
<section id='footer' class='' style='padding-bottom:70px;'>
	<div class='company'>
		<div class="bottom_menu<?php if($GLOBALS["cfgMobileShop"]["mobileShopIcon"]){?> bottom_menu_favorite<?php }?>">
			<div class="bottom_menu_contents">
				<div class="bottom_menu_left">
					<a href="<?php echo $GLOBALS["mobileRootDir"]?>/proc/faq.php">&nbsp;&nbsp;&nbsp;&nbsp;FAQ</a>
					<div class="bar_area"><img src="/shop/data/skin_mobileV2/light/common/img/bottom/menubar.png" /></div>
				</div>
				<div class="bottom_menu_center">
					<a href="<?php echo $GLOBALS["mobileRootDir"]?>/proc/guide.php">이용안내</a>
					<div class="bar_area"><img src="/shop/data/skin_mobileV2/light/common/img/bottom/menubar.png" /></div>
				</div>
<?php if($GLOBALS["cfgMobileShop"]["mobileShopIcon"]){?>
				<div class="bottom_menu_center">
					<a href="javascript://" onclick="addHomeButtonForDevice('<?php echo $GLOBALS["shop_name"]?>')">즐겨찾기</a>
					<div class="bar_area"><img src="/shop/data/skin_mobileV2/light/common/img/bottom/menubar.png" /></div>
				</div>
<?php }?>
				<div class="bottom_menu_last">
					<a href="<?php echo $GLOBALS["cfg"]["rootDir"]?>/?pc" title="PC버전으로 보기" class="btn_pcmode">PC버전</a>
				</div>
			</div>
		</div>
		<div class="service_menu">
			<a href="<?php echo $GLOBALS["mobileRootDir"]?>/service/agrmt.php">이용약관</a> | <a href="<?php echo $GLOBALS["mobileRootDir"]?>/service/private.php" style="font-weight:bold;">개인정보취급방침</a>
		</div>
		<div class='lineinfo'>
			<div class="info_title">상호</div><div class="info_content"><?php echo $GLOBALS["cfg"]["shopName"]?></div>
		</div>
		<div class='lineinfo'>
			<div class="info_title">대표이사</div><div class="info_content"><?php echo $GLOBALS["cfg"]["ceoName"]?></div>
		</div>
		<div class='lineinfo'>
			<div class="info_title">주소</div><div class="info_content"><?php echo $GLOBALS["cfg"]["address"]?></div>
		</div>
		<div class='lineinfo'>
			<div class="info_title">고객센터</div><div class="info_content"><a href="<?php echo $GLOBALS["mobileRootDir"]?>/service/customer.php"><?php echo $GLOBALS["cfg"]["customerPhone"]?></div>
		</div>
		<div class='lineinfo'>
			<div class="info_title">사업자번호</div><div class="info_content"><?php echo $GLOBALS["cfg"]["compSerial"]?></div>
		</div>
		<div class='lineinfo'>
			<div class="info_title">통신판매업신고</div><div class="info_content"><?php echo $GLOBALS["cfg"]["orderSerial"]?></div>
		</div>
	</div>

	<div class="copyright" style='padding-top:5'>
		<div class='lineinfo'>COPYRIGHT (C) <span style='font-weight:bold;color:#9e9e9e;'><?php echo $GLOBALS["cfg"]["shopName"]?></span> ALL RIGHTS RESERVED.</div>
		<div class='lineinfo'>SYSTEM BY <span style='#9e9e9e;'>Godo</span>Mall</div>
	</div>
</section>

</div>
<iframe class="" id="ifrmHidden" name="ifrmHidden" src='<?php echo $GLOBALS["cfg"]["rootDir"]?>/blank.txt'></iframe>
</body>
</html>