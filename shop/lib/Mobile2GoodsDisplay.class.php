<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Copyright (c) 2013 GODO Co. Ltd
 * All right reserved.
 *
 * This software is the confidential and proprietary information of GODO Co., Ltd.
 * You shall not disclose such Confidential Information and shall use it only in accordance
 * with the terms of the license agreement  you entered into with GODO Co., Ltd
 *
 * Revision History
 * Author            Date              Description
 * ---------------   --------------    ------------------
 * workingparksee    2013.09.25        First Draft.
 */

/**
 * Description of Mobile2GoodsDisplay
 *
 * @author Mobile2GoodsDisplay.class.php workingparksee <parksee@godo.co.kr>
 * @version 1.0
 * @date 2013-09-25, 2013-09-25
 */
class Mobile2GoodsDisplay
{

	private $_dbo, $temporaryDesignData = array(), $_mobileShopConfig;

	function __construct()
	{
		@include dirname(__FILE__).'/../conf/config.mobileShop.php';
		$this->_dbo = Core::loader('GODO_DB');
		$this->_mobileShopConfig = $cfgMobileShop;
	}

	function initializeMainDisplay()
	{
		$designData = $this->_dbo->_select('SELECT * FROM gd_mobile_design WHERE page_type="main" ORDER BY mdesign_no ASC');

		if(empty($designData)) {

			$design1 = Array();

			$design1['page_type'] = 'main';
			$design1['chk'] = 'on';
			$design1['title'] = '신상품';
			$design1['line_cnt'] = '';
			$design1['disp_cnt'] = '';
			$design1['banner_width'] = '';
			$design1['banner_height'] = '';
			$design1['tpl'] = '';
			$design1['tpl_opt'] = '';

			$insertDesignQuery = $this->_dbo->_query_print('INSERT INTO gd_mobile_design SET [cv]', $design1);
			$this->_dbo->query($insertDesignQuery);
			$design1['mdesign_no'] = $this->_dbo->_last_insert_id();
			unset($insertDesignQuery);

			$designData[] = $design1;

			$design2 = Array();

			$design2['page_type'] = 'main';
			$design2['chk'] = 'on';
			$design2['title'] = '인기상품';
			$design2['line_cnt'] = '';
			$design2['disp_cnt'] = '';
			$design2['banner_width'] = '';
			$design2['banner_height'] = '';
			$design2['tpl'] = '';
			$design2['tpl_opt'] = '';

			$insertDesignQuery = $this->_dbo->_query_print('INSERT INTO gd_mobile_design SET [cv]', $design2);
			$this->_dbo->query($insertDesignQuery);
			$design2['mdesign_no'] = $this->_dbo->_last_insert_id();
			unset($insertDesignQuery);

			$designData[] = $design2;
		}
		return $designData;
	}

	function getDesignData($displayNo)
	{
		if (!isset($this->temporaryDesignData[$displayNo]) || !is_array($this->temporaryDesignData[$displayNo])) {
			$designQuery = $this->_dbo->_query_print('SELECT * FROM gd_mobile_design WHERE page_type="main" AND mdesign_no=[i] LIMIT 1', $displayNo);
			$this->temporaryDesignData[$displayNo] = $this->_dbo->fetch($designQuery, true);
		}
		return $this->temporaryDesignData[$displayNo];
	}

	function isInitStatus()
	{
		$mobileDesign = $this->_dbo->fetch('SELECT COUNT(mdesign_no) AS cnt FROM gd_mobile_design WHERE page_type="main"', true);
		if ($mobileDesign['cnt'] == 0) {
			return true;
		}
		else if ($mobileDesign['cnt'] == 2) {
			if ($this->designModified(1) || $this->designModified(2)) return false;
			else return true;
		}
		else {
			return false;
		}
	}


	function isPCDIsplay()
	{
		return ($this->_mobileShopConfig['vtype_main'] === 'pc');
	}

	function isMobileDisplay()
	{
		return ($this->_mobileShopConfig['vtype_main'] === 'mobile');
	}

	function displayTypeIsSet()
	{
		return isset($this->_mobileShopConfig['vtype_main']);
	}

	function saveMainDisplayType($displayType)
	{
		include dirname(__FILE__).'/lib/qfile.class.php';
		@include dirname(__FILE__).'/../conf/config.mobileShop.php';

		if (!$cfgMobileShop) return false;

		if ($displayType === 'pc') $cfgMobileShop['vtype_main'] = 'pc';
		else $cfgMobileShop['vtype_main'] = 'mobile';

		$qfile = new qfile();
		$qfile->open(dirname(__FILE__).'/../conf/config.mobileShop.php');
		$qfile->write("<? \n");
		$qfile->write("\$cfgMobileShop = array( \n");
		foreach ($cfgMobileShop as $key => $value) {
			$qfile->write("'$key' => '$value', \n");
		}
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		@include dirname(__FILE__).'/../conf/config.mobileShop.php';
		$this->_mobileShopConfig = $cfgMobileShop;
	}

	function designModified($displayNo)
	{
		$designData = $this->getDesignData($displayNo);
		if ($designData['line_cnt'] == '0' && $designData['disp_cnt'] == '0' && $designData['display_type'] == '' && $designData['tpl'] == '') {
			return false;
		}
		else {
			return true;
		}
	}

	function getDisplaySequence($mdesignNo)
	{
		$this->_dbo->query('SET @SEQUENCE =  0');
		$result = $this->_dbo->fetch('
			SELECT seqmap.sequence FROM (
				SELECT @SEQUENCE := @SEQUENCE + 1 AS sequence, mdesign_no
				FROM gd_mobile_design
				WHERE page_type="main"
				ORDER BY mdesign_no ASC
			) AS seqmap WHERE seqmap.mdesign_no = '.$mdesignNo.' LIMIT 1
		', true);
		return $result['sequence'];
	}

	function getPCDesignData($index)
	{
		$config = Core::loader('config');
		$cfg = $config->load('config');
		@include dirname(__FILE__).'/../conf/design.main.php';
		@include dirname(__FILE__).'/../conf/design_main.'.$cfg['tplSkin'].'.php';
		$cfg_step_key = array_keys($cfg_step);
		return $cfg_step[$cfg_step_key[$index]];
	}

	function makeDefaultMainDisplayDesign($mdesignNo, &$design)
	{
		$displaySequence = $this->getDisplaySequence($mdesignNo);
		$pcDesignData = $this->getPCDesignData($displaySequence-1);
		if ($design['chk'] == 'on') {
			$design['tpl'] = 'tpl_03';
			$design['title'] = $pcDesignData['title'];
			$design['display_type'] = '1';
			$design['line_cnt'] = '2';
			$design['disp_cnt'] = '3';
			return true;
		}
		else {
			return false;
		}
	}

	function getPCMainDisplayGoods($mdesignNo, $isAdmin = false)
	{
		$config = Core::loader('config');
		$cfg = $config->load('config');
		$mainAutoSort = Core::loader('mainAutoSort');

		// 상품할인
		$goodsDiscountModel = Clib_Application::getModelClass('goods_discount');

		// 메인상품진열 설정(통합 설정/스킨별 설정)에 따른 환경정보 Read 
		if (is_file( dirname(__FILE__).'/../conf/design_basic_'.$cfg['tplSkin'].'.php')) {
			@include dirname(__FILE__).'/../conf/design_basic_'.$cfg['tplSkin'].'.php';
		}
		@include dirname(__FILE__).'/../conf/design.main.php';
		@include dirname(__FILE__).'/../conf/design_main.'.$cfg['tplSkin'].'.php';

		if (is_file( dirname(__FILE__).'/../conf/config.soldout.php'))
			include dirname(__FILE__).'/../conf/config.soldout.php';

		@include dirname(__FILE__).'/../conf/config.display.php';

		$cfg_step_key = array_keys($cfg_step);
		$displaySequence = $this->getDisplaySequence($mdesignNo);
		$cfg_step_arr = $cfg_step[$cfg_step_key[$displaySequence-1]];

		if (!$cfg_step_arr['sort_type'] || $cfg_step_arr['sort_type'] == 1) {
			if ($cfg['shopMainGoodsConf'] == 'E') {
				$where = ' AND gd.tplSkin = "'.$cfg['tplSkin'].'" ';
			}
			else {
				$where = ' AND (gd.tplSkin = "" OR gd.tplSkin IS NULL) ';
			}

			$orderby = "order by gd.sort ASC";
		} else {
			$sortNum = $mainAutoSort->use_table.".sort".$cfg_step_arr['sort_type']."_".$cfg_step_arr['select_date'];
			$orderby = 'order by '.$sortNum.' ASC';
		}

		// 품절 상품 제외
		if ($cfg_soldout['exclude_main']) {
			if (!$cfg_step_arr['sort_type'] || $cfg_step_arr['sort_type'] == 1) {
				$where .= " AND !( g.runout = 1 OR (g.usestock = 'o' AND g.usestock IS NOT NULL AND g.totstock < 1) ) ";
			} else {
				$where .= " AND !( gd_goods.runout = 1 OR (gd_goods.usestock = 'o' AND gd_goods.usestock IS NOT NULL AND gd_goods.totstock < 1) ) ";
			}
		}

		// 제외시키지 않는 다면, 맨 뒤로 보낼지를 결정
		else if ($cfg_soldout['back_main']) {
			if (!$cfg_step_arr['sort_type'] || $cfg_step_arr['sort_type'] == 1) {
				$orderby = "order by `soldout` ASC, gd.sort";
				$_add_field = ",IF (g.runout = 1 , 1, IF (g.usestock = 'o' AND g.totstock = 0, 1, 0)) as `soldout`";
			} else {
				$orderby = 'order by `soldout` ASC, '.$sortNum;
				$_add_field = ",IF (gd_goods.runout = 1 , 1, IF (gd_goods.usestock = 'o' AND gd_goods.totstock = 0, 1, 0)) as `soldout`";
			}
		}
		$displayGoods = array();

		if (!$cfg_step_arr['sort_type'] || $cfg_step_arr['sort_type'] == 1) {
			$goodsDisplayQuery = $this->_dbo->_query_print("
				SELECT gd.goodsno, g.goodsnm, g.img_mobile, g.img_l, g.img_m, g.use_mobile_img, g.img_w, g.strprice, go.price, go.consumer, g.runout, g.usestock, g.totstock, g.use_only_adult, g.speach_description_useyn, g.speach_description, g.use_goods_discount $_add_field
				FROM gd_goods_display AS gd
				LEFT JOIN gd_goods AS g ON gd.goodsno=g.goodsno
				LEFT JOIN gd_goods_option AS go ON g.goodsno=go.goodsno AND go_is_deleted <> '1'
				WHERE gd.mode=[s] ".($isAdmin ? "" : "AND g.open_mobile")." AND go.link=1 ".$where."
				$orderby
			", strval($cfg_step_key[$displaySequence-1]));
		} else {
			list($add_table, $add_where, $add_order) = $mainAutoSort->getSortTerms($cfg_step_arr['categoods'], $cfg_step_arr['price'], $cfg_step_arr['stock_type'], $cfg_step_arr['stock_amount'], $cfg_step_arr['regdt'], $sortNum);
 
			$goodsDisplayQuery = $this->_dbo->_query_print("
				SELECT
					".$mainAutoSort->use_table.".goodsno, gd_goods.goodsnm, gd_goods.img_mobile, gd_goods.img_l, gd_goods.img_m, gd_goods.use_mobile_img, gd_goods.img_w, gd_goods.strprice, gd_goods_option.price, gd_goods_option.consumer, gd_goods.runout, gd_goods.usestock, gd_goods.totstock, gd_goods.use_only_adult, gd_goods.speach_description_useyn, gd_goods.speach_description, gd_goods.use_goods_discount $_add_field
				FROM
					".$mainAutoSort->use_table."
					{$add_table}
				WHERE
					gd_goods_option.link=1
					".($isAdmin ? "" : "AND gd_goods.open_mobile")."
					{$where}
					{$add_where}
				GROUP BY ".$mainAutoSort->use_table.".goodsno $orderby
				LIMIT ".$mainAutoSort->sort_limit."
			", strval($cfg_step_key[$displaySequence-1]));
		}

		//DB Cache 사용 141030
		$dbCache = Core::loader('dbcache')->setLocation('mobile_display');

		if (!$displayGoods = $dbCache->getCache($goodsDisplayQuery)) {
			$res = $this->_dbo->query($goodsDisplayQuery);
			while ($row = $this->_dbo->fetch($res, true)) {
				$goodsImagePrefix = '/shop/data/goods/';
				if($row['use_mobile_img'] == '1'){
					$zoomImage = array_notnull(explode('|', $row['img_w']));
				} else {
					$zoomImage = array_notnull(explode('|', $row['img_l']));
				}
				if (count($zoomImage)) {
					if (preg_match('/^http:\/\//i', $zoomImage[0])) $goodsImg = $zoomImage[0];
					else $goodsImg = $goodsImagePrefix.$zoomImage[0];
				}
				else {
					$detailImage = array_notnull(explode('|', $row['img_m']));
					if (preg_match('/^http:\/\//i', $detailImage[0])) $goodsImg = $detailImage[0];
					else $goodsImg = $goodsImagePrefix.$detailImage[0];
				}
				if($row['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
					$goodsImg = 'http://' . $_SERVER['HTTP_HOST'] . $cfg['rootDir'] . "/data/skin/" . $cfg['tplSkin'] . '/img/common/19.gif';
				}
				if (strlen(trim($row['strprice'])) > 0) {
					$goodsPrice = $row['strprice'];
				}
				else {
					$goodsPrice = number_format($row['price']).' 원';
				}
				// 소비자가
				if ($row['consumer'] > 0) {
					$consumer = $row['consumer'];
				}
				else if ($row['consumer'] == '' || $row['consumer'] == 0) {
					$consumer = '';
				}

				if ($row['speach_description_useyn'] === 'y' && strlen($row['speach_description']) > 0) {
					$tts_url = Core::loader('TextToSpeach')->getURL($row['speach_description']);
				}
				else {
					$tts_url = '';
				}

				// 상품할인
				$special_discount = "";
				if($row['use_goods_discount']){
					$special_discount = $goodsDiscountModel->getDiscountUnit($row, Clib_Application::session()->getMemberLevel());
				}

				// 상품할인 가격 표시
				$oriPrice = '';
				$goodsDiscountPrice = '';
				if ($displayCfg['displayType'] === 'discount') {
					$goodsDiscount = '';
					if ($row['use_goods_discount'] === '1') {
						$goodsDiscount = $goodsDiscountModel->getDiscountAmountSearch($row);
					}
					if ($goodsDiscount) {
						$oriPrice = $row['price'];
						$goodsDiscountPrice = number_format($row['price'] - $goodsDiscount).' 원';
					}
					else {
						$oriPrice = '0';
						$goodsDiscountPrice = number_format($row['price']).' 원';
					}
				}

				// 품절상품
				$css_selector = "";
				if ( $row['runout'] > 0 || $row['usestock'] && $row['totstock'] < 1) {
					if($cfg_soldout['mobile_display'] === 'overlay')
						$css_selector = 'class="el-goods-soldout-image"';
				}

				// 즉석할인쿠폰 유효성 검사 (pc or mobile)
				list($row['coupon'], $row['coupon_emoney']) = getCouponInfoMobile($row['goodsno'], $row['price']);

				// 쿠폰 이미지 경로
				$coupon_discount = null;
				if($row['coupon'] > 0 || $row['coupon_emoney'] > 0){
					$coupon_discount = true;
				}

				$displayGoods[] = array(
					'goods_img' => $goodsImg,
					'goods_price' => $goodsPrice,
					'consumer' => $consumer,
					'goodsnm' => $row['goodsnm'],
					'goodsno' => $row['goodsno'],
					'tts_url' => $tts_url,
					'special_discount' => $special_discount,
					'coupon_discount' => $coupon_discount,
					'coupon' => $row['coupon'],
					'css_selector' => $css_selector,
				);
			}
			if ($dbCache) { $dbCache->setCache($goodsDisplayQuery, $displayGoods); }
		}
		return $displayGoods;
	}

	function getMobileMainImg($cfg, $row, $is_main = true) {
		$img_path = '';

		if($cfg['rootDir']) $img_path = $cfg['rootDir'].'/data/goods/';
		else $img_path = '/shop/data/goods/';
		
		if ($is_main) {
			// 메인의 경우 메인이미지 맵핑
			$mobile_img = $row['img_w'];
			$mobile_pc_imgs = explode('|',$row[$row['img_pc_w']]);
			$mobile_pc_img = $mobile_pc_imgs[0];
			$default_img = $row['img_i'];			
		} else {
			// 분류/이벤트의 경우 리스트이미지 맵핑
			$mobile_img = $row['img_x'];
			$mobile_pc_imgs = explode('|',$row[$row['img_pc_x']]);
			$mobile_pc_img = $mobile_pc_imgs[0];
			$default_img = $row['img_s'];
		}

		// 상품수정에서 모바일샵 전용 이미지 사용여부에 따른 이미지 URL 설정
		if (preg_match("/^http/i", $mobile_img)) {
			$goods_img = $mobile_img;
		} else if (preg_match("/^http/i", $default_img)) {
			$goods_img = $default_img;
		} else {
			if ($row['use_mobile_img'] === '1') {
				$goods_img = $img_path.$mobile_img;
			} else if ($row['use_mobile_img'] === '0') {
				$goods_img = $img_path.$mobile_pc_img;
			} else {
				$goods_img = $img_path.$default_img;
			}
			if (!is_file($_SERVER['DOCUMENT_ROOT'] . $goods_img)) {
				$goods_img = 'http://' . $_SERVER['HTTP_HOST'] . $cfg['rootDir'] . "/data/skin/" . $cfg['tplSkin'] . '/img/common/noimg_100.gif';
			}
		}

		if($row['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
			$goods_img = 'http://' . $_SERVER['HTTP_HOST'] . $cfg['rootDir'] . "/data/skin/" . $cfg['tplSkin'] . '/img/common/19.gif';
		}

		return $goods_img;
	}
	
	function getMobileMainDisplayGoods($req_arr) {
		@include dirname(__FILE__). "/../../shop/conf/config.soldout.php";

		$config = Core::loader('config');
		$cfg = $config->load('config');
		$mainAutoSort = Core::loader('mainAutoSort');

		if (is_file(dirname(__FILE__). "/../../shop/conf/config.soldout.php"))
			include dirname(__FILE__). "/../../shop/conf/config.soldout.php";

		@include dirname(__FILE__).'/../conf/config.display.php';

		$goodsDiscountModel = Clib_Application::getModelClass('goods_discount');

		//정렬
		$_add_table = $orderby2 = "";
		if (!$req_arr['sort_type'] || $req_arr['sort_type'] == 1) {
			if($req_arr['display_type'] == '2'){
				$_add_field = ", md.sort as md_sort, IF (length(md.category) = 3 AND c.sort_type = 'MANUAL' , gl.sort1, IF (length(md.category) = 6 AND c.sort_type = 'MANUAL' , gl.sort2, IF (length(md.category) = 9 AND c.sort_type = 'MANUAL' , gl.sort3 , IF (length(md.category) = 12 AND c.sort_type = 'MANUAL' , gl.sort4 , gl.sort)))) as `dis_sort`";
				$_add_table = "LEFT JOIN ".GD_CATEGORY." c ON md.category = c.category";
				$orderby = "ORDER BY md.sort, dis_sort";
				$orderby2 = "ORDER BY gd_mobile.md_sort, gd_mobile.dis_sort";
			}
			else
				$orderby = "order by md.sort ASC";
		} else {
			$sortNum = $mainAutoSort->use_table.".sort".$req_arr['sort_type']."_".$req_arr['select_date'];
			$orderby = "ORDER BY ".$sortNum;
		}

		// 품절 상품 제외
		if ($cfg_soldout['exclude_main']) {
			if (!$req_arr['sort_type'] || $req_arr['sort_type'] == 1) {
				$where = " AND !( g.runout = 1 OR (g.usestock = 'o' AND g.usestock IS NOT NULL AND g.totstock < 1) ) ";
			} else {
				$where = " AND !( gd_goods.runout = 1 OR (gd_goods.usestock = 'o' AND gd_goods.usestock IS NOT NULL AND gd_goods.totstock < 1) ) ";
			}
		}
		// 제외시키지 않는 다면, 맨 뒤로 보낼지를 결정
		else if ($cfg_soldout['back_main']) {
			if (!$req_arr['sort_type'] || $req_arr['sort_type'] == 1) {
				$orderby = "order by `soldout` ASC, md.sort";
				$_add_field = ",IF (g.runout = 1 , 1, IF (g.usestock = 'o' AND g.totstock = 0, 1, 0)) as `soldout`";
			} else {
				$orderby = "order by `soldout` ASC, ".$sortNum;
				$_add_field = ",IF (gd_goods.runout = 1 , 1, IF (gd_goods.usestock = 'o' AND gd_goods.totstock = 0, 1, 0)) as `soldout`";
			}
		}

		//$req_arr['mdesign_no'] = intval($req_arr['mdesign_no']);
		//$req_arr['display_type'] = intval($req_arr['display_type']);

		if (!$req_arr['sort_type'] || $req_arr['sort_type'] == 1) {
			switch ($req_arr['display_type']) {
				case '1' :
					$tmp_query = "
						SELECT
							md.goodsno, g.goodsnm, g.img_i, g.img_s, g.img_m, g.img_l, g.use_mobile_img, g.img_w, g.img_pc_w, g.strprice, go.price, go.consumer, g.runout, g.usestock, g.totstock, g.use_only_adult, g.speach_description_useyn, g.speach_description, g.use_goods_discount $_add_field
						FROM
							".GD_MOBILE_DISPLAY." md
							LEFT JOIN ".GD_GOODS." g ON md.goodsno = g.goodsno
							LEFT JOIN ".GD_GOODS_OPTION." go ON md.goodsno = go.goodsno AND link and go_is_deleted <> '1' and go_is_display = '1'
						WHERE
							md.mdesign_no=[s] AND md.display_type=[s]
							and g.open_mobile
							$where
						$orderby
						";

					$display_query = $this->_dbo->_query_print($tmp_query, $req_arr['mdesign_no'], $req_arr['display_type']);
					$tmp_display = $this->_dbo->_select($display_query);

					//DB Cache 사용 141030
					$dbCache = Core::loader('dbcache')->setLocation('mobile_display');

					if (!$res_display = $dbCache->getCache($display_query)) {
						$res_display = array();

						if(is_array($tmp_display) && !empty($tmp_display)) {
							foreach($tmp_display as $row_display) {
								$tmp_arr = array();

								$tmp_arr['goodsno'] = $row_display['goodsno'];
								$tmp_arr['goodsnm'] = strip_tags($row_display['goodsnm']);
								$tmp_arr['goods_img'] = $this->getMobileMainImg($cfg, $row_display);

								if (strlen(trim($row_display['strprice'])) > 0) {
									$tmp_arr['goods_price'] = $row_display['strprice'];
								}
								else {
									$tmp_arr['goods_price'] = number_format($row_display['price']).' 원';
								}

								// 소비자가
								if ($row_display['consumer'] > 0) {
									$tmp_arr['consumer'] = $row_display['consumer'];
								}
								else if ($row_display['consumer'] == '' || $row_display['consumer'] == 0) {
									$tmp_arr['consumer'] = '';
								}

								if ($row_display['speach_description_useyn'] === 'y' && strlen($row_display['speach_description']) > 0) {
									$tmp_arr['tts_url'] = Core::loader('TextToSpeach')->getURL($row_display['speach_description']);
								}
								else {
									$tmp_arr['tts_url'] = '';
								}

								// 상품할인
								if($row_display['use_goods_discount']){
									$tmp_arr['special_discount'] = $goodsDiscountModel->getDiscountUnit($row_display, Clib_Application::session()->getMemberLevel());
								}

								// 상품할인 가격 표시
								if ($displayCfg['displayType'] === 'discount') {
									$goodsDiscount = '';
									if ($row_display['use_goods_discount'] === '1') {
										$goodsDiscount = $goodsDiscountModel->getDiscountAmountSearch($row_display);
									}
									if ($goodsDiscount) {
										$tmp_arr['oriPrice'] = $row_display['price'];
										$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price'] - $goodsDiscount).' 원';
									}
									else {
										$tmp_arr['oriPrice'] = '0';
										$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price']).' 원';
									}
								}

								// 품절상품
								if ( $row_display['runout'] > 0 || $row_display['usestock'] && $row_display['totstock'] < 1) {
									if($cfg_soldout['mobile_display'] === 'overlay')
										$tmp_arr['css_selector'] = 'class="el-goods-soldout-image"';
								}

								// 즉석할인쿠폰 유효성 검사 (pc or mobile)
								list($tmp_arr['coupon'], $tmp_arr['coupon_emoney']) = getCouponInfoMobile($row_display['goodsno'], $row_display['price']);

								// 쿠폰할인여부
								if($tmp_arr['coupon'] > 0 || $tmp_arr['coupon_emoney'] > 0){
									$tmp_arr['coupon_discount'] = true;
								}

								$res_display[] = $tmp_arr;
							}
							if ($dbCache) { $dbCache->setCache($display_query, $res_display); }
						}
					}

					break;

				case '2' :
					$tmp_query = "
						SELECT * FROM (
							SELECT
								md.category, g.goodsno, g.goodsnm, g.img_i, g.img_s, g.img_m, g.img_l, g.use_mobile_img, g.img_w, g.img_pc_w, g.strprice, go.price, go.consumer, g.runout, g.usestock, g.totstock, g.use_only_adult, g.speach_description_useyn, g.speach_description, g.use_goods_discount $_add_field
							FROM
								".GD_MOBILE_DISPLAY." md
								LEFT JOIN ".GD_GOODS_LINK." gl ON gl.category = md.category
								LEFT JOIN ".GD_GOODS." g ON gl.goodsno = g.goodsno
								LEFT JOIN ".GD_GOODS_OPTION." go ON g.goodsno = go.goodsno AND link and go_is_deleted <> '1' and go_is_display = '1'
								$_add_table
							WHERE
								md.mdesign_no=[s] AND md.display_type=[s]
								and g.open_mobile
								$where
							$orderby
						) gd_mobile
						GROUP BY gd_mobile.goodsno
						$orderby2
						";

					$display_query = $this->_dbo->_query_print($tmp_query, $req_arr['mdesign_no'], $req_arr['display_type']);
					$tmp_display = $this->_dbo->_select($display_query);
					$res_display = array();

					if(is_array($tmp_display) && !empty($tmp_display)) {
						foreach($tmp_display as $row_display) {
							$tmp_arr = array();

							$tmp_arr['goodsno'] = $row_display['goodsno'];
							$tmp_arr['goodsnm'] = strip_tags($row_display['goodsnm']);
							$tmp_arr['goods_img'] = $this->getMobileMainImg($cfg, $row_display);

							if (strlen(trim($row_display['strprice'])) > 0) {
								$tmp_arr['goods_price'] = $row_display['strprice'];
							}
							else {
								$tmp_arr['goods_price'] = number_format($row_display['price']).' 원';
							}

							// 소비자가
							if ($row_display['consumer'] > 0) {
								$tmp_arr['consumer'] = $row_display['consumer'];
							}
							else if ($row_display['consumer'] == '' || $row_display['consumer'] == 0) {
									$tmp_arr['consumer'] = '';
							}

							if ($row_display['speach_description_useyn'] === 'y' && strlen($row_display['speach_description']) > 0) {
								$tmp_arr['tts_url'] = Core::loader('TextToSpeach')->getURL($row_display['speach_description']);
							}
							else {
								$tmp_arr['tts_url'] = '';
							}

							// 상품할인
							if($row_display['use_goods_discount']){
								$tmp_arr['special_discount'] = $goodsDiscountModel->getDiscountUnit($row_display, Clib_Application::session()->getMemberLevel());
							}

							// 상품할인 가격 표시
							if ($displayCfg['displayType'] === 'discount') {
								$goodsDiscount = '';
								if ($row_display['use_goods_discount'] === '1') {
									$goodsDiscount = $goodsDiscountModel->getDiscountAmountSearch($row_display);
								}
								if ($goodsDiscount) {
									$tmp_arr['oriPrice'] = $row_display['price'];
									$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price'] - $goodsDiscount).' 원';
								}
								else {
									$tmp_arr['oriPrice'] = '0';
									$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price']).' 원';
								}
							}

							// 품절상품
							if ( $row_display['runout'] > 0 || $row_display['usestock'] && $row_display['totstock'] < 1) {
								if($cfg_soldout['mobile_display'] === 'overlay')
									$tmp_arr['css_selector'] = 'class="el-goods-soldout-image"';
							}

							// 즉석할인쿠폰 유효성 검사 (pc or mobile)
							list($tmp_arr['coupon'], $tmp_arr['coupon_emoney']) = getCouponInfoMobile($row_display['goodsno'], $row_display['price']);

							// 쿠폰할인여부
							if($tmp_arr['coupon'] > 0 || $tmp_arr['coupon_emoney'] > 0){
								$tmp_arr['coupon_discount'] = true;
							}

							$res_display[] = $tmp_arr;
						}
					}
					break;
				case '3' :
					$tmp_query = '
						SELECT
							md.category, c.catnm, md.temp2
						FROM
							'.GD_MOBILE_DISPLAY.' md
							LEFT JOIN '.GD_CATEGORY.' c ON md.category = c.category
						WHERE
							md.mdesign_no=[s] AND md.display_type=[s]
						ORDER BY
							md.sort ASC
						';
						$display_query = $this->_dbo->_query_print($tmp_query, $req_arr['mdesign_no'], $req_arr['display_type']);

						$tmp_display = $this->_dbo->_select($display_query);

						$img_path = '';

						if($cfg['rootDir']) {
							$img_path = $cfg['rootDir'].'/data/m/upload_img/';
						}
						else {
							$img_path = '/shop/data/m/upload_img/';
						}

						$res_display = array();

						if(is_array($tmp_display) && !empty($tmp_display)) {
							foreach($tmp_display as $row_display) {
								$tmp_arr = array();

								$tmp_arr['goodsno'] = $row_display['category'];
								$tmp_arr['goodsnm'] = $row_display['catnm'];
								$tmp_arr['goods_img'] = $img_path.$row_display['temp2'];
								$tmp_arr['goods_price'] = '';

								if (!is_file($_SERVER['DOCUMENT_ROOT'] . $tmp_arr['goods_img'])) {
									$tmp_arr['goods_img'] = $cfg['rootDir'] . "/data/skin/" . $cfg['tplSkin'] . '/img/common/noimg_100.gif';
								}

								if(is_file('../'.$tmp_arr['goods_img']) && $tmp_arr['goodsnm']) {
									$res_display[] = $tmp_arr;
								}
							}
						}

					break;

				case '5' :
					$tab_query = $this->_dbo->_query_print('SELECT tpl_opt FROM '.GD_MOBILE_DESIGN.' WHERE mdesign_no=[s]', $req_arr['mdesign_no']);
					$tab_res = $this->_dbo->_select($tab_query);
					$tab_res = $tab_res[0];

					$json = new Services_JSON(16);
					$tab_info = $json->decode($tab_res['tpl_opt']);

					$res_display = array();

					if(is_array($tab_info['tab_name']) && !empty($tab_info['tab_name'])) {
						foreach ($tab_info['tab_name'] as $key => $val) {
							$tmp_query = "
								SELECT
									md.goodsno, g.goodsnm, g.img_i, g.img_s, g.img_m, g.img_l, g.use_mobile_img, g.img_w, g.img_pc_w, g.strprice, go.price, go.consumer, g.runout, g.usestock, g.totstock, g.speach_description_useyn, g.speach_description, g.use_goods_discount $_add_field
								FROM
									".GD_MOBILE_DISPLAY." md
									LEFT JOIN ".GD_GOODS." g ON md.goodsno = g.goodsno
									LEFT JOIN ".GD_GOODS_OPTION." go ON md.goodsno = go.goodsno AND link and go_is_deleted <> '1' and go_is_display = '1'
								WHERE
									md.mdesign_no=[s] AND md.display_type=[s] AND md.tab_no=[s]
									and g.open_mobile
									$where
								$orderby
								";

							$display_query = $this->_dbo->_query_print($tmp_query, $req_arr['mdesign_no'], $req_arr['display_type'], $key);
							$tmp_display = $this->_dbo->_select($display_query);

							$tmp_res = array();
							if(is_array($tmp_display) && !empty($tmp_display)) {
								foreach($tmp_display as $row_display) {
									$tmp_arr = array();

									$tmp_arr['goodsno'] = $row_display['goodsno'];
									$tmp_arr['goodsnm'] = strip_tags($row_display['goodsnm']);
									$tmp_arr['goods_img'] = $this->getMobileMainImg($cfg, $row_display);

									if (strlen(trim($row_display['strprice'])) > 0) {
										$tmp_arr['goods_price'] = $row_display['strprice'];
									}
									else {
										$tmp_arr['goods_price'] = number_format($row_display['price']).' 원';
									}

									// 소비자가
									if ($row_display['consumer'] > 0) {
										$tmp_arr['consumer'] = $row_display['consumer'];
									}
									else if ($row_display['consumer'] == '' || $row_display['consumer'] == 0) {
										$tmp_arr['consumer'] = '';
									}

									if ($row_display['speach_description_useyn'] === 'y' && strlen($row_display['speach_description']) > 0) {
										$tmp_arr['tts_url'] = Core::loader('TextToSpeach')->getURL($row_display['speach_description']);
									}
									else {
										$tmp_arr['tts_url'] = '';
									}

									// 상품할인
									if($row_display['use_goods_discount']){
										$tmp_arr['special_discount'] = $goodsDiscountModel->getDiscountUnit($row_display, Clib_Application::session()->getMemberLevel());
									}

									// 상품할인 가격 표시
									if ($displayCfg['displayType'] === 'discount') {
										$goodsDiscount = '';
										if ($row_display['use_goods_discount'] === '1') {
											$goodsDiscount = $goodsDiscountModel->getDiscountAmountSearch($row_display);
										}
										if ($goodsDiscount) {
											$tmp_arr['oriPrice'] = $row_display['price'];
											$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price'] - $goodsDiscount).' 원';
										}
										else {
											$tmp_arr['oriPrice'] = '0';
											$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price']).' 원';
										}
									}

									// 품절상품
									if ( $row_display['runout'] > 0 || $row_display['usestock'] && $row_display['totstock'] < 1) {
										if($cfg_soldout['mobile_display'] === 'overlay')
											$tmp_arr['css_selector'] = 'class="el-goods-soldout-image"';
									}

									// 즉석할인쿠폰 유효성 검사 (pc or mobile)
									list($tmp_arr['coupon'], $tmp_arr['coupon_emoney']) = getCouponInfoMobile($row_display['goodsno'], $row_display['price']);

									// 쿠폰할인여부
									if($tmp_arr['coupon'] > 0 || $tmp_arr['coupon_emoney'] > 0){
										$tmp_arr['coupon_discount'] = true;
									}

									$tmp_res[] = $tmp_arr;
								}
							}
							$res_display[] = $tmp_res;
						}
					}

					break;

				case '7' :
					$banner_query = $this->_dbo->_query_print('SELECT tpl_opt FROM '.GD_MOBILE_DESIGN.' WHERE mdesign_no=[s]', $req_arr['mdesign_no']);
					$banner_res = $this->_dbo->_select($banner_query);
					$banner_res = $banner_res[0];

					$json = new Services_JSON(16);
					$banner_info = $json->decode($banner_res['tpl_opt']);

					$res_display = array();

					if(is_array($banner_info['banner_img']) && !empty($banner_info['banner_img'])) {

						foreach ($banner_info['banner_img'] as $key => $val) {

							$tmp_query = '
								SELECT
									md.temp1
								FROM
									'.GD_MOBILE_DISPLAY.' md
								WHERE
									md.mdesign_no=[s] AND md.display_type=[s] AND md.banner_no=[s]
								ORDER BY
									md.sort ASC
								';

							$display_query = $this->_dbo->_query_print($tmp_query, $req_arr['mdesign_no'], $req_arr['display_type'], $key);
							$tmp_display = $this->_dbo->_select($display_query);
							$tmp_display = $tmp_display[0];

							$tmp_res = array();

							if($cfg['rootDir']) {
								$img_path = $cfg['rootDir'].'/data/m/upload_img/';
							}
							else {
								$img_path = '/shop/data/m/upload_img/';
							}

							$tmp_res['banner_img'] = $img_path.$val;

							if(strstr($tmp_display, 'http')) {
								$tmp_res['link_url'] = $tmp_display['temp1'];
							}
							else {
								$tmp_res['link_url'] = 'http://'.$tmp_display['temp1'];
							}
							if(is_file($_SERVER['DOCUMENT_ROOT'] . $tmp_res['banner_img'])) {
								$res_display[] = $tmp_res;
							}
						}
					}
					break;

			}
		} else {
			list($add_table, $add_where, $add_order) = $mainAutoSort->getSortTerms($req_arr['mobile_categoods'], $req_arr['price'], $req_arr['stock_type'], $req_arr['stock_amount'], $req_arr['regdt'], $sortNum);
 
			$tmp_query = "
				SELECT
					".$mainAutoSort->use_table.".goodsno, gd_goods.goodsnm, gd_goods.img_i, gd_goods.img_s, gd_goods.img_m, gd_goods.img_l, gd_goods.use_mobile_img, gd_goods.img_w, gd_goods.img_pc_w, gd_goods.strprice, gd_goods_option.price, gd_goods_option.consumer, gd_goods.runout, gd_goods.usestock, gd_goods.totstock, gd_goods.use_only_adult, gd_goods.speach_description_useyn, gd_goods.speach_description, gd_goods.use_goods_discount $_add_field
				FROM
					".$mainAutoSort->use_table."
					{$add_table}
				WHERE
					gd_goods.open_mobile
					$where
					{$add_where}
				GROUP BY ".$mainAutoSort->use_table.".goodsno $orderby
				LIMIT ".$mainAutoSort->sort_limit."
				";
			
			$display_query = $this->_dbo->_query_print($tmp_query, $req_arr['mdesign_no'], $req_arr['display_type']);
			$tmp_display = $this->_dbo->_select($display_query);

			//DB Cache 사용 141030
			$dbCache = Core::loader('dbcache')->setLocation('mobile_display');

			if (!$res_display = $dbCache->getCache($display_query)) {
				$res_display = array();

				if(is_array($tmp_display) && !empty($tmp_display)) {
					foreach($tmp_display as $row_display) {
						$tmp_arr = array();

						$tmp_arr['goodsno'] = $row_display['goodsno'];
						$tmp_arr['goodsnm'] = strip_tags($row_display['goodsnm']);
						$tmp_arr['goods_img'] = $this->getMobileMainImg($cfg, $row_display);

						if (strlen(trim($row_display['strprice'])) > 0) {
							$tmp_arr['goods_price'] = $row_display['strprice'];
						}
						else {
							$tmp_arr['goods_price'] = number_format($row_display['price']).' 원';
						}

						// 소비자가
						if ($row_display['consumer'] > 0) {
							$tmp_arr['consumer'] = $row_display['consumer'];
						}
						else if ($row_display['consumer'] == '' || $row_display['consumer'] == 0) {
							$tmp_arr['consumer'] = '';
						}

						if ($row_display['speach_description_useyn'] === 'y' && strlen($row_display['speach_description']) > 0) {
							$tmp_arr['tts_url'] = Core::loader('TextToSpeach')->getURL($row_display['speach_description']);
						}
						else {
							$tmp_arr['tts_url'] = '';
						}

						// 상품할인
						if($row_display['use_goods_discount']){
							$tmp_arr['special_discount'] = $goodsDiscountModel->getDiscountUnit($row_display, Clib_Application::session()->getMemberLevel());
						}

						// 상품할인 가격 표시
						if ($displayCfg['displayType'] === 'discount') {
							$goodsDiscount = '';
							if ($row_display['use_goods_discount'] === '1') {
								$goodsDiscount = $goodsDiscountModel->getDiscountAmountSearch($row_display);
							}
							if ($goodsDiscount) {
								$tmp_arr['oriPrice'] = $row_display['price'];
								$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price'] - $goodsDiscount).' 원';
							}
							else {
								$tmp_arr['oriPrice'] = '0';
								$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price']).' 원';
							}
						}

						// 품절상품
						if ( $row_display['runout'] > 0 || $row_display['usestock'] && $row_display['totstock'] < 1) {
							if($cfg_soldout['mobile_display'] === 'overlay')
								$tmp_arr['css_selector'] = 'class="el-goods-soldout-image"';
						}

						// 즉석할인쿠폰 유효성 검사 (pc or mobile)
						list($tmp_arr['coupon'], $tmp_arr['coupon_emoney']) = getCouponInfoMobile($row_display['goodsno'], $row_display['price']);

						// 쿠폰할인여부
						if($tmp_arr['coupon'] > 0 || $tmp_arr['coupon_emoney'] > 0){
							$tmp_arr['coupon_discount'] = true;
						}

						$res_display[] = $tmp_arr;
					}
					if ($dbCache) { $dbCache->setCache($display_query, $res_display); }
				}
			}
		}

		if ($this->isPCDisplay()) {
			$res_display = $this->getPCMainDisplayGoods($req_arr['mdesign_no']);
		}

		return $res_display;
	}

	function getMobileEventDisplayGoods($mevent_no) {
		@include dirname(__FILE__). "/../../shop/conf/config.soldout.php";
		include dirname(__FILE__). "/../../shop/lib/json.class.php";
		include dirname(__FILE__). "/../../shop/conf/config.display.php";

		$config = Core::loader('config');
		$cfg = $config->load('config');

		if (is_file(dirname(__FILE__). "/../../shop/conf/config.soldout.php"))
			include dirname(__FILE__). "/../../shop/conf/config.soldout.php";

		$json = new Services_JSON(16);
		$goodsDiscountModel = Clib_Application::getModelClass('goods_discount');

		$design_query = $this->_dbo->_query_print('SELECT tpl, tpl_opt FROM '.GD_MOBILE_EVENT.' WHERE mevent_no=[s]', $mevent_no);
		$res_design = $this->_dbo->_select($design_query);

		//정렬
		$orderby = "order by md.sort ASC";

		// 품절 상품 제외
		if ($cfg_soldout['exclude_event']) {
			$where = " AND !( g.runout = 1 OR (g.usestock = 'o' AND g.usestock IS NOT NULL AND g.totstock < 1) ) ";
		}
		// 제외시키지 않는다면, 맨 뒤로 보낼지를 결정
		else if ($cfg_soldout['back_event']) {
			$orderby = "order by `soldout` ASC, md.sort";
			$_add_field = ",IF (g.runout = 1 , 1, IF (g.usestock = 'o' AND g.totstock = 0, 1, 0)) as `soldout`";
		}

		switch ( $res_design[0]['tpl']) {
			case "tpl_05":
				$tab_info = $json->decode($res_design[0]['tpl_opt']);

				if(is_array($tab_info['tab_name']) && !empty($tab_info['tab_name'])) {
					foreach ($tab_info['tab_name'] as $key => $val) {
						$tmp_query = "
							SELECT
								md.goodsno, g.goodsnm, g.img_s, g.use_mobile_img, g.img_x, g.img_pc_x, go.price, g.use_goods_discount, g.speach_description_useyn, g.speach_description, g.runout, g.usestock, g.totstock $_add_field
							FROM
								".GD_MOBILE_DISPLAY." md
								LEFT JOIN ".GD_GOODS." g ON md.goodsno = g.goodsno
								LEFT JOIN ".GD_GOODS_OPTION." go ON md.goodsno = go.goodsno AND link and go_is_deleted <> '1' and go_is_display = '1'
							WHERE
								md.mevent_no=[s] AND md.tab_no=[s]
								$where
							$orderby
							";

						$display_query = $this->_dbo->_query_print($tmp_query, $mevent_no, $key);
						$tmp_display = $this->_dbo->_select($display_query);

						$tmp_res = array();
						if(is_array($tmp_display) && !empty($tmp_display)) {
							foreach($tmp_display as $row_display) {
								$tmp_arr = array();

								$tmp_arr['goodsno'] = $row_display['goodsno'];
								$tmp_arr['goodsnm'] = $row_display['goodsnm'];
								$tmp_arr['goods_img'] = $this->getMobileMainImg($cfg, $row_display, false);

								if ($row_display['speach_description_useyn'] === 'y' && strlen($row_display['speach_description']) > 0) {
									$tmp_arr['tts_url'] = Core::loader('TextToSpeach')->getURL($row_display['speach_description']);
								}
								else {
									$tmp_arr['tts_url'] = '';
								}

								// 상품할인	
								if($row_display['use_goods_discount']){
									$tmp_arr['special_discount'] = $goodsDiscountModel->getDiscountUnit($row_display, Clib_Application::session()->getMemberLevel());
								}

								// 상품할인 가격 표시
								if ($displayCfg['displayType'] === 'discount') {
									$goodsDiscount = '';
									if ($row_display['use_goods_discount'] === '1') {
										$goodsDiscount = $goodsDiscountModel->getDiscountAmountSearch($row_display);
									}
									if ($goodsDiscount) {
										$tmp_arr['oriPrice'] = $row_display['price'];
										$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price'] - $goodsDiscount).' 원';
									}
									else {
										$tmp_arr['oriPrice'] = '0';
										$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price']).' 원';
									}
								}

								// 품절상품
								if ( $row_display['runout'] > 0 || $row_display['usestock'] && $row_display['totstock'] < 1) {
									if($cfg_soldout['mobile_display'] === 'overlay')
										$tmp_arr['css_selector'] = 'class="el-goods-soldout-image"';
								}

								// 즉석할인쿠폰 유효성 검사 (pc or mobile)
								list($tmp_arr['coupon'], $tmp_arr['coupon_emoney']) = getCouponInfoMobile($row_display['goodsno'], $row_display['price']);

								// 쿠폰할인여부			
								if($tmp_arr['coupon'] > 0 || $tmp_arr['coupon_emoney'] > 0){
									$tmp_arr['coupon_discount'] = true;
								}

								$tmp_arr['goods_price'] = number_format($row_display['price']).' 원';
								$tmp_res[] = $tmp_arr;
							}
						}
						$res_display[] = $tmp_res;
					}
				}
				break;
			default:
				$tmp_query = "
					SELECT
						md.goodsno, g.goodsnm, g.img_s, g.use_mobile_img, g.img_x, g.img_pc_x, go.price, g.use_goods_discount, g.speach_description_useyn, g.speach_description, g.runout, g.usestock, g.totstock $_add_field
					FROM
						".GD_MOBILE_DISPLAY." md
						LEFT JOIN ".GD_GOODS." g ON md.goodsno = g.goodsno
						LEFT JOIN ".GD_GOODS_OPTION." go ON md.goodsno = go.goodsno AND link and go_is_deleted <> '1' and go_is_display = '1'
					WHERE
						md.mevent_no=[s]
						$where
					$orderby
					";

				$display_query = $this->_dbo->_query_print($tmp_query, $mevent_no);
				$tmp_display = $this->_dbo->_select($display_query);

				$res_display = array();

				if(is_array($tmp_display) && !empty($tmp_display)) {
					foreach($tmp_display as $row_display) {
						$tmp_arr = array();

						$tmp_arr['goodsno'] = $row_display['goodsno'];
						$tmp_arr['goodsnm'] = $row_display['goodsnm'];
						$tmp_arr['goods_img'] = $this->getMobileMainImg($cfg, $row_display, false);

						if ($row_display['speach_description_useyn'] === 'y' && strlen($row_display['speach_description']) > 0) {
							$tmp_arr['tts_url'] = Core::loader('TextToSpeach')->getURL($row_display['speach_description']);
						}
						else {
							$tmp_arr['tts_url'] = '';
						}

						// 상품할인	
						if($row_display['use_goods_discount']){
							$tmp_arr['special_discount'] = $goodsDiscountModel->getDiscountUnit($row_display, Clib_Application::session()->getMemberLevel());
						}

						// 상품할인 가격 표시
						if ($displayCfg['displayType'] === 'discount') {
							$goodsDiscount = '';
							if ($row_display['use_goods_discount'] === '1') {
								$goodsDiscount = $goodsDiscountModel->getDiscountAmountSearch($row_display);
							}
							if ($goodsDiscount) {
								$tmp_arr['oriPrice'] = $row_display['price'];
								$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price'] - $goodsDiscount).' 원';
							}
							else {
								$tmp_arr['oriPrice'] = '0';
								$tmp_arr['goodsDiscountPrice'] = number_format($row_display['price']).' 원';
							}
						}

						// 품절상품
						if ( $row_display['runout'] > 0 || $row_display['usestock'] && $row_display['totstock'] < 1) {
							if($cfg_soldout['mobile_display'] === 'overlay')
								$tmp_arr['css_selector'] = 'class="el-goods-soldout-image"';
						}

						// 즉석할인쿠폰 유효성 검사 (pc or mobile)
						list($tmp_arr['coupon'], $tmp_arr['coupon_emoney']) = getCouponInfoMobile($row_display['goodsno'], $row_display['price']);

						// 쿠폰할인여부			
						if($tmp_arr['coupon'] > 0 || $tmp_arr['coupon_emoney'] > 0){
							$tmp_arr['coupon_discount'] = true;
						}

						$tmp_arr['goods_price'] = number_format($row_display['price']).' 원';
						$res_display[] = $tmp_arr;
					}
				}
				break;

		}

		return $res_display;
	}

	function getMobileCategoryDisplayGoods($req_arr) {
		if (is_file(dirname(__FILE__). "/../../shop/conf/config.soldout.php")) @include dirname(__FILE__). "/../../shop/conf/config.soldout.php";
		if (is_file(dirname(__FILE__). "/../../shop/conf/config.mobileShop.category.php")) @include dirname(__FILE__). "/../../shop/conf/config.mobileShop.category.php";
		else {
			$cfgMobileDispCategory['disp_goods_count'] = 10;
		}

		### 변수할당
		$category = $req_arr['category'];
		$kw = $req_arr['kw'];
		$kw_encode = iconv('utf-8', 'euc-kr', $req_arr['kw']);//$req_arr['kw'];
		$item_cnt = $req_arr['item_cnt'];
		$view_type = $req_arr['view_type'];
		$sort_type = $req_arr['sort_type'];

		if($view_type == 'gallery') {
			$number = $cfgMobileDispCategory['disp_goods_count'] - ($cfgMobileDispCategory['disp_goods_count'] % 3);
		}
		else {
			$number = $cfgMobileDispCategory['disp_goods_count'];
		}

		if(!$item_cnt) {
			$item_cnt = 0;
			$page = 1;
		}
		else {
			$page = ceil($item_cnt / $number) + 1;
		}

		$goodsHelper   = Clib_Application::getHelperClass('front_goods_mobile');

		if(!$kw) {
			$categoryModel = $goodsHelper->getCategoryModel($category);
			$lstcfg = $categoryModel->getConfig();
		}

		switch($sort_type) {
			case 'regdt' :
				$order_by = 'goods.regdt desc';
				break;
			case 'low_price' :
				$order_by = 'goods.goods_price asc';
				break;
			case 'high_price' :
				$order_by = 'goods.goods_price desc';
				break;
			case 'sort' :
			default:
				if($categoryModel instanceof Clib_Model_Category_Category) {
					$order_by = $categoryModel->getSortColumnName();
				}
				else {
					$order_by = 'goods.regdt desc';
				}
				break;
		}

		// 파라미터 설정
		if($kw) {
			$params = array(
				'page' => $page,
				'page_num' => $number,
				'keyword' => $kw,
				'sort' => $order_by,
				'item_cnt' => $item_cnt,
			);

			// GROUP BY 처리를 위해서 기존의 객체를 변경함
			$params['resetRelationShip'] = array(
				'categories' => array(
					'modelName' => 'goods_link',
					'isCollection' => true,
					'foreignColumn' => 'goodsno',
					'deleteCascade' => true,
					'withoutGroup' => false,
				),
			);
		}
		else {
			$params = array(
				'page' => $page,
				'page_num' => $number,
				'keyword' => $kw,
				'sort' => $order_by,
				'category' => $category,
				'item_cnt' => $item_cnt,
			);
		}

		// 상품 목록
		$goodsCollection = $goodsHelper->getGoodsCollection($params);

		// 검색어가 있는 경우 페이징 및 검색 총 갯수, 상품주소
		if ($kw) {
			$pg = $goodsCollection->getPaging();
			$ret_goods['pg'] = $pg;
			$ret_goods['total'] = $pg->recode['total'];
			$ret_goods['goods_src'] = '../goods/view.php?kw=' . $kw;// 상품상세주소 설정
		} else {
			$ret_goods['goods_src'] = '../goods/view.php?category=' . $category;// 상품상세주소 설정
		}

		$ret_goods['goods_data'] = $goodsHelper->getGoodsCollectionArray($goodsCollection, $categoryModel, true);

		return $ret_goods;
	}

	function getMobileBrandDisplayGoods($req_arr) {
		if (is_file(dirname(__FILE__). "/../../shop/conf/config.soldout.php")) @include dirname(__FILE__). "/../../shop/conf/config.soldout.php";
		if (is_file(dirname(__FILE__). "/../../shop/conf/config.mobileShop.category.php")) @include dirname(__FILE__). "/../../shop/conf/config.mobileShop.category.php";
		else {
			$cfgMobileDispCategory['disp_goods_count'] = 10;
		}

		### 변수할당
		$brand = $req_arr['brand'];
		$item_cnt = $req_arr['item_cnt'];
		$view_type = $req_arr['view_type'];
		$sort_type = $req_arr['sort_type'];

		if($view_type == 'gallery') {
			$number = $cfgMobileDispCategory['disp_goods_count'] - ($cfgMobileDispCategory['disp_goods_count'] % 3);
		}
		else {
			$number = $cfgMobileDispCategory['disp_goods_count'];
		}

		if(!$item_cnt) {
			$item_cnt = 0;
			$page = 1;
		}
		else {
			$page = ceil($item_cnt / $number) + 1;
		}

		$goodsHelper   = Clib_Application::getHelperClass('front_goods_mobile');

		$brandModel = $goodsHelper->getBrandModel($brand);
		$lstcfg = $brandModel->getConfig();

		switch($sort_type) {
			case 'regdt' :
				$order_by = 'goods.regdt desc';
				break;
			case 'low_price' :
				$order_by = 'goods.goods_price asc';
				break;
			case 'high_price' :
				$order_by = 'goods.goods_price desc';
				break;
			case 'sort' :
			default:
				if($brandModel instanceof Clib_Model_Goods_Brand) {
					$order_by = $brandModel->getSortColumnName();
				}
				else {
					$order_by = 'goods.regdt desc';
				}
				break;
		}

		// 파라미터 설정
		$params = array(
			'page' => $page,
			'page_num' => $number,
			'keyword' => '',
			'sort' => $order_by,
			'brandno' => $brandModel->getId(),
		);

		// GROUP BY 처리를 위해서 기존의 객체를 변경함
		$params['resetRelationShip'] = array(
			'categories' => array(
				'modelName' => 'goods_link',
				'isCollection' => true,
				'foreignColumn' => 'goodsno',
				'deleteCascade' => true,
				'withoutGroup' => false,
			),
		);

		// 상품 목록
		$goodsCollection = $goodsHelper->getGoodsCollection($params);
		$pg = $goodsCollection->getPaging();
		$ret_goods['pg'] = $pg;
		$ret_goods['total'] = $pg->recode['total'];

		// 검색어가 있는 경우 페이징 및 검색 총 갯수, 상품주소
		$ret_goods['goods_src'] = '../goods/view.php?brand=' . $brand;// 상품상세주소 설정
		$ret_goods['goods_data'] = $goodsHelper->getGoodsCollectionArray($goodsCollection, $brandModel);

		return $ret_goods;
	}
}

?>
