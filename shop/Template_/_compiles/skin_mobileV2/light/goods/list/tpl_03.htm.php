<?php /* Template_ 2.2.7 2015/12/28 15:21:51 /www/hummingimc_godo_co_kr/shop/data/skin_mobileV2/light/goods/list/tpl_03.htm 000004168 */ ?>
<div class="list_goodsscroll" id="goodsscroll-<?php echo $TPL_VAR["dpCfg"]["mdesign_no"]?>">
<?php if($TPL_VAR["dpCfg"]["title"]){?>
	<div class="list_title">
		<div class="bullet"></div>
		<div class="title"><?php echo $TPL_VAR["dpCfg"]["title"]?></div>
	</div>
<?php }?>
	<div class="list_content_wrap">
		<div class="swipe_gs" id="swipe_gs-<?php echo $TPL_VAR["dpCfg"]["mdesign_no"]?>">
			<div>
<?php if((is_array($TPL_R1=range( 0,$TPL_VAR["dpCfg"]["page_cnt"]- 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_I1=-1;foreach($TPL_R1 as $TPL_V1){$TPL_I1++;?>
				<div class="list_content<?php if($TPL_I1!= 0){?> hidden<?php }?>" id="goodsscroll-<?php echo $TPL_VAR["dpCfg"]["mdesign_no"]?>-<?php echo $TPL_I1+ 1?>">
					<div class="list_content_border"></div>
<?php if((is_array($TPL_R2=$TPL_VAR["dpCfg"]["goods_data"][$TPL_I1])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>
					<div class="list_item" style="width:<?php echo $TPL_VAR["dpCfg"]["item_width"]?>%;<?php if($TPL_I2%$TPL_VAR["dpCfg"]["disp_cnt"]== 0){?>clear:both;<?php }?>" onClick="<?php if($TPL_VAR["dpCfg"]["display_type"]=='3'){?>goCate('<?php echo $TPL_V2["goodsno"]?>')<?php }else{?>goGoods('<?php echo $TPL_V2["goodsno"]?>')<?php }?>">
<?php if($TPL_V2["goods_img"]!=''){?>
						<div class="item_img">
<?php if($TPL_V2["coupon_discount"]){?><div class="item_coupon_img"></div><?php }?>
							<img src="<?php echo $TPL_V2["goods_img"]?>" alt="<?php echo $TPL_V2["goodsnm"]?>" <?php echo $TPL_V2["css_selector"]?> />
<?php if($TPL_V2["tts_url"]){?>
							<div class="goods-speach-description" style="display:none;">
								<span class="speach-description-play" data-src="<?php echo $TPL_V2["tts_url"]?>">재생</span>
								<span class="speach-description-timer"></span>
							</div>
<?php }?>
						</div>
<?php }else{?>
						<div class="item_img"><?php if($TPL_V2["coupon_discount"]){?><div class="item_coupon_img"></div><?php }?></div>
<?php }?>
						<div class="item_name" style="width:<?php echo $TPL_VAR["dpCfg"]["item_name_width"]?>px;"><?php echo $TPL_V2["goodsnm"]?></div>
<?php if($TPL_V2["goods_strprice"]==''&&$TPL_V2["goodsDiscountPrice"]){?>
<?php if($TPL_V2["oriPrice"]){?>
							<div class="item_consumer" style="display:none;"><strike><?php echo number_format($TPL_V2["oriPrice"])?> 원</strike>↓</div>
<?php }?>
							<div class="item_price"><b><?php echo $TPL_V2["goodsDiscountPrice"]?></b></div>
<?php }else{?>
<?php if($TPL_V2["goods_strprice"]==''&&$TPL_V2["consumer"]!=''){?>
							<div class="item_consumer" style="display:none;"><strike><?php echo number_format($TPL_V2["consumer"])?> 원</strike>↓</div>
<?php }?>
							<div class="item_price"><b><?php echo $TPL_V2["goods_price"]?></b></div>
<?php }?>
<?php if($TPL_V2["special_discount"]){?>
						<div class="item_discount" style="display:none;"><?php echo $TPL_V2["special_discount"]?> 할인</div>
<?php }?>
<?php if($TPL_V2["coupon"]){?>
						<div class="item_coupon_price" style="display:none;"><?php echo number_format($TPL_V2["coupon"])?> 원 <div class="item_coupon_icon"></div></div>
<?php }?>
					</div>
<?php }}?>
					<div class="list_content_border"></div>
				</div>
<?php }}?>
			</div>
		</div>
	</div>
	<div class="list_page">
		<div class="list_page_wrap">
			<div class="list_page_left" onclick="javascript:scroll_btn('swipe_gs-<?php echo $TPL_VAR["dpCfg"]["mdesign_no"]?>', 'left');"></div>
			<div class="list_page_num"><span id="swipe_gs-<?php echo $TPL_VAR["dpCfg"]["mdesign_no"]?>-page" class="n_page">1</span> / <span id="goodsscroll-<?php echo $TPL_VAR["dpCfg"]["mdesign_no"]?>-tpage"><?php echo $TPL_VAR["dpCfg"]["page_cnt"]?></span></div>
			<div class="list_page_right" onclick="javascript:scroll_btn('swipe_gs-<?php echo $TPL_VAR["dpCfg"]["mdesign_no"]?>', 'right');"></div>
		</div>
	</div>
	<div class="list_margin"></div>
</div>